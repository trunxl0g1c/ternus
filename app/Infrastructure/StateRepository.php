<?php
declare(strict_types=1);
namespace Ternus\Infrastructure;
use PDO;
use PDOException;
use RuntimeException;
use Throwable;

/** Persistence boundary. Domain services never execute SQL. */
final class StateRepository
{
    private ?PDO $connection = null;

    public function connection(bool $create = false): PDO
    {
        if ($this->connection) {
            return $this->connection;
        }
        $config = require dirname(__DIR__, 2) . '/server/config.php';
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $config['database'])) {
            throw new RuntimeException('Nama database tidak valid.');
        }
        $dsn = 'mysql:host=' . $config['host'] . ';port=' . $config['port'] . ';charset=utf8mb4';
        if (!$create) {
            $dsn .= ';dbname=' . $config['database'];
        }
        $db = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        if ($create) {
            $db->exec(
                'CREATE DATABASE IF NOT EXISTS `' .
                    $config['database'] .
                    '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
            );
            $db->exec('USE `' . $config['database'] . '`');
        }
        return $this->connection = $db;
    }

    public function installed(): bool
    {
        try {
            return (bool) $this->connection()
                ->query('SELECT payload FROM app_state WHERE id=1')
                ->fetchColumn();
        } catch (PDOException $error) {
            return false;
        }
    }

    public function install(array $state): void
    {
        $db = $this->connection(true);
        $db->exec(
            'CREATE TABLE IF NOT EXISTS app_state (id INT PRIMARY KEY, payload LONGTEXT NOT NULL, version INT NOT NULL DEFAULT 1, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB',
        );
        try {
            $db->prepare('INSERT INTO app_state (id,payload) VALUES (1,?)')->execute([
                json_encode($state, JSON_THROW_ON_ERROR),
            ]);
        } catch (PDOException $error) {
            if ($error->getCode() === '23000') {
                throw new \DomainException('Aplikasi sudah terpasang. Silakan login.');
            }
            throw $error;
        }
    }

    public function read(bool $lock = false): array
    {
        $payload = $this->connection()
            ->query('SELECT payload FROM app_state WHERE id=1' . ($lock ? ' FOR UPDATE' : ''))
            ->fetchColumn();
        if (!$payload) {
            throw new RuntimeException('Aplikasi belum terpasang.');
        }
        return json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
    }

    public function save(array $state): void
    {
        $this->connection()
            ->prepare('UPDATE app_state SET payload=?,version=version+1 WHERE id=1')
            ->execute([json_encode($state, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)]);
    }

    /** The entire command is atomic, including ledger, audit and idempotency key. */
    public function transaction(callable $callback): array
    {
        $db = $this->connection();
        $db->beginTransaction();
        try {
            $state = $this->read(true);
            $result = $callback($state);
            $this->save($state);
            $db->commit();
            return $result;
        } catch (Throwable $error) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $error;
        }
    }
}

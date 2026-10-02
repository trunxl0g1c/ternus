<?php
declare(strict_types=1);
namespace Ternus\Http\Controllers;
use Ternus\Infrastructure\StateRepository;
use Ternus\Http\SessionGuard;
final class CommandController
{
    public function __construct(private StateRepository $repository) {}
    public function execute(array $input): array
    {
        return $this->repository->transaction(function (array &$state) use ($input): array {
            $user = SessionGuard::user($state);
            $key = clean($input['key'] ?? '', 100);
            $operation = $input['op'] ?? '';
            $data = $input['data'] ?? [];
            need(is_array($data), 'Data perintah harus berupa object.');
            $hash = hash('sha256', json_encode([$operation, $data]));
            if (isset($state['requests'][$key])) {
                $previous = $state['requests'][$key];
                need(
                    $previous['hash'] === $hash && $previous['user'] === $user['id'],
                    'Kunci permintaan tidak cocok.',
                );
                return ['ok' => true, 'result' => $previous['result']];
            }
            $result = operate($state, $user, $operation, $data);
            $state['requests'][$key] = [
                'hash' => $hash,
                'user' => $user['id'],
                'result' => $result,
            ];
            $state['audit'][] = [
                'id' => uid(),
                'time' => date(DATE_ATOM),
                'user' => $user['name'],
                'op' => $operation,
                'document' => $result['id'] ?? '',
                'summary' => $result['message'] ?? '',
            ];
            return ['ok' => true, 'result' => $result];
        });
    }
}

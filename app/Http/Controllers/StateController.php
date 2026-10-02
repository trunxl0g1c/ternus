<?php
declare(strict_types=1);
namespace Ternus\Http\Controllers;
use Ternus\Infrastructure\StateRepository;
use Ternus\Http\SessionGuard;
final class StateController
{
    public function __construct(private StateRepository $repository) {}
    public function show(array $input): array
    {
        $state = $this->repository->read();
        $user = SessionGuard::user($state);
        return [
            'state' => viewState($state, $user),
            'user' => array_diff_key($user, ['password' => true]),
            'csrf' => SessionGuard::token(),
        ];
    }
    public function backup(array $input): array
    {
        $state = $this->repository->read();
        permit(SessionGuard::user($state), ['owner']);
        unset($state['login_attempts']);
        return ['exported_at' => date(DATE_ATOM), 'format' => 'ternus-backup-1', 'state' => $state];
    }
}

<?php

declare(strict_types=1);

namespace Shart\Helpers;

use Shart\Models\Notification;
use Shart\Models\Role;
use Shart\Models\User;

class Notifier
{
    /**
     * @var array
     */
    private $users = [];
private $notification;
    public function __construct(Notification $notification) {
        $this->notification = $notification;
    }

    public function attach(User $user)
    {
        $this->users[] = $user->getKey();
    }

    public function send()
    {
        if ($this->users) {
            $this->notification->users()->syncWithoutDetaching($this->users);
        }
    }
}

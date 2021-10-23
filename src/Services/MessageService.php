<?php

declare(strict_types=1);

namespace Shart\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Shart\Helpers\Notifier;
use Shart\Models\Message;
use Shart\Models\Notification;

class MessageService
{
    /**
     * @var mixed
     */
    protected $auth;

    /**
     * @param Authenticatable $auth
     */
    public function __construct(? Authenticatable $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param string $sender
     * @param string $reference
     * @param string $id_object
     *
     * @return mixed
     */
    public function newMessage(
        string $subject,
        string $body,
        string $sender = null,
        string $reference = null,
        string $id_object = null
    ): Message {
        $message = new Message(compact('subject', 'body'));
        $message->sender = $sender ?: ($this->auth ? $this->auth->fullname : null);
        $message->save();

        return $message;
    }

    public function newNotification(
        Message $message,
        string $action = 'PREVIEW'
    ): Notification {
        $notification = new Notification();
        $notification->id_message = $message->getKey();
        $notification->action = Notification::VERIFICATION === $action ? $action : Notification::PREVIEW;
        $notification->status = 1;
        $notification->save();

        return $notification;
    }
}

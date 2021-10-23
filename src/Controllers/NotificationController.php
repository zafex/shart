<?php

declare(strict_types=1);

namespace Shart\Controllers;

use Illuminate\Http\Request;
use Shart\BaseController;
use Shart\IndexAction;
use Shart\Models\Notification;

class NotificationController extends BaseController
{
    use IndexAction;

    /**
     * @var string
     */
    protected $indexName = 'notification';

    /**
     * @return mixed
     */
    public function detail(string $id, Request $request)
    {
        if ($notification = Notification::with('message.target')->where('id', $id)->first()) {
            if (0 == $notification->status) {
                $notification->status = 1;
                $notification->save();
            }

            return $notification;
        }
    }
}

<?php

namespace Modules\Base\Traits;

use Illuminate\Notifications\RoutesNotifications;

trait SlackNotifiable
{
    use RoutesNotifications;

    public function routeNotificationForSlack()
    {
        return config('base.slackWebhook');
    }
}

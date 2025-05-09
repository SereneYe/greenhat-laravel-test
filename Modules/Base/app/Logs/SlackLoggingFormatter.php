<?php

namespace Modules\Base\Logs;

use Illuminate\Log\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\SlackWebhookHandler;

class SlackLoggingFormatter
{
    public function __invoke(Logger $logger): void
    {
        foreach ($logger->getHandlers() as $handler) {
            if ($handler instanceof SlackWebhookHandler) {
                $output = "%datetime% > %level_name% - %message% `%context% %extra%` :poop: \n";

                $formatter = new LineFormatter($output, 'Y-m-d H:i:s');

                $handler->setFormatter($formatter);

                $handler->pushProcessor(function ($record) {
                    $record['extra'] = [
                        'environment' => config('app.env'),
                        'url' => config('app.url'),
                        'ip_address' => request()->ip(),
                    ];

                    return $record;
                });
            }
        }
    }
}

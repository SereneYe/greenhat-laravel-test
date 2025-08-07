<?php

namespace App\Contracts;

interface MailServiceInterface
{
    /**
     * Send email notification
     */
    public function send($recipient, $template, array $data = []): bool;

    /**
     * Queue email notification
     */
    public function queue($recipient, $template, array $data = []): bool;
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class NotificationService
{
    protected array $keywords = ['post', 'comment'];

    public function checkAndNotify(string $type, array $data): void
    {
        if ($type === 'post') {
            $content = isset($data['caption']) ? $data['caption'] : '';
        } else {
            $content = isset($data['text']) ? $data['text'] : '';
        }

        foreach ($this->keywords as $keyword) {
            Log::info("hi");
            if (stripos($content, $keyword) !== false) {

                $id = isset($data['id']) ? $data['id'] : '[unknown ID]';
                $message = "Notification: new $type matches keyword '$keyword'. ID: $id";

                Log::info($message);
                echo $message . "\n";
                break;
            }
        }
    }
}

<?php

namespace App\Traits;

use Carbon\Carbon;

trait TimestampTrait
{
    public function applyTimestamps(array &$data): void
    {
        $now = Carbon::now()->toISOString();
        $data['created_at'] = $now;
        $data['updated_at'] = $now;
    }
}

<?php

namespace App\Traits;

use App\Models\ActionLog;
use Illuminate\Support\Facades\Auth;

trait LogsActions
{
    /**
     * Log a user action manually
     */
    protected function logAction(string $action, string $description, $model = null, array $metadata = []): void
    {
        if (!Auth::check()) {
            return;
        }

        try {
            ActionLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'description' => $description,
                'model_type' => $model ? get_class($model) : null,
                'model_id' => $model ? $model->id : null,
                'metadata' => $metadata,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to log user action: ' . $e->getMessage());
        }
    }
}

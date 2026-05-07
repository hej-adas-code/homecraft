<?php

namespace App\Observers;

use App\Models\TimelineEntry;

class TimelineObserver
{
    protected string $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function created($model): void
    {
        if (!isset($model->user_id)) {
            return;
        }

        TimelineEntry::create([
            'user_id'        => $model->user_id,
            'type'           => $this->type,
            'entry_title'    => $model->name ?? $model->title ?? $model->subject ?? class_basename($model) . ' #' . $model->id,
            'entry_date'     => $model->created_at,
            'entryable_type' => get_class($model),
            'entryable_id'   => $model->id,
        ]);
    }
}

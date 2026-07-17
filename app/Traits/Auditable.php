<?php

namespace App\Traits;

use App\Models\AuditLog;

trait Auditable
{
    protected static function bootAuditable(): void
    {
        static::created(function ($model) {
            AuditLog::logAction(
                'created',
                get_class($model),
                $model->id,
                null,
                $model->getAttributes()
            );
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();
            if (empty($changes)) {
                return; // No changes
            }

            AuditLog::logAction(
                'updated',
                get_class($model),
                $model->id,
                $model->getOriginal(),
                $changes
            );
        });

        static::deleted(function ($model) {
            AuditLog::logAction(
                'deleted',
                get_class($model),
                $model->id,
                $model->getAttributes(),
                null
            );
        });
    }
}

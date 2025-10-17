<?php

namespace App\Traits;

use App\Models\auditlog\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    /**
     * Boot the auditable trait for a model.
     */
    public static function bootAuditable()
    {
        static::created(function ($model) {
            $model->auditCreated();
        });

        static::updated(function ($model) {
            $model->auditUpdated();
        });

        static::deleted(function ($model) {
            $model->auditDeleted();
        });
    }

    /**
     * Audit model creation
     */
    protected function auditCreated()
    {
        $this->createAuditLog('created', null, $this->getAuditableAttributes());
    }

    /**
     * Audit model update
     */
    protected function auditUpdated()
    {
        $changes = $this->getChanges();

        if (empty($changes)) {
            return;
        }

        $original = [];
        foreach (array_keys($changes) as $key) {
            $original[$key] = $this->getOriginal($key);
        }

        $this->createAuditLog('updated', $original, $changes);
    }

    /**
     * Audit model deletion
     */
    protected function auditDeleted()
    {
        $this->createAuditLog('deleted', $this->getAuditableAttributes(), null);
    }

    /**
     * Create audit log entry
     */
    protected function createAuditLog(string $action, ?array $oldValues, ?array $newValues)
    {
        // Remover campos sensíveis do log
        $sensitiveFields = ['password', 'remember_token'];

        if ($oldValues) {
            $oldValues = array_diff_key($oldValues, array_flip($sensitiveFields));
        }

        if ($newValues) {
            $newValues = array_diff_key($newValues, array_flip($sensitiveFields));
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'auditable_type' => get_class($this),
            'auditable_id' => $this->id,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'description' => $this->getAuditDescription($action),
            'ip_address' => Request::ip(),
            'user_agent' => Request::header('User-Agent'),
        ]);
    }

    /**
     * Get auditable attributes
     */
    protected function getAuditableAttributes(): array
    {
        return $this->getAttributes();
    }

    /**
     * Get audit description
     */
    protected function getAuditDescription(string $action): string
    {
        $modelName = class_basename($this);
        $identifier = $this->name ?? $this->id;

        return match($action) {
            'created' => "{$modelName} '{$identifier}' foi criado(a)",
            'updated' => "{$modelName} '{$identifier}' foi atualizado(a)",
            'deleted' => "{$modelName} '{$identifier}' foi excluído(a)",
            default => "{$modelName} '{$identifier}' - {$action}",
        };
    }
}


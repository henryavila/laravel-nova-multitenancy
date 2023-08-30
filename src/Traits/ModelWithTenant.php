<?php

namespace HenryAvila\LaravelNovaMultitenancy\Traits;

use HenryAvila\LaravelNovaMultitenancy\Observers\ModelWithTenantObserver;
use HenryAvila\LaravelNovaMultitenancy\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Trait ModelWithTenant
 *
 * Used to manage the Tenant Specific content
 *
 * @property int tenant_id
 */
trait ModelWithTenant
{
    /**
     * Name of all columns from this model that has the size of the file
     */
    protected static array $fileSizeColumns = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setConnection(config('database.tenant_connection'));
    }

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new TenantScope());
        static::observe(new ModelWithTenantObserver());
    }

    protected static function booted(): void
    {
        parent::booted();

        static::created(function (Model $file) {
            $file->load('tenant');
            foreach (static::$fileSizeColumns as $column) {
                $file->tenant->updateDiskUsage($file->$column);
            }
        });

        static::updating(function (Model $file) {
            foreach (static::$fileSizeColumns as $column) {
                if ($file->isDirty($column)) {

                    $file->load('tenant');
                    $totalSize = $file->$column - $file->getOriginal($column);
                    $file->tenant->updateDiskUsage($totalSize);
                }
            }
        });

        if (in_array(SoftDeletes::class, class_uses(static::class))) {
            static::forceDeleted(function (Model $file) {
                foreach (static::$fileSizeColumns as $column) {
                    $originalSize = $file->getOriginal($column);
                    if ($originalSize) {
                        $file->load('tenant');
                        $file->tenant->updateDiskUsage(-$originalSize);
                    }
                }
            });
        } else {
            static::deleted(function (Model $file) {
                foreach (static::$fileSizeColumns as $column) {
                    $originalSize = $file->getOriginal($column);
                    if ($originalSize) {
                        $file->load('tenant');
                        $file->tenant->updateDiskUsage(-$originalSize);
                    }
                }
            });
        }
    }

    public function tenant()
    {
        return $this->belongsTo(config('nova-multitenancy.tenant_model'));
    }
}

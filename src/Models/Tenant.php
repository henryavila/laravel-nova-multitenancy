<?php

namespace HenryAvila\LaravelNovaMultitenancy\Models;

use DateTimeZone;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tenant extends \Spatie\Multitenancy\Models\Tenant
{
    const TENANT_SELECTOR_SESSION_ID = 'tenant-id-selector';

    protected $casts = [
        'disk_quota_in_gigabytes' => 'float',
        'disk_usage_in_bytes' => 'int',
    ];

	protected static function booted(): void
	{
		parent::booted();

		static::updating(function ($tenant) {
			if ($tenant->disk_usage_in_bytes < 0) {
				$tenant->disk_usage_in_bytes = null;
			}
		});

	}
    /**
     * Is there any Tenant selected
     */
    public static function hasSelected(): bool
    {
        return Tenant::checkCurrent();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('primary');
    }

    public function updateDiskUsage(int $bytes): void
    {
        $this->disk_usage_in_bytes += $bytes;
        $this->save();
    }

    public static function getAllTimezoneList(): array
    {
        $list = DateTimeZone::listIdentifiers();

        return array_combine($list, $list);
    }

    public function diskQuotaInBytes(): Attribute
    {
        return new Attribute(
            get: fn () => empty($this->disk_quota_in_gigabytes) ? null : round($this->disk_quota_in_gigabytes * 1024 * 1024 * 1024, 0),
            set: fn () => empty($this->disk_quota_in_gigabytes) ? null : $this->disk_quota_in_gigabytes / 1024 / 1024 / 1024,
        );
    }

    public function diskSpaceAvailable(): Attribute
    {
        return new Attribute(
            get: function () {
                if (! $this->hasLimitedDiskQuota()) {
                    return null;
                }

                return $this->disk_quota_in_bytes - $this->disk_usage_in_bytes;
            }
        );
    }

    public function hasLimitedDiskQuota(): bool
    {
        return $this->disk_quota_in_bytes !== null;
    }

    public function canUploadNewFiles(): Attribute
    {
        return new Attribute(
            get: function () {
                if (! $this->hasLimitedDiskQuota()) {
                    return true;
                }

                return $this->disk_usage_in_bytes < $this->disk_quota_in_bytes;
            }
        );
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder; // لاستخدام الـ scope
use Carbon\Carbon; // لإدارة التواريخ (إذا لزم الأمر للدوال)

class EquipmentTracking extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'equipment_tracking'; // تأكد من اسم الجدول

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'equipment_id',
        'latitude',
        'longitude',
        'speed',
        'battery_level',
        'status',
        'start_time',
        'end_time',
        'duration',
        // 'timestamp' is covered by created_at
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'latitude' => 'decimal:7', // 10, 7
        'longitude' => 'decimal:7', // 10, 7
        'speed' => 'decimal:2',     // 6, 2
        'battery_level' => 'decimal:2', // 5, 2
        'status' => 'string', // Enum in DB, treated as string
        'start_time' => 'datetime',
        'end_time' => 'datetime',  
        'duration' => 'decimal:2',
        // 'timestamp' is implicitly covered by 'created_at'
    ];

    // Accessors and Methods (من الـ classDiagram)

    /**
     * Determine if the tracking device is currently online.
     * +isOnline() bool
     */
    public function isOnline(): bool
    {
        return $this->status === 'online' || $this->status === 'moving' || $this->status === 'idle';
    }

    /**
     * Determine if the battery level is low (e.g., below 20%).
     * +isLowBattery() bool
     */
    public function isLowBattery(): bool
    {
        return $this->battery_level !== null && $this->battery_level <= 20;
    }

    /**
     * Calculate the distance from the last known location. (Requires previous entry context)
     * +getDistanceFromLastLocation() decimal
     *
     * This method would require fetching the *previous* tracking record.
     * For a single model instance, it might not be directly applicable without context.
     * We will implement it by trying to fetch the immediately preceding record.
     *
     * Note: This is a placeholder for Haversine or similar calculation.
     */
    public function getDistanceFromLastLocation(): float
    {
        $previousLocation = $this->equipment->trackingRecords()
            ->where('id', '<', $this->id)
            ->latest('created_at')
            ->first();

        if (!$previousLocation) {
            return 0.0; // No previous location to compare
        }

        // Using a simple approximation of Haversine for demonstration.
        // For accurate distance, use a dedicated geospatial library or database functions.
        $earthRadius = 6371; // km
        $lat1 = deg2rad($previousLocation->latitude);
        $lon1 = deg2rad($previousLocation->longitude);
        $lat2 = deg2rad($this->latitude);
        $lon2 = deg2rad($this->longitude);

        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos($lat1) * cos($lat2) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Distance in kilometers
    }

    // Scopes

    /**
     * Scope a query to only include recent tracking records (e.g., last 24 hours).
     * +scopeRecent() QueryBuilder
     */
    public function scopeRecent(Builder $query): void
    {
        $query->where('created_at', '>=', Carbon::now()->subDay());
    }

    /**
     * Scope a query to only include tracking records for a specific equipment.
     * +scopeByEquipment(equipment_id) QueryBuilder
     */
    public function scopeByEquipment(Builder $query, int $equipmentId): void
    {
        $query->where('equipment_id', $equipmentId);
    }

    // Relationships

    /**
     * Get the equipment that this tracking record belongs to.
     * Equipment "1" --> "0..*" EquipmentTracking
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }
}

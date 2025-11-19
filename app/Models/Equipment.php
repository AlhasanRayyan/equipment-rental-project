<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder; // لاستخدام الـ scope
use Illuminate\Database\Eloquent\Casts\Attribute; // لتحديد Accessor و Mutator بشكل حديث
use Illuminate\Database\Eloquent\SoftDeletes;
class Equipment extends Model
{
    use HasFactory,  SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'equipment'; // تأكد من اسم الجدول

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'owner_id',
        'category_id',
        'name',
        'description',
        'daily_rate',
        'weekly_rate',
        'monthly_rate',
        'deposit_amount',
        'location_latitude',
        'location_longitude',
        'location_address',
        'status',
        'is_approved_by_admin',
        'has_gps_tracker',
        'average_rating',
        'total_reviews',
        'last_maintenance_date',
        'maintenance_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'daily_rate' => 'decimal:2',
        'weekly_rate' => 'decimal:2',
        'monthly_rate' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'location_latitude' => 'decimal:7', // 10, 7
        'location_longitude' => 'decimal:7', // 10, 7
        'is_approved_by_admin' => 'boolean',
        'has_gps_tracker' => 'boolean',
        'average_rating' => 'decimal:2',
        'total_reviews' => 'integer',
        'last_maintenance_date' => 'date',
        'status' => 'string', // Enum in DB, treated as string in application
    ];

    // Accessors (Methods from classDiagram)

    /**
     * Determine if the equipment is available for rent.
     * +isAvailable() bool
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    /**
     * Determine if the equipment is approved by an admin.
     * +isApproved() bool
     */
    public function isApproved(): bool
    {
        return $this->is_approved_by_admin;
    }

    /**
     * Get the rental rate by type (daily, weekly, monthly).
     * +getRateByType(type) decimal
     */
    public function getRateByType(string $type): ?float
    {
        return match ($type) {
            'daily' => $this->daily_rate,
            'weekly' => $this->weekly_rate,
            'monthly' => $this->monthly_rate,
            default => null,
        };
    }

    /**
     * Calculate the total cost for a given duration and rate type.
     * +calculateTotalCost(days, type) decimal
     */
    public function calculateTotalCost(int $days, string $type): float
    {
        $rate = $this->getRateByType($type);

        if (is_null($rate)) {
            // Handle invalid rate type or throw an exception
            return 0.00;
        }

        return $rate * $days;
    }

    /**
     * Get the distance attribute (requires context, e.g., from a specific point).
     * This is a virtual attribute that needs external input (e.g., user's current lat/lng)
     * to be calculated. For now, it's just a placeholder.
     * +getDistanceAttribute() decimal
     *
     * Example usage: $equipment->withDistance(user_lat, user_lng)->get();
     * Or, can be added dynamically to collection after fetching.
     */
    protected function distance(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->attributes['distance'] ?? null, // Default to null if not loaded
        );
    }

    /**
     * Mark the equipment as rented.
     * +markAsRented() void
     */
    public function markAsRented(): void
    {
        $this->status = 'rented';
        $this->save();
    }

    /**
     * Mark the equipment as available.
     * +markAsAvailable() void
     */
    public function markAsAvailable(): void
    {
        $this->status = 'available';
        $this->save();
    }


    // Scopes

    /**
     * Scope a query to only include available equipment.
     * +scopeAvailable() QueryBuilder
     */
    public function scopeAvailable(Builder $query): void
    {
        $query->where('status', 'available');
    }

    /**
     * Scope a query to only include approved equipment.
     * +scopeApproved() QueryBuilder
     */
    public function scopeApproved(Builder $query): void
    {
        $query->where('is_approved_by_admin', true);
    }

    /**
     * Scope a query to include equipment within a certain radius of a location.
     * This requires a more complex geospatial query. For simplicity, this is a basic placeholder.
     * You might need PostGIS or more advanced calculations for production.
     * +scopeByLocation(lat, lng, radius) QueryBuilder
     */
    public function scopeByLocation(Builder $query, float $latitude, float $longitude, float $radiusKm): void
    {
        // Example for a simple spherical distance calculation (approximate)
        // This assumes your database supports Haversine formula or similar via raw SQL
        // For production, consider using a database with geospatial extensions (e.g., PostGIS for PostgreSQL)
        // or a dedicated package.
        $distanceSql = "(6371 * acos(cos(radians(?)) * cos(radians(location_latitude)) * cos(radians(location_longitude) - radians(?)) + sin(radians(?)) * sin(radians(location_latitude))))";

        $query->selectRaw("*, $distanceSql AS distance", [
            $latitude,
            $longitude,
            $latitude,
        ])
        ->whereNotNull('location_latitude')
        ->whereNotNull('location_longitude')
        ->having('distance', '<', $radiusKm)
        ->orderBy('distance');
    }

    // Relationships

    /**
     * Get the user who owns this equipment.
     * User "1" --> "0..*" Equipment : owns
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the category that this equipment belongs to.
     * EquipmentCategory "1" --> "0..*" Equipment : categorizes
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(EquipmentCategory::class, 'category_id');
    }

    /**
     * Get the images for the equipment.
     * Equipment "1" --> "0..*" EquipmentImage
     */
    public function images(): HasMany
    {
        return $this->hasMany(EquipmentImage::class, 'equipment_id');
    }

    /**
     * Get the bookings for the equipment.
     * Equipment "1" --> "0..*" Booking
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'equipment_id');
    }

    /**
     * Get the reviews for the equipment.
     * Equipment "1" --> "0..*" Review
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'equipment_id');
    }

    /**
     * Get the tracking records for the equipment.
     * Equipment "1" --> "0..*" EquipmentTracking
     */
    public function trackingRecords(): HasMany
    {
        return $this->hasMany(EquipmentTracking::class, 'equipment_id');
    }

    /**
     * Get the user favorites for this equipment.
     * Equipment "1" --> "0..*" UserFavorite
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(UserFavorite::class, 'equipment_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder; // لاستخدام الـ scope

class EquipmentImage extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'equipment_images'; 

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'equipment_id',
        'image_url',
        'is_main',
        'display_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_main' => 'boolean',
        'display_order' => 'integer',
    ];

    // Accessors and Methods 

    /**
     * Set this image as the main image for its equipment.
     * +setAsMain() void
     */
    public function setAsMain(): void
    {
        // First, set all other images for this equipment as not main
        $this->equipment->images()->where('id', '!=', $this->id)->update(['is_main' => false]);

        // Then, set this image as main
        $this->is_main = true;
        $this->save();
    }

    // Scopes

    /**
     * Scope a query to only include the main image.
     * +scopeMain() QueryBuilder
     */
    public function scopeMain(Builder $query): void
    {
        $query->where('is_main', true);
    }

    /**
     * Scope a query to order images by display_order.
     * +scopeOrdered() QueryBuilder
     */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('display_order')->orderBy('id');
    }

    // Relationships

  
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }
}
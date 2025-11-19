<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class EquipmentCategory extends Model
{
    use HasFactory,  SoftDeletes;

    protected $table = 'equipment_categories';

    protected $fillable = [
        'category_name',
        'description',
        'image_url',
        'parent_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships


    public function parent(): BelongsTo
    {
        return $this->belongsTo(EquipmentCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(EquipmentCategory::class, 'parent_id');
    }

    /**
     * Get the equipment for the category.
     * EquipmentCategory "1" --> "0..*" Equipment : categorizes
     */
    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class, 'category_id');
    }

    // Accessors (Methods from classDiagram)

    /**
     * Get the total number of equipment associated with this category and its children.
     * +getTotalEquipmentAttribute() int
     */
    public function getTotalEquipmentAttribute(): int
    {
        // يحسب المعدات في هذه الفئة والفئات الفرعية لها
        $total = $this->equipment()->count();
        foreach ($this->children as $child) {
            $total += $child->getTotalEquipmentAttribute(); // استدعاء تكراري (recursive)
        }
        return $total;
    }

    // Scopes

    /**
     * Scope a query to only include active categories.
     * +scopeActive() QueryBuilder
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Scope a query to only include parent categories (those without a parent).
     */
    public function scopeParents(Builder $query): void
    {
        $query->whereNull('parent_id');
    }
}

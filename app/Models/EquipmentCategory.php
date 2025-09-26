<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class EquipmentCategory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'equipment_categories'; 

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_name',
        'description',
        'image_url',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Accessors (Methods from classDiagram)

 
    public function getTotalEquipmentAttribute(): int
    {
        return $this->equipment()->count();
    }

    // Scopes

  
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    // Relationships

  
    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class, 'category_id');
    }
}
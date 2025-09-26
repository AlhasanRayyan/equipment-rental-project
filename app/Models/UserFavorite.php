<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder; // لاستخدام الـ scope

class UserFavorite extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_favorites'; // تأكد من اسم الجدول

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'equipment_id',
        // 'added_date' is covered by created_at
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // 'added_date' is implicitly covered by 'created_at'
    ];

    // Accessors and Methods: UserFavorite does not have specific ones in classDiagram beyond scopes.

    // Scopes

    /**
     * Scope a query to only include favorites for a specific user.
     * +scopeByUser(user_id) QueryBuilder
     */
    public function scopeByUser(Builder $query, int $userId): void
    {
        $query->where('user_id', $userId);
    }

    // Relationships

    /**
     * Get the user who marked this equipment as favorite.
     * User "1" --> "0..*" UserFavorite
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the equipment that was marked as favorite.
     * Equipment "1" --> "0..*" UserFavorite
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }
}
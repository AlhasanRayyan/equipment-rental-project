<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class AdminSetting extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admin_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'setting_key',
        'setting_value',
        'description',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // لا يوجد حقول تاريخ/وقت محددة غير created_at/updated_at التي يتم التعامل معها تلقائياً.
    ];

    // Relationships

    /**
     * Get the user who last updated this setting.
     * User "1" --> "0..*" AdminSetting : updated_by
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Accessors (من الـ classDiagram)

    /**
     * Get the setting's value, casting it based on key if needed.
     * +getValue() mixed
     */
    public function getValue(): mixed
    {
        // يمكنك إضافة منطق لتحديد نوع القيمة هنا (مثال: boolean, int, float)
        // بناءً على setting_key. حالياً، يعود كسلسلة نصية.
        if ($this->setting_key === 'maintenance_mode') {
            return filter_var($this->setting_value, FILTER_VALIDATE_BOOLEAN);
        }
        if ($this->setting_key === 'tax_rate_percent' || $this->setting_key === 'some_float_value') {
            return (float) $this->setting_value;
        }
        if ($this->setting_key === 'minimum_rental_days' || $this->setting_key === 'some_int_value') {
            return (int) $this->setting_value;
        }
        return $this->setting_value;
    }

    /**
     * Set the setting's value.
     * +setValue(value) void
     */
    public function setValue(mixed $value): void
    {
        $this->setting_value = (string) $value; // عادة تخزن كـ string في DB
        $this->save();
    }

    // Scopes (من الـ classDiagram)

    /**
     * Scope a query to only include settings by a specific key.
     * +scopeByKey(key) QueryBuilder
     */
    public function scopeByKey(Builder $query, string $key): void
    {
        $query->where('setting_key', $key);
    }
}
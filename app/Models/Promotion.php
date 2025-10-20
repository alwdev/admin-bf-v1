<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Promotion extends Model
{
    use HasFactory;

    protected $table = 'promotion';

    protected $fillable = [
        'name',
        'turnover',
        'enable',
        'active',
        'bonus',
        'withdraw_percent',
        'is_newuser',
        'withdraw_limit',
        'deposit',
        'store_id',
        'description',
        'image',
        'is_percentage_based',
        'bonus_percentage',
        'turnover_percentage',
        'withdraw_limit_percentage',
        'is_recurring_promotion',
        'recurring_promotion_days',
        'applicable_games', // ยังคงต้องอยู่ใน fillable
        'is_first_deposit_bonus',
        'recurring_bonus_percentage',
        'recurring_turnover_percentage',
        'is_turnover_x2',
    ];

    protected $casts = [
        // *** ลองเอา 'applicable_games' ออกจาก $casts ไปก่อน ***
        // *** เพราะดูเหมือนว่ามันจะทำงานได้ไม่ดีกับระบบของคุณในตอนนี้ ***
        // 'applicable_games' => 'array',

        'is_turnover_x2' => 'boolean',
        'is_percentage_based' => 'boolean',
        'is_recurring_promotion' => 'boolean',
        'is_first_deposit_bonus' => 'boolean',
        'enable' => 'boolean',
        'active' => 'boolean',
        'is_newuser' => 'boolean',
        'deposit' => 'float',
        'bonus' => 'float',
        'turnover' => 'float',
        'withdraw_limit' => 'float',
        'bonus_percentage' => 'float',
        'turnover_percentage' => 'float',
        'withdraw_limit_percentage' => 'float',
        'recurring_promotion_days' => 'integer',
        'recurring_bonus_percentage' => 'float',
        'withdraw_percent' => 'integer',
        'store_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'recurring_turnover_percentage' => 'float',

    ];

    protected $attributes = [
        // 'applicable_games' => '[]', // ไม่ต้องกำหนด default ตรงนี้เมื่อจัดการด้วย Mutator ด้วยมือ
        'is_percentage_based' => false,
        'is_recurring_promotion' => false,
        'is_first_deposit_bonus' => false,
        'turnover' => 0.0,
        'bonus' => 0.0,
        'withdraw_limit' => 0.0,
        'withdraw_percent' => 0,
        'store_id' => 1,
        'recurring_promotion_days' => 0,
        'bonus_percentage' => 0.0,
        'turnover_percentage' => 0.0,
        'withdraw_limit_percentage' => 0.0,
        'recurring_bonus_percentage' => 0.0,
        'active' => true,
        'enable' => true,
        'is_newuser' => false,
    ];

    // Percentage fields mutators (เหมือนเดิม)
    protected function bonusPercentage(): Attribute
    {
        return Attribute::make(get: fn($value) => (float) $value, set: fn($value) => (float) str_replace('%', '', (string) $value));
    }
    protected function turnoverPercentage(): Attribute
    {
        return Attribute::make(get: fn($value) => (float) $value, set: fn($value) => (float) str_replace('%', '', (string) $value));
    }
    protected function withdrawLimitPercentage(): Attribute
    {
        return Attribute::make(get: fn($value) => (float) $value, set: fn($value) => (float) str_replace('%', '', (string) $value));
    }
    protected function recurringBonusPercentage(): Attribute
    {
        return Attribute::make(get: fn($value) => (float) $value, set: fn($value) => (float) str_replace('%', '', (string) $value));
    }
    protected function recurringTurnoverPercentage(): Attribute
    {
        return Attribute::make(get: fn($value) => (float) $value, set: fn($value) => (float) str_replace('%', '', (string) $value));
    }

    public function getIsRecurringPromotionTextAttribute(): string
    {
        return $this->is_recurring_promotion ? 'ใช่' : 'ไม่ใช่';
    }

    /**
     * **แก้ไขใหม่ทั้งหมดสำหรับ applicable_games**
     *
     * เราจะจัดการ json_encode/decode ด้วยมือใน Mutator/Accessor
     * และเอาออกจาก $casts array เพื่อป้องกันความขัดแย้ง
     */

    // Mutator (เมื่อบันทึกข้อมูล): บังคับ json_encode
    public function setApplicableGamesAttribute($value)
    {
        // ตรวจสอบให้แน่ใจว่าเป็น Array
        if (!is_array($value)) {
            $value = (array) $value; // ถ้าเป็น string หรืออะไรก็ตาม ให้แปลงเป็น array
        }
        // ลบค่าว่าง/null ออก และทำให้เป็น simple indexed array
        $cleanedValue = array_values(array_filter($value, fn($item) => $item !== null && $item !== ''));

        // บังคับ encode เป็น JSON string
        $this->attributes['applicable_games'] = json_encode($cleanedValue, JSON_UNESCAPED_UNICODE);
    }

    // Accessor (เมื่อดึงข้อมูล): บังคับ json_decode
    public function getApplicableGamesAttribute($value)
    {
        // ถ้าค่าเป็น string (จาก DB) และไม่ใช่ null/empty string
        if (is_string($value) && !empty($value)) {
            $decoded = json_decode($value, true);
            // ถ้า decode ได้เป็น array ให้คืนค่า array นั้น
            if (is_array($decoded)) {
                return $decoded;
            }

            // ถ้า decode ไม่ได้ (อาจเป็น Unicode escape ที่ไม่สมบูรณ์ หรือสตริงเดียวๆ)
            // ลองพยายามแก้ไขรูปแบบ Unicode escape ที่ไม่มี backslash
            // เช่น 'u0e17' --> '\u0e17'
            $fixedValue = preg_replace('/(?<!\\\)(u[0-9a-fA-F]{4})/', '\\\\$1', $value);
            // พยายาม json_decode อีกครั้งหลังจากแก้ไข
            $decodedFixed = json_decode('["' . $fixedValue . '"]', true); // ลองใส่ใน array และ quotes
            if (is_array($decodedFixed) && !empty($decodedFixed[0])) {
                // ตรวจสอบว่า element แรกถูกถอดรหัสแล้ว
                $finalDecoded = json_decode('"' . $decodedFixed[0] . '"');
                if ($finalDecoded !== null && $finalDecoded !== false) {
                    return [$finalDecoded];
                }
            }
            // หากยังไม่ได้ ให้คืนค่าเป็น array ที่มี string เดิม (ถ้าไม่ empty)
            return [$value]; // ห่อด้วย array เพื่อให้เข้ากับรูปแบบที่คาดหวัง
        }
        // ถ้าไม่ใช่ string หรือเป็น null/empty string ให้คืน array ว่าง
        return [];
    }

    // --- Scopes และ Relationships (ยังคงเหมือนเดิม) ---
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
    public function scopeForNewUsers($query)
    {
        return $query->where('is_newuser', true);
    }
    public function scopeForFirstDepositBonus($query)
    {
        return $query->where('is_first_deposit_bonus', true);
    }
    public function scopePercentageBased($query)
    {
        return $query->where('is_percentage_based', true);
    }
    public function scopeApplicableToGame($query, $gameType)
    {
        // ใช้ WHERE JSON_CONTAINS ได้ถ้าคอลัมน์เป็น JSON type จริงๆ
        // แต่ถ้าเป็น TEXT ต้องใช้วิธีอื่น (เช่น LIKE '%"'.$gameType.'"%')
        // ถ้าตอนนี้ยังเป็น TEXT ให้ใช้ LIKE ไปก่อน
        return $query->whereJsonContains('applicable_games', $gameType);
    }
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}

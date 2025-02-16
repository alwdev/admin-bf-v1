<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $table = 'store';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'address1',
        'address2',
        'city',
        'state',
        'country',
        'zipcode',
        'phone1',
        'phone2',
        'tier_level',
        'cost_method',
        /*
        value=1 FIFO Method
        value=2 LIFO Method
        value=3 Average Method
        value=4 Special Identity Method
        */
        'image',
        'created_by',
        'isActive',
        'branch_limit',
        'user_limit',
        'warehouse_limit',
        'taxnexus_use',
        'convenience_fee',
        'email',
        'ach_payment',
        'creditCard_payment',
        'terminal_payment',
        'merchant_name',
        'merchant_site_id',
        'merchant_key',
        'merchant_web_api_key',
        'note1',
        'note2',
        'note3',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }
    public function active_branches() {
        return $this->branches()->where('isActive','=', true);
    }
    public function warehouse()
    {
        return $this->hasMany(Warehouse::class)->where('isActive','=', true);
    }
    
}

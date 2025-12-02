<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentAccount extends Model
{
    //
    protected $fillable = ['tenant_id', 'unit_number','apartment_id','start_date', 'account_type', 'created_by','status'];
    
    
      public function Apartment()
    {
        return $this->hasOne(ApartmentIdentity::class, 'id', 'apartment_id');
     
    }
    public function Tenant(){
         return $this->hasOne(TenantModel::class, 'id', 'tenant_id');
    }
        
    public function rentCycles()
    {
        return $this->hasMany(RentCycle::class, 'rent_account_id');
    }
         
}

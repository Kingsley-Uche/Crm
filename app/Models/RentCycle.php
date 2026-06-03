<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentCycle extends Model
{
    //
 
        protected $fillable = ['apartment_id', 'start_date', 'tenant_id','end_date', 'rent_fee', 'account_type', 'unit_number','duration_months',
        'payment_method', 'escalation_policy', 'created_by', 'rent_account_id','status', 'payment_made', ];
        
         public function Apartment()
    {
        return $this->hasOne(ApartmentIdentity::class, 'id', 'apartment_id');
     
    }
    public function Tenant(){
         return $this->hasOne(TenantModel::class, 'id', 'tenant_id');
    }
    public function RentAccount(){
          return $this->hasOne(RentAccount::class, 'id', 'rent_account_id');
    }
        
    
        
        
       
        
}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationModel extends Model
{
    protected $fillable = ['name'];
    protected $table= 'location_models';
   
   
   
    public function blocks()
{
    return $this->hasMany(BlockModel::class, 'location_id');
}

}

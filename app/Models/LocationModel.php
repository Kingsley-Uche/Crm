<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationModel extends Model
{
    protected $fillable = ['name', 'branch_id'];
    protected $table= 'location_models';
   
   
   
    public function blocks()
{
    return $this->hasMany(BlockModel::class, 'location_id');
}

    public function branch()
    {
        return $this->belongsTo(BranchModel::class, 'branch_id');
    }

}

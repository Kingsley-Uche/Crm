<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApartmentIdentity extends Model
{
    protected $fillable = [
        'branch_id', 'location_models_id', 'shelter_id',  'unique_code', 'pay_frequency_id','landlord_id',
        'fee', 'pro_sco_code', 'property_ref','ownership', 'unit_number', 'post_code', 'admin_unit','tenancy_type','address'
    ];

 

    /**
     * Generate a unique code for the apartment identity
     *
     * @param  int $branch_id
     * @param  int $location_id
     * @return string
     */
   public function generateUniqueCode($branch_id, $location_id)
{
    // Get the abbreviation from the .env file with a fallback (limit to 3 characters)
    $app_abbr = substr(env('APP_ABRV', 'proptech'), 0, 3); // Default to 'CTR' if not set

    // Ensure branch_id and location_id are fixed length (4 digits each)
    $branch_id = str_pad((int)$branch_id, 4, '0', STR_PAD_LEFT);
    $location_id = str_pad((int)$location_id, 4, '0', STR_PAD_LEFT);

    $maxAttempts = 10; // Prevent infinite loops
    $attempt = 0;

    do {
        // Generate a random number (5 digits)
        $random = str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);

        // Concatenate parts: 3 (abbr) + 4 (block_id) + 4 (shelter_id) + 5 (random) = 16 characters
        $unique_code = $app_abbr . $branch_id . $location_id . $random;

        // Check if this code already exists in the database
        $exists = static::where('unique_code', $unique_code)->exists();
        $attempt++;

        if ($attempt >= $maxAttempts) {
            throw new \Exception("Unable to generate a unique code after $maxAttempts attempts");
        }
    } while ($exists);

    // Return the unique code in uppercase for consistency
    return strtoupper($unique_code);
}
    /**
     * Relation with BlockShelter
    


     * Relation with ShelterAmenities
     */
    public function shelterAmenities()
    {
        return $this->hasMany(Shelter_Amenities::class, 'id_apartment_id', 'id')->select('id', 'id_apartment_id', 'amenity_id', 'amenity_number', 'branch_id');
       
    }
    public function amenitySize(){
         return $this->hasMany(AmenitySize::class, 'apartment_id', 'id');
    }
        
    

    /**
     * Relation with Shelter
     */
    public function shelter()
    {
       return $this->belongsTo(Shelter::class, 'shelter_id')->where('is_active', 1)->select('id', 'name');
     
    }
    public function booking()
{
    return $this->hasOne(BookingModel::class, 'shelter_id', 'shelter_id');
}


public function bookStatus(){
    return $this->hasOne(BookingModel::class, 'apartment_id', 'id');
}
    
    /**
     * Relation with Landlord
     */
    public function landlord()
    {
        return $this->hasOne(EstateOwner::class, 'id', 'landlord_id')->select('id', 'fName','lName', 'email', 'phones');
    }

    /**
     * Relation with LocationModel
     */
    public function location()
    {
        return $this->belongsTo(LocationModel::class, 'location_models_id')->select('id', 'name', 'branch_id');
    }

    //amenity size relation
    public function amenitySizes()
    {
        return $this->hasMany(AmenitySize::class, 'apartment_id', 'id');
    }
    

}

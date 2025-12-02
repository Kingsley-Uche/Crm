<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApartmentIdentity extends Model
{
    protected $fillable = [
        'block_models_id', 'shelter_id', 'block_shelter_id', 'unique_code', 'pay_frequency_id',
        'fee', 'pro_sco_code', 'property_ref','ownership', 'unit_number', 'post_code', 'admin_unit','tenancy_type','address'
    ];

 

    /**
     * Generate a unique code for the apartment identity
     *
     * @param  int $block_id
     * @param  int $shelter_id
     * @return string
     */
   public function generateUniqueCode($block_id, $shelter_id)
{
    // Get the abbreviation from the .env file with a fallback (limit to 3 characters)
    $app_abbr = substr(env('APP_ABRV', 'CTR'), 0, 3); // Default to 'CTR' if not set

    // Ensure block_id and shelter_id are fixed length (4 digits each)
    $block_id = str_pad((int)$block_id, 4, '0', STR_PAD_LEFT);
    $shelter_id = str_pad((int)$shelter_id, 4, '0', STR_PAD_LEFT);

    $maxAttempts = 10; // Prevent infinite loops
    $attempt = 0;

    do {
        // Generate a random number (5 digits)
        $random = str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);

        // Concatenate parts: 3 (abbr) + 4 (block_id) + 4 (shelter_id) + 5 (random) = 16 characters
        $unique_code = $app_abbr . $block_id . $shelter_id . $random;

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
     */
    public function blockShelter()
    {
        return $this->hasMany(BlockShelter::class, 'shelter_id', 'id');
        
    }

    /**
     * Relation with ShelterAmenities
     */
    public function shelterAmenities()
    {
        return $this->hasMany(Shelter_Amenities::class, 'id_apartment_id', 'id');
       
    }
    public function amenitySize(){
         return $this->hasMany(AmenitySize::class, 'apartment_id', 'id');
    }
        
    

    /**
     * Relation with Shelter
     */
    public function shelter()
    {
        return $this->hasOne(Shelter::class, 'id', 'shelter_id');
     
    }
    public function booking()
{
    return $this->hasOne(BookingModel::class, 'shelter_id', 'shelter_id');
}

public function block(){
     return $this->hasOne(BlockModel::class, 'id', 'block_models_id');
}
public function bookStatus(){
    return $this->hasOne(BookingModel::class, 'apartment_id', 'id');
}
    

    

}

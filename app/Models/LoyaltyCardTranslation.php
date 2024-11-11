<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{LoyaltyCard};   

class LoyaltyCardTranslation extends Model
{
    use HasFactory;
    protected $fillable = [
        'loyalty_card_id',  // Add this line
        'language_id',      // Add other fields you want to allow mass assignment
        'description',  
        'name'    // Example field
    ];
    public function loyaltyCard()
    {
        return $this->belongsTo(LoyaltyCard::class);
    }
}

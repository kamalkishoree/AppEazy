<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use app\Models\SubscriptionPlanUserTranslation;
class ClientLanguage extends Model
{
	 protected $primaryKey = null;
    public $incrementing = false;
    
    protected $fillable = ['client_code', 'language_id', 'is_primary', 'is_active'];

    public function language()
    {
      return $this->belongsTo('App\Models\Language','language_id','id')->select('id', 'name', 'sort_code','nativeName');
    }

    public function languageTrans(){
       return $this->hasMany(ClientLanguage::class, 'client_code', 'client_code'); 
    }

    public function variantTrans(){
       return $this->hasOne(VariantTranslation::class, 'language_id', 'language_id'); 
    }

    public function brand_trans(){
       return $this->hasOne(BrandTranslation::class, 'language_id', 'language_id')->select('id', 'title', 'brand_id', 'language_id'); 
    }
    public function giftcardTrans(){
      return $this->hasOne(GiftCardTranslation::class, 'language_id', 'language_id')->select('id', 'title', 'gift_card_id', 'language_id', 'description'); 
   }
   public function vendorSubsTrans(){
      return $this->hasOne(SubscriptionPlanVendorTranslation::class, 'language_id', 'language_id')->select('id', 'title', 'subsplan_vendor_id', 'language_id', 'description'); 
   }
   public function userSubsTrans(){
      return $this->hasOne(SubscriptionPlansUserTranslation::class, 'language_id', 'language_id')->select('id', 'title', 'subsplan_userid', 'language_id', 'description'); 
   }

    /*public function addon_trans(){
       return $this->hasOne(BrandTranslation::class, 'language_id', 'language_id')->select('id', 'title', 'brand_id', 'language_id'); 
    }*/

}

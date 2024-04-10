<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promocode extends Model
{
    use HasFactory;

    protected $table = 'promocodes';

    protected $fillable = ['name', 'amount', 'expiry_date', 'promo_type_id', 'allow_free_delivery', 'minimum_spend', 'maximum_spend', 'first_order_only', 'limit_per_user', 'limit_total', 'paid_by_vendor_admin','restriction_on', 'image','short_desc', 'promo_visibility', 'promo_type'];

    public function restriction()
    {
        return $this->hasMany(PromocodeRestriction::class);
    }
    public function details()
    {
        return $this->hasMany(PromoCodeDetail::class)->select('id', 'promocode_id', 'refrence_id');
    }
    public function getImageAttribute($value){
        $img = 'default/default_image.png';
        $values = array();
        if(!empty($value)){
            $img = $value;
        }
        $ex = checkImageExtension($img);
        $values['proxy_url'] = \Config::get('app.IMG_URL1');
        $values['image_path'] = \Config::get('app.IMG_URL2').'/'.\Storage::disk('s3')->url($img).$ex;
        $values['image_fit'] = \Config::get('app.FIT_URl');
        return $values;
    }

    public function type()
    {
        return $this->belongsTo(PromoType::class, 'promo_type_id', 'id')->select('id', 'title')->where('status', 1);
    }
}
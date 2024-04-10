<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlansVendor extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = "subscription_plans_vendor";

    public function features(){
        return $this->hasMany('App\Models\SubscriptionPlanFeaturesVendor', 'subscription_plan_id', 'id')->select('id','subscription_plan_id', 'feature_id');
    }

    public function getImageAttribute($value)
    {
        $values = array();
        $img = 'default/default_image.png';
        if(!empty($value)){
          $img = $value;
        }
        $ex = checkImageExtension($img);
        $values['proxy_url'] = \Config::get('app.IMG_URL1');
        $values['image_path'] = \Config::get('app.IMG_URL2').'/'.\Storage::disk('s3')->url($img).$ex;
        $values['image_fit'] = \Config::get('app.FIT_URl');
        $values['original'] = $value;

        return $values;
    }
}

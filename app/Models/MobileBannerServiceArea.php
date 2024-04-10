<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MobileBannerServiceArea extends Model
{
    use HasFactory;

    public function serviceArea(){
      return $this->hasOne('App\Models\ServiceAreaForBanner', 'id', 'service_area_id')->where('type', 2)->select('id', 'geo_array', 'name');
    }
}
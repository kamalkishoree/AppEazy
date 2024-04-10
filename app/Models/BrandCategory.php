<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandCategory extends Model
{
    protected $fillable = ['brand_id', 'category_id'];

    public function cate(){
       return $this->belongsToMany('App\Models\Category','brand_categories','category_id','brand_id'); 
    }

    public function translation(){
       return $this->hasMany('App\Models\BrandTranslation', 'brand_id', 'brand_id')->select('brand_id', 'language_id', 'title'); 
    }

    public function brand(){
        return $this->hasOne('App\Models\Brand', 'id', 'brand_id')->select('id','title');
    }
    
    public function categoryDetail(){
      return $this->belongsTo('App\Models\Category', 'category_id', 'id'); 
  }
}

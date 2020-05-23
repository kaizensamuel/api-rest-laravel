<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Post extends Model
{
    protected $table='posts';

    //Relacion de muchos a uno muchos post pueden pertenecer a un usuario y muchos post pueden tener una categoria
    public function user(){
        return $this->belongsTo('App\User','user_id');
    }
    public function category(){
        return $this->belongsTo('App\Category','category_id');
    }
    //
}

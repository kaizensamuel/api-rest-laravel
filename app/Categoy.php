<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categoy extends Model
{
    protected $table='categories';
    // Relacion de uno a muchos. Sacar todos los post que tienen esa categoria
    public function posts(){
        return $this->hasMany('App\Post');
    }
    //
}

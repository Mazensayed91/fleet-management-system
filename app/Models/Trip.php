<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    public function bus(){

        return $this->hasOne(Bus::class);
        
    }

    public function station(){

        return $this->hasMany(Station::class);
        
    }

    public function cross_over_station(){

        return $this->hasMany(CrossOverStation::class) -> orderBy('order', 'ASC');
        
    }
}

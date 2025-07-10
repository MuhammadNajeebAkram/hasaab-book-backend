<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institute extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'contact_no',
        'city_id',        
    ];

    public function professors(){
        return $this->hasMany(Professor::class);
    }

    public function city(){
        return $this->belongsTo(City::class);
    }
}

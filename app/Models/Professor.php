<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professor extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'contact_no',
        'city_id',
        'institute_id',
        'subject_id',
        'is_author',
    ];

    public function city(){
        return $this->belongsTo(City::class);
    }

    public function institute(){
        return $this->belongsTo(Institute::class);
    }

    public function subject(){
        return $this->belongsTo(Subject::class);
    }
}

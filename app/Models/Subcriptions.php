<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcriptions extends Model
{
    //
    protected $fillable = ['user_id','name','category','price','billing_cycle'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}

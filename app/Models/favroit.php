<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class favroit extends Model
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $fillable = [
        'user_id',
        'house_id'];

        protected $table = 'favroit';

    public function User(){
        return $this->hasMany(User::class);
    }

    public function Houses(){
        return $this->hasMany(houses::class);
    }
}

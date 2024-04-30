<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
class houses extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'neighborhood',
        'area',
        'width',
        'height',
        'estateType',
        'estateStreet',
        'estateDeed',
        'price',
        'images',
        'displayType',
        'note'
    ];

    public function User()
    {
        return $this->hasMany(User::class);
    }
}

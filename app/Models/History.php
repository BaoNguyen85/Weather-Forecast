<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'email', 'location_localtime', 'temp', 'wind', 'humidity' 
    ];
    protected $primaryKey = 'id';
    protected $table = 'history';
}

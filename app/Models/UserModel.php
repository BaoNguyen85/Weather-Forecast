<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'email', 'location', 'daily'
    ];
    protected $primaryKey = 'id';
    protected $table = 'tbl_users';
}

<?php

namespace App\Models\MySQL;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MySqlQuery extends Model
{
    use HasFactory;

    protected $table = 'mysql_queries'; //Name of the table in the database

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

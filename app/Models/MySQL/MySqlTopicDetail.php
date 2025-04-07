<?php

namespace App\Models\MySQL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MySqlTopicDetail extends Model
{
    use HasFactory;

    protected $table = 'mysql_topic_details'; //Name of the table in the database

    public function topics()
    {
        return $this->hasMany(MySqlTopic::class, 'topic_id');
    }
}


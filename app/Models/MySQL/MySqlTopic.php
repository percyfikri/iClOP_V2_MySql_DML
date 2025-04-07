<?php

namespace App\Models\MySQL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MySqlTopic extends Model
{
    use HasFactory;
    
    protected $table = 'mysql_topics'; //Name of the table in the database

    public function topicDetail()
    {
        return $this->belongsTo(MySqlTopicDetail::class, 'topic_id');
    }
}

<?php

namespace App\Models\MySQL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MySqlTopics extends Model
{
    use HasFactory;
    
    protected $table = 'mysql_topics'; //Name of the table in the database

    protected $fillable = [
        'title',
    ];

    // Relasi HasMany dengan TopicDetail
    public function topicDetails()
    {
        return $this->hasMany(MySqlTopicDetails::class, 'topic_id');
    }
}

<?php

namespace App\Models\MySQL;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MySqlTopics extends Model
{
    use HasFactory;
    
    protected $table = 'mysql_topics'; //Name of the table in the database

    protected $fillable = [
        'title',
        'created_by',
    ];

    // Relasi HasMany dengan TopicDetail
    public function topicDetails()
    {
        return $this->hasMany(MySqlTopicDetails::class, 'topic_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

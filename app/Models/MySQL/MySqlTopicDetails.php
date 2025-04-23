<?php

namespace App\Models\MySQL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MySqlTopicDetails extends Model
{
    use HasFactory;

    protected $table = 'mysql_topic_details'; //Name of the table in the database

    protected $fillable = [
        'title', 'description', 'folder_path', 'topic_id',
    ];

    // Relasi BelongsTo dengan Topic
    public function topic()
    {
        return $this->belongsTo(MySqlTopics::class, 'topic_id');
    }

    // Relasi HasOne dengan StudentSubmission
    public function submission()
    {
        return $this->hasOne(MySqlStudentSubmissions::class, 'topic_detail_id');
    }
}


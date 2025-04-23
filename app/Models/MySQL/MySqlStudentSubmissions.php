<?php

namespace App\Models\MySQL;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MySqlStudentSubmissions extends Model
{
    use HasFactory;

    protected $table = 'mysql_student_submissions';

    protected $fillable = [
        'user_id', 'topic_detail_id', 'feedback_id', 'query',
    ];

    // Relasi BelongsTo dengan User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi BelongsTo dengan TopicDetail
    public function topicDetail()
    {
        return $this->belongsTo(MySqlTopicDetails::class, 'topic_detail_id');
    }

    // Relasi BelongsTo dengan Feedback
    public function feedback()
    {
        return $this->belongsTo(MySqlFeedbacks::class, 'feedback_id');
    }
}

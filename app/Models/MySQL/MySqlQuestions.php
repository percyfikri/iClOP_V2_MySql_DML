<?php

namespace App\Models\MySQL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MySqlQuestions extends Model
{
    use HasFactory;

    protected $table = 'mysql_questions';

    protected $fillable = [
        'topic_detail_id',
        'question',
        'answer_key',
        'folder_path',
        'file_name',
        'created_by',
    ];

    // Relasi: Setiap question milik satu topic detail
    public function topicDetail()
    {
        return $this->belongsTo(MySqlTopicDetails::class, 'topic_detail_id');
    }

    // Relasi: Setiap question memiliki banyak student submissions
    public function studentSubmissions()
    {
        return $this->hasMany(MySqlStudentSubmissions::class, 'question_id');
    }
}

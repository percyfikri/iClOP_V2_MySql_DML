<?php

namespace App\Models\MySQL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MySqlFeedbacks extends Model
{
    use HasFactory;

    protected $table = 'mysql_feedbacks'; //Name of the table in the database

    protected $fillable = [
        'feedback',
    ];

    // Relasi BelongsTo dengan StudentSubmission
    public function submission()
    {
        return $this->hasOne(MySqlStudentSubmissions::class, 'feedback_id');
    }
}

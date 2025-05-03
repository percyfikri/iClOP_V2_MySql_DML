<?php

namespace App\Models\MySQL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MySqlQueries extends Model
{
    use HasFactory;

    protected $table = 'mysql_queries';

    protected $fillable = [
        'submission_id', 'question_number', 'query',
    ];

    // Relasi: Setiap query milik satu submission
    public function submission()
    {
        return $this->belongsTo(MySqlStudentSubmissions::class, 'submission_id');
    }
}

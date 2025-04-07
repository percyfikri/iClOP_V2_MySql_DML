<?php

namespace App\Models\MySQL;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MySqlStudentSubmissions extends Model
{
    use HasFactory;

    protected $table = 'mysql_student_submissions'; //Name of the table in the database

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function task()
    {
        return $this->belongsTo(MySqlTopic::class, 'task_id');
    }

    public function queries()
    {
        return $this->belongsTo(MySqlQuery::class, 'query_id');
    }
}

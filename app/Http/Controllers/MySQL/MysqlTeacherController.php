<?php

namespace App\Http\Controllers\MySQL;

use App\Http\Controllers\Controller;
use App\Models\MySQL\MySqlTopics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MysqlTeacherController extends Controller
{
    public function index()
    {
        $data = DB::table('mysql_topics as t')
            ->join('mysql_topic_details as td', 'td.topic_id', '=', 't.id')
            ->join('mysql_questions as q', 'q.topic_detail_id', '=', 'td.id')
            ->select('t.title as topic_title', 'td.title as sub_topic_title', 'td.file_name as module', 'q.question', 'q.answer_key')
            ->orderBy('t.id', 'asc')
            ->orderBy('td.id', 'asc')
            ->orderBy('q.id', 'asc')
            ->get();

        echo "<script>console.log(" . json_encode($data) . ");</script>";
        
        return view('mysql_dml.teacher.index', compact('data'));
    }
}


<?php

namespace App\Http\Controllers\MySQL;

use App\Http\Controllers\Controller;
use App\Models\MySQL\MysqlExpectedQuery;
use App\Models\MySQL\MySqlTopics;
use App\Models\MySQL\MySqlTopicDetails;

class MysqlTeacherAnswerKeyController extends Controller
{
    public function answerKeyTable()
    {
        // Ambil semua answer key beserta relasi subtopik (topicDetail)
        $answerKeys = MysqlExpectedQuery::with('topicDetail')->get();

        // Ambil semua topik
        $topics = MySqlTopics::all();

        // Ambil semua subtopik
        $subtopics = MySqlTopicDetails::all();

        // Kirim ke view
        return view('mysql_dml.teacher.answer_key', compact('answerKeys', 'topics', 'subtopics'));
    }
}

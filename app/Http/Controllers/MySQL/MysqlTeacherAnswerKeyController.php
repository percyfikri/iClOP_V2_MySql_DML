<?php

namespace App\Http\Controllers\MySQL;

use App\Http\Controllers\Controller;
use App\Models\MySQL\MysqlExpectedQuery;
use App\Models\MySQL\MySqlTopics;
use App\Models\MySQL\MySqlTopicDetails;
use Illuminate\Http\Request;

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

    public function saveAnswerKey(Request $request)
    {
        $validated = $request->validate([
            'topic_detail_id' => 'required|exists:mysql_topic_details,id',
            'answer_number' => 'required|integer|min:1',
            'expected_query' => 'required|string',
            'expected_table' => 'nullable|string',
        ]);

        MysqlExpectedQuery::updateOrCreate(
            [
                'topic_detail_id' => $validated['topic_detail_id'],
                'answer_number' => $validated['answer_number'],
            ],
            [
                'expected_query' => $validated['expected_query'],
                'expected_table' => $validated['expected_table'],
            ]
        );

        return response()->json(['success' => true]);
    }

    public function getAnswerKeyList()
    {
        return MysqlExpectedQuery::all();
    }

    public function deleteAnswerKey($id)
    {
        $deleted = MysqlExpectedQuery::where('id', $id)->delete();
        return response()->json(['success' => $deleted]);
    }
}

<?php

namespace App\Http\Controllers\MySQL;

use App\Http\Controllers\Controller;
use App\Models\MySQL\MySqlQuestions;
use App\Models\MySQL\MySqlTopicDetails;
use Illuminate\Http\Request;

class MysqlTeacherQuestionController extends Controller
{
    public function questionsTable()
    {
        $questions = MySqlQuestions::with('topicDetail')->get();
        $subtopics = MySqlTopicDetails::all();
        return view('mysql_dml.teacher.questions_table', compact('questions', 'subtopics'));
    }

    public function editQuestionAjax($id)
    {
        $question = MySqlQuestions::findOrFail($id);
        return response()->json($question);
    }

    public function updateQuestionAjax(Request $request, $id)
    {
        $request->validate([
            'question' => 'required|string',
            'answer_key' => 'required|string',
            'topic_detail_id' => 'required|integer',
        ]);
        $q = MySqlQuestions::findOrFail($id);
        $q->update($request->only('question', 'answer_key', 'topic_detail_id'));
        return response()->json(['success' => true]);
    }

    public function addQuestionAjax(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
            'answer_key' => 'required|string',
            'topic_detail_id' => 'required|integer',
        ]);
        MySqlQuestions::create($request->only('question', 'answer_key', 'topic_detail_id'));
        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        $question = MySqlQuestions::with(['topicDetail', 'createdByUser'])->findOrFail($id);

        return response()->json([
            'question' => $question->question,
            'answer_key' => $question->answer_key,
            'topic_detail' => $question->topicDetail ? [
                'title' => $question->topicDetail->title
            ] : null,
            'file_name' => $question->file_name ?? '-',
            'created_by_user' => $question->createdByUser ? [
                'name' => $question->createdByUser->name
            ] : null,
        ]);
    }
}

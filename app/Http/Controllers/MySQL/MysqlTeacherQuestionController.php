<?php

namespace App\Http\Controllers\MySQL;

use App\Http\Controllers\Controller;
use App\Models\MySQL\MySqlQuestions;
use App\Models\MySQL\MySqlTopicDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MysqlTeacherQuestionController extends Controller
{
    public function questionsTable()
    {
        $questions = MySqlQuestions::with('topicDetail')->get();
        $subtopics = MySqlTopicDetails::all();
        return view('mysql_dml.teacher.questions_table', compact('questions', 'subtopics'));
    }

    public function storeQuestion(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
            'answer_key' => 'required|string',
            'topic_detail_id' => 'required|exists:mysql_topic_details,id',
        ]);

        $question = MySqlQuestions::create([
            'question' => $request->question,
            'answer_key' => $request->answer_key,
            'topic_detail_id' => $request->topic_detail_id,
            'created_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'question' => $question]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'question' => 'required|string',
            'answer_key' => 'required|string',
            'topic_detail_id' => 'required|exists:mysql_topic_details,id',
        ]);

        $question = MySqlQuestions::findOrFail($id);
        $question->question = $request->question;
        $question->answer_key = $request->answer_key;
        $question->topic_detail_id = $request->topic_detail_id;
        $question->save();

        return response()->json(['success' => true, 'question' => $question]);
    }

    public function show($id)
    {
        $question = MySqlQuestions::with(['topicDetail', 'createdByUser'])->findOrFail($id);

        return response()->json([
            'question' => $question->question,
            'answer_key' => $question->answer_key,
            'topic_detail_id' => $question->topic_detail_id,
            'topic_detail' => $question->topicDetail ? [
                'title' => $question->topicDetail->title
            ] : null,
            'file_name' => $question->topicDetail->file_name ?? '-',
            'file_path' => $question->topicDetail->file_path ?? '',
            'created_by_user' => $question->createdByUser ? [
                'name' => $question->createdByUser->name
            ] : null,
        ]);
    }

    public function destroy($id)
    {
        $question = MySqlQuestions::findOrFail($id);
        $question->delete();

        return response()->json(['success' => true]);
    }
}

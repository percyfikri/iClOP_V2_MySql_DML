<?php

namespace App\Http\Controllers\MySQL;

use App\Http\Controllers\Controller;
use App\Models\MySQL\MySqlQuestions;
use App\Models\MySQL\MySqlTopicDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

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
            'question' => [
                'required',
                'string',
                // Unik per subtopic
                Rule::unique('mysql_questions')->where(function ($query) use ($request) {
                    return $query->where('topic_detail_id', $request->topic_detail_id);
                }),
            ],
            'answer_key' => 'required|string',
            'topic_detail_id' => 'required|exists:mysql_topic_details,id',
        ], [
            'question.unique' => 'Pertanyaan sudah ada pada subtopik ini.'
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
            'question' => [
                'required',
                'string',
                Rule::unique('mysql_questions')->where(function ($query) use ($request) {
                    return $query->where('topic_detail_id', $request->topic_detail_id);
                })->ignore($id),
            ],
            'answer_key' => 'required|string',
            'topic_detail_id' => 'required|exists:mysql_topic_details,id',
        ], [
            'question.unique' => 'Pertanyaan sudah ada pada subtopik ini.'
        ]);

        $question = MySqlQuestions::findOrFail($id);
        $question->update([
            'question' => $request->question,
            'answer_key' => $request->answer_key,
            'topic_detail_id' => $request->topic_detail_id,
        ]);

        return response()->json(['success' => true, 'question' => $question]);
    }

    public function getSubtopicModul($id)
    {
        $subtopic = \App\Models\MySQL\MySqlTopicDetails::find($id);
        if (!$subtopic) {
            return response()->json(['file_name' => null, 'file_path' => null]);
        }
        return response()->json([
            'file_name' => $subtopic->file_name,
            'file_path' => $subtopic->file_path,
        ]);
    }

    public function checkDuplicate(Request $request)
    {
        $question = $request->question;
        $topic_detail_id = $request->topic_detail_id;
        $exclude_id = $request->exclude_id; // Untuk edit

        $query = MySqlQuestions::where('question', $question)
            ->where('topic_detail_id', $topic_detail_id);

        if ($exclude_id) {
            $query->where('id', '!=', $exclude_id);
        }

        $exists = $query->exists();

        return response()->json(['exists' => $exists]);
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

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

    public function editQuestionAjax($id)
    {
        $question = MySqlQuestions::findOrFail($id);
        return response()->json($question);
    }

    public function storeQuestion(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
            'answer_key' => 'required|string',
            'topic_detail_id' => 'required|exists:mysql_topic_details,id',
            // 'modul' => 'nullable|file|mimes:pdf|max:20480',
        ]);

        $fileName = null;
        $folderPath = null;
        if ($request->hasFile('modul')) {
            $file = $request->file('modul');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $folderPath = 'mysql/DML/';
            $file->move(public_path($folderPath), $fileName);
        }

        $question = MySqlQuestions::create([
            'question' => $request->question,
            'answer_key' => $request->answer_key,
            'topic_detail_id' => $request->topic_detail_id,
            'file_name' => $fileName,
            'folder_path' => $folderPath,
            'created_by' => auth()->id(),
        ]);

        // echo "<script>console.log(" . json_encode($question) . ");</script>"; // Debug jika perlu

        return response()->json(['success' => true, 'question' => $question]);
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

    public function destroy($id)
    {
        $question = MySqlQuestions::findOrFail($id);

        // Hapus file jika ada
        if ($question->file_name && $question->folder_path) {
            $filePath = public_path($question->folder_path . $question->file_name);
            // Debug: cek path dan file_exists
            Log::info('Try delete file: ' . $filePath . ' exists: ' . (file_exists($filePath) ? 'yes' : 'no'));    
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        $question->delete();

        return response()->json(['success' => true]);
    }
}

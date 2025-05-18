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
            // 'modul' => 'nullable|file|mimes:pdf|max:20480',
        ]);

        $fileName = null;
        $filePath = null;
        if ($request->hasFile('modul')) {
            $file = $request->file('modul');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = 'mysql/DML/';
            $file->move(public_path($filePath), $fileName);

            // Simpan ke mysql_topic_details
            $topicDetail = MySqlTopicDetails::find($request->topic_detail_id);
            $topicDetail->file_name = $fileName;
            $topicDetail->file_path = $filePath;
            $topicDetail->save();
        }

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
            'modul' => 'nullable|file|mimes:pdf|max:20480',
        ]);

        $question = MySqlQuestions::findOrFail($id);

        // Handle file upload
        if ($request->hasFile('modul')) {
            $topicDetail = MySqlTopicDetails::find($request->topic_detail_id);

            // Hapus file lama jika ada
            if ($topicDetail && $topicDetail->file_name && $topicDetail->file_path) {
                $oldFile = public_path($topicDetail->file_path . $topicDetail->file_name);
                if (file_exists($oldFile)) {
                    @unlink($oldFile);
                }
            }

            $file = $request->file('modul');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = 'mysql/DML/';
            $file->move(public_path($filePath), $fileName);

            // Simpan ke mysql_topic_details
            if ($topicDetail) {
                $topicDetail->file_name = $fileName;
                $topicDetail->file_path = $filePath;
                $topicDetail->save();
            }
        }

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
            // Ambil file_name dari topicDetail
            'file_name' => $question->topicDetail->file_name ?? '-',
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

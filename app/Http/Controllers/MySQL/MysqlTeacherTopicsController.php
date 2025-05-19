<?php

namespace App\Http\Controllers\MySQL;

use App\Http\Controllers\Controller;
use App\Models\MySQL\MySqlTopics;
use App\Models\MySQL\MySqlTopicDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MysqlTeacherTopicsController extends Controller
{
    public function index()
    {
        $data = DB::table('mysql_topics as t')
            ->join('mysql_topic_details as td', 'td.topic_id', '=', 't.id')
            ->join('mysql_questions as q', 'q.topic_detail_id', '=', 'td.id')
            ->select(
                't.title as topic_title',
                'td.title as sub_topic_title',
                'td.file_name as module',      // ambil dari topic_details
                'td.file_path',                // ambil dari topic_details
                'q.question',
                'q.answer_key'
            )
            ->orderBy('t.id', 'asc')
            ->orderBy('td.id', 'asc')
            ->orderBy('q.id', 'asc')
            ->get();

        return view('mysql_dml.teacher.index', compact('data'));
    }

    public function topicsTable()
    {
        $data1 = \App\Models\MySQL\MySqlTopics::with(['topicDetails', 'createdBy'])->get();
        // Jika ingin menampilkan file_name/folder_path di subtopic, gunakan eager loading relasi questions
        // $data1 = MySqlTopics::with(['topicDetails.questions'])->get();
        return view('mysql_dml.teacher.topics_table', compact('data1'));
    }

    public function addTopicSubtopic(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'topic_title' => 'required|string|max:255',
            'sub_topic_title' => 'required|array|min:1',
            'sub_topic_title.*' => 'required|string|max:255',
            'sub_topic_file.*' => 'nullable|file|mimes:pdf|max:20480',
        ]);

        $topic = MySqlTopics::create([
            'title' => $request->topic_title,
            'created_by' => $userId,
        ]);

        if ($request->hasFile('sub_topic_file')) {
            $files = $request->file('sub_topic_file');
        }

        foreach ($request->sub_topic_title as $i => $subtopicTitle) {
            $fileName = null;
            $filePath = null;
            if (isset($files[$i]) && $files[$i]) {
                $file = $files[$i];
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = 'mysql/DML/';
                $file->move(public_path($filePath), $fileName);
            }
            MySqlTopicDetails::create([
                'topic_id' => $topic->id,
                'title' => $subtopicTitle,
                'file_name' => $fileName,
                'file_path' => $filePath,
                'created_by' => $userId,
            ]);
        }
        // dd($request->all());

        // Jika request AJAX, return JSON
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('mysql_teacher')->with('success', 'Topic & Sub-Topic saved!');
    }

    public function editTopicAjax($id)
    {
        $topic = MySqlTopics::findOrFail($id);
        $subtopics = MySqlTopicDetails::where('topic_id', $id)->get();
        return response()->json([
            'topic' => $topic,
            'subtopics' => $subtopics
        ]);
    }

    public function updateTopicAjax(Request $request, $id)
    {
        $topic = MySqlTopics::findOrFail($id);
        $topic->title = $request->topic_title;
        $topic->save();

        // Update subtopics
        $ids = $request->sub_topic_ids ?? [];
        $titles = $request->sub_topic_titles ?? [];
        $files = $request->file('edit_sub_topic_file', []);

        // Hapus subtopic yang dihapus user
        MySqlTopicDetails::where('topic_id', $id)
            ->whereNotIn('id', array_filter($ids))
            ->each(function ($subtopic) {
                // Hapus file lama jika ada
                if ($subtopic->file_name && $subtopic->file_path) {
                    $oldFile = public_path(rtrim($subtopic->file_path, '/\\') . DIRECTORY_SEPARATOR . $subtopic->file_name);
                    if (file_exists($oldFile)) {
                        @unlink($oldFile);
                    }
                }
                $subtopic->delete();
            });

        // Update atau tambah subtopic
        foreach ($titles as $i => $title) {
            $fileName = null;
            $filePath = null;
            if (!empty($ids[$i])) {
                // Update existing
                $sub = MySqlTopicDetails::find($ids[$i]);
                if ($sub) {
                    $sub->title = $title;
                    // Jika ada file baru diupload
                    if (isset($files[$i]) && $files[$i]) {
                        // Hapus file lama jika ada
                        if ($sub->file_name && $sub->file_path) {
                            $oldFile = public_path(rtrim($sub->file_path, '/\\') . DIRECTORY_SEPARATOR . $sub->file_name);
                            if (file_exists($oldFile)) {
                                @unlink($oldFile);
                            }
                        }
                        $file = $files[$i];
                        $fileName = time() . '_' . $file->getClientOriginalName();
                        $filePath = 'mysql/DML/';
                        $file->move(public_path($filePath), $fileName);
                        $sub->file_name = $fileName;
                        $sub->file_path = $filePath;
                    }
                    $sub->save();
                }
            } else {
                // Tambah baru
                if (isset($files[$i]) && $files[$i]) {
                    $file = $files[$i];
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = 'mysql/DML/';
                    $file->move(public_path($filePath), $fileName);
                }
                MySqlTopicDetails::create([
                    'topic_id' => $id,
                    'title' => $title,
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'created_by' => auth()->id(),
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    public function deleteTopic($id)
    {
        $topic = MySqlTopics::findOrFail($id);

        // Hapus file PDF pada setiap subtopic
        foreach ($topic->topicDetails as $subtopic) {
            if ($subtopic->file_name && $subtopic->file_path) {
                $filePath = public_path(rtrim($subtopic->file_path, '/\\') . DIRECTORY_SEPARATOR . $subtopic->file_name);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
        }

        // Hapus semua subtopic terkait
        $topic->topicDetails()->delete();

        // Hapus topic
        $topic->delete();

        // Jika request AJAX, return JSON
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('mysql_teacher')->with('success', 'Topic dan seluruh sub-topic berhasil dihapus.');
    }

    // Delete Topic, SubTopik dan juga file uploadnya
    public function deleteSubtopic($id)
    {
        $subtopic = MySqlTopicDetails::findOrFail($id);

        // Hapus file jika ada
        if ($subtopic->file_name && $subtopic->file_path) {
            $filePath = public_path(rtrim($subtopic->file_path, '/\\') . DIRECTORY_SEPARATOR . $subtopic->file_name);
            Log::info('Try delete file: ' . $filePath . ' exists: ' . (file_exists($filePath) ? 'yes' : 'no'));
            if (file_exists($filePath)) {
                @unlink($filePath);
                Log::info('File deleted: ' . $filePath);
            } else {
                Log::warning('File not found: ' . $filePath);
            }
        }

        $subtopic->delete();

        return response()->json(['success' => true]);
    }
}

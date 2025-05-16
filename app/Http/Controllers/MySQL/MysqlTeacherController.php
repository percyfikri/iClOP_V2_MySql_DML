<?php

namespace App\Http\Controllers\MySQL;

use App\Http\Controllers\Controller;
use App\Models\MySQL\MySqlTopics;
use App\Models\MySQL\MySqlTopicDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MysqlTeacherController extends Controller
{
    public function index()
    {
        $data = DB::table('mysql_topics as t')
            ->join('mysql_topic_details as td', 'td.topic_id', '=', 't.id')
            ->join('mysql_questions as q', 'q.topic_detail_id', '=', 'td.id')
            ->select(
                't.title as topic_title',
                'td.title as sub_topic_title',
                'q.file_name as module', // file_name diambil dari mysql_questions
                'q.folder_path',         // jika ingin menampilkan folder_path juga
                'q.question',
                'q.answer_key'
            )
            ->orderBy('t.id', 'asc')
            ->orderBy('td.id', 'asc')
            ->orderBy('q.id', 'asc')
            ->get();

        // echo "<script>console.log(" . json_encode($data) . ");</script>"; // Debug jika perlu

        return view('mysql_dml.teacher.index', compact('data'));
    }

    public function topicsTable()
    {
        // Jika ingin menampilkan file_name/folder_path di subtopic, gunakan eager loading relasi questions
        $data1 = MySqlTopics::with(['topicDetails.questions'])->get();
        return view('mysql_dml.teacher.topics_table', compact('data1'));
    }

    public function addTopicSubtopic(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'topic_title' => 'required|string|max:255',
            'sub_topic_title' => 'required|array|min:1',
            'sub_topic_title.*' => 'required|string|max:255',
        ]);

        $topic = MySqlTopics::create([
            'title' => $request->topic_title,
            'created_by' => $userId,
        ]);

        foreach ($request->sub_topic_title as $subtopicTitle) {
            MySqlTopicDetails::create([
                'topic_id' => $topic->id,
                'title' => $subtopicTitle,
                'created_by' => $userId,
            ]);
        }
        // dd($request->all());

            return redirect()->route('mysql_teacher')->with('success', 'Topic & Sub-Topic saved!');
    }

    public function deleteTopic($id)
    {
        $topic = MySqlTopics::findOrFail($id);

        // Hapus semua subtopic terkait
        $topic->topicDetails()->delete();

        // Hapus topic
        $topic->delete();

        return redirect()->route('mysql_teacher')->with('success', 'Topic dan seluruh sub-topic berhasil dihapus.');
    }
}

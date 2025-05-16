<?php

namespace App\Http\Controllers\MySQL;

use App\Http\Controllers\Controller;
use App\Models\MySQL\MySqlTopicDetails;
use App\Models\MySQL\MySqlTopics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\select;

class MysqlController extends Controller
{
    public function index()
    {
        $topics = MySqlTopics::all();
        $topicDetails = MySqlTopicDetails::all();
        $topicsCount = count($topics);

        $idUser         = Auth::user()->id;
        $role    = DB::select("select role from users where id = $idUser");

        if ($role[0]->role == "student") {
            return view(
                'mysql_dml.student.material.index',
                compact(
                    'topics',
                    'role',
                    'topicsCount',
                    'topicDetails',
                )
            );
        }
    }

    function mysql_material_detail()
    {
        $mysqlid = (int)$_GET['mysqlid'] ?? '';
        $start = (int)$_GET['start'] ?? '';
        $output = $_GET['output'] ?? '';

        // Ambil topic detail
        $results = DB::select("select * from mysql_topic_details where topic_id = $mysqlid and id ='$start' ");

        // Ambil question terkait topic detail
        $question = DB::table('mysql_questions')
            ->where('topic_detail_id', $start)
            ->first();

        $html_start = '';
        $pdf_reader = 0;

        if ($question) {
            $html_start = empty($question->file_name) ? $question->question : $question->file_name;
            $pdf_reader = !empty($question->file_name) ? 1 : 0;
        }

        $idUser = Auth::user()->id;
        $roleTeacher = DB::select("select role from users where id = $idUser");

        $topics = MySqlTopics::all();
        $topicsNavbar = MySqlTopics::findOrFail($mysqlid);
        $detail = MySqlTopicDetails::findOrFail($mysqlid);
        $topicsCount = count($topics);
        $detailCount = ($topicsCount / $topicsCount) * 10;

        return view('mysql_dml.student.material.topic_detail', [
            'row' => $detail,
            'topics' => $topics,
            'topicsNavbar' => $topicsNavbar,
            'mysqlid' => $mysqlid,
            'html_start' => $html_start,
            'pdf_reader' => $pdf_reader,
            'topicsCount' => $topicsCount,
            'detailCount' => $detailCount,
            'output' => $output,
            'role' => isset($roleTeacher[0]) ? $roleTeacher[0]->role : '',
            'question' => $question, // kirim question ke view jika perlu
        ]);
    }

    function submit_user_input(Request $request)
    {
        // Validasi input
        $request->validate([
            'userInput' => 'required|string|max:255',
        ]);

        // Ambil data dari request
        $userInput = $request->input('userInput');
        $userId = Auth::user()->id; // Ambil ID pengguna yang sedang login
        $topicDetailId = $request->input('topic_detail_id'); // Pastikan Anda mengirimkan topic_detail_id dari form

        // Simpan ke tabel mysql_student_submissions terlebih dahulu
        $submissionId = DB::table('mysql_student_submissions')->insertGetId([
            'user_id' => $userId,
            'topic_detail_id' => $topicDetailId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Simpan query ke tabel mysql_queries
        DB::table('mysql_queries')->insert([
            'submission_id' => $submissionId,
            'question_number' => 1, // Anda bisa mengganti ini sesuai kebutuhan
            'query' => $userInput,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Redirect dengan pesan sukses
        return redirect()->back()->with('success', 'Your query has been submitted successfully.');
    }
}

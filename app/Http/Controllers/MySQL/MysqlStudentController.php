<?php

namespace App\Http\Controllers\MySQL;

use App\Http\Controllers\Controller;
use App\Models\MySQL\MySqlTopicDetails;
use App\Models\MySQL\MySqlTopics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MysqlStudentController extends Controller
{
    function mysql_material_detail()
    {
        $mysqlid = (int)$_GET['mysqlid'] ?? '';
        $start = (int)$_GET['start'] ?? '';
        $output = $_GET['output'] ?? '';

        // Ambil topic detail
        $results = DB::select("select * from mysql_topic_details where topic_id = $mysqlid and id ='$start' ");

        $html_start = '';
        $pdf_reader = 0;

        // foreach ($results as $r) {
        //     if ($mysqlid == $r->topic_id) {
        //         $html_start = empty($r->file_name) ? $r->description : $r->file_name;
        //         $pdf_reader = !empty($r->file_name) ? 1 : 0;
        //         break;
        //     }
        // }

        foreach ($results as $r) {
            if ($mysqlid == $r->topic_id) {
                if (!empty($r->file_name)) {
                    $html_start = $r->file_name;
                    $pdf_reader = 1;
                } else {
                    $html_start = "No Modules";
                    $pdf_reader = 0;
                }
                break;
            }
        }

        $idUser = Auth::user()->id;
        $roleTeacher = DB::select("select role from users where id = $idUser");

        $topics = MySqlTopics::all();
        $topicsNavbar = MySqlTopics::findOrFail($mysqlid);
        $detail = MySqlTopicDetails::findOrFail($start);
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

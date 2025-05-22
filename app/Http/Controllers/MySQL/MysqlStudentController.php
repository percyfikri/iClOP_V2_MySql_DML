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
    public function showTopicDetail(Request $request)
    {
        $mysqlid = (int) $request->get('mysqlid');
        $start = (int) $request->get('start');
        $output = $request->get('output', '');
        $questionIndex = (int) $request->get('q', 0); // index soal, default 0 (soal pertama)
        $answerStatus = session('answer_status'); // pesan benar/salah

        // Ambil semua soal terkait subtopik
        $questions = DB::table('mysql_questions')
            ->where('topic_detail_id', $start)
            ->orderBy('id')
            ->get();

        $userId = Auth::user()->id;

        // Ambil soal yang sedang aktif
        $currentQuestion = $questions->get($questionIndex);

        // Cek jika ada soal, baru ambil submission terakhir
        $lastAnswer = '';
        $lastStatus = null;
        if ($currentQuestion) {
            $lastSubmission = DB::table('mysql_student_submissions')
                ->where('user_id', $userId)
                ->where('question_id', $currentQuestion->id)
                ->orderByDesc('created_at')
                ->first();

            if ($lastSubmission) {
                $lastQuery = DB::table('mysql_queries')->where('id', $lastSubmission->query_id)->first();
                $lastAnswer = $lastQuery ? $lastQuery->query : '';
                $lastStatus = $lastSubmission->status ?? null;
            }
        }

        // Data lain (tidak berubah)
        $results = DB::select("select * from mysql_topic_details where topic_id = $mysqlid and id ='$start' ");
        $html_start = '';
        $pdf_reader = 0;
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
            'questions' => $questions,
            'currentQuestion' => $currentQuestion,
            'questionIndex' => $questionIndex,
            'answerStatus' => $answerStatus,
            'lastAnswer' => $lastAnswer,
            'lastStatus' => $lastStatus,
            'detail' => $detail,
        ]);
    }

    public function submitUserInput(Request $request)
    {
        $request->validate([
            'userInput' => 'required|string|max:255',
            'topic_detail_id' => 'required|integer',
            'question_id' => 'required|integer',
            'question_index' => 'required|integer',
            'mysqlid' => 'required|integer',
            'start' => 'required|integer',
        ]);

        $userInput = trim($request->input('userInput'));
        $questionId = $request->input('question_id');
        $questionIndex = $request->input('question_index');
        $mysqlid = $request->input('mysqlid');
        $start = $request->input('start');

        // Ambil kunci jawaban dari DB
        $question = DB::table('mysql_questions')->where('id', $questionId)->first();
        $isCorrect = false;
        if ($question) {
            $isCorrect = trim(strtolower($userInput)) === trim(strtolower($question->answer_key));
        }

        $userId = Auth::user()->id;

        // 1. Simpan ke tabel queries dan ambil ID-nya
        $queryId = DB::table('mysql_queries')->insertGetId([
            'query' => $userInput,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Simpan ke tabel submission, isi kolom query_id dan status
        DB::table('mysql_student_submissions')->insertGetId([
            'user_id' => $userId,
            'question_id' => $questionId,
            'query_id' => $queryId,
            'status' => $isCorrect ? 'true' : 'false', // <-- tambahkan ini
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Redirect ke soal yang sama dengan pesan benar/salah
        $status = $isCorrect ? 'Jawaban Anda BENAR!' : 'Jawaban Anda SALAH!';
        return redirect()->route('showTopicDetail', [
            'mysqlid' => $mysqlid,
            'start' => $start,
            'q' => $questionIndex,
        ])->with('answer_status', $status);
    }
}

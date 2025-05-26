<?php

namespace App\Http\Controllers\MySQL;

use App\Http\Controllers\Controller;
use App\Models\MySQL\MySqlTopicDetails;
use App\Models\MySQL\MySqlTopics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MysqlStudentController extends Controller
{
    public function showTopicDetail(Request $request)
    {
        $mysqlid = (int) $request->get('mysqlid');
        $start = (int) $request->get('start');
        $output = $request->get('output', '');
        $answerStatus = session('answer_status');

        $userId = Auth::user()->id;
        $progressPercent = 0; // Anda bisa buat logika progress baru jika perlu

        // Ambil detail subtopik
        $detail = MySqlTopicDetails::findOrFail($start);

        // Ambil submission terakhir user pada subtopik ini
        $lastSubmission = DB::table('mysql_student_submissions')
            ->where('user_id', $userId)
            ->where('topic_detail_id', $start)
            ->orderByDesc('created_at')
            ->first();

        $lastAnswer = '';
        $lastStatus = null;
        if ($lastSubmission) {
            $lastQuery = DB::table('mysql_queries')->where('id', $lastSubmission->query_id)->first();
            $lastAnswer = $lastQuery ? $lastQuery->query : '';
            $lastStatus = $lastSubmission->status ?? null;
        }

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
            'answerStatus' => $answerStatus,
            'lastAnswer' => $lastAnswer,
            'lastStatus' => $lastStatus,
            'detail' => $detail,
            'progressPercent' => $progressPercent,
        ]);
    }

    public function submitUserInput(Request $request)
    {
        $request->validate([
            'userInput' => 'required|string|max:255',
            'topic_detail_id' => 'required|integer',
            'mysqlid' => 'required|integer',
            'start' => 'required|integer',
        ]);

        $userInput = trim($request->input('userInput'));
        $topicDetailId = $request->input('topic_detail_id');
        $userId = Auth::user()->id;

        // Simpan query ke file
        file_put_contents(base_path('tests/query_user.sql'), $userInput);

        // 1. Simpan query ke mysql_queries
        $queryId = DB::table('mysql_queries')->insertGetId([
            'query' => $userInput,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Jalankan Codeception (pastikan query user bisa diakses oleh UserQueryCest)
        // Kirim query_id sebagai environment variable
        $projectPath = base_path();
        $codeceptPath = $projectPath . '\\vendor\\bin\\codecept.bat';
        $command = "cd /d \"{$projectPath}\" && set QUERY_ID={$queryId} && \"{$codeceptPath}\" run acceptance UserQueryCest:testUserQuery --env testing 2>&1";
        Log::info("Codeception command: " . $command);
        $testResult = shell_exec($command);

        // Debug: log atau tampilkan hasil
        if ($testResult === null || trim($testResult) === '') {
            return back()->with('answer_status', "Codeception tidak berjalan. Cek perintah: $command");
        }

        Log::info("Codeception output: " . $testResult);

        // 3. Simpan feedback ke mysql_feedbacks
        if ($testResult) {
            $feedbackId = DB::table('mysql_feedbacks')->insertGetId([
                'query_id' => $queryId,
                'feedback' => $testResult,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            // Handle error, misal tampilkan pesan gagal testing
            return back()->with('answer_status', 'Gagal menjalankan pengujian query.');
        }

        // 4. Simpan ke mysql_student_submissions
        $status = (strpos($testResult, 'OK (1 test') !== false &&
            stripos($testResult, 'error') === false &&
            stripos($testResult, 'Exception') === false) ? 'true' : 'false';

        DB::table('mysql_student_submissions')->insert([
            'user_id' => $userId,
            'topic_detail_id' => $topicDetailId,
            'query_id' => $queryId,
            'feedback_id' => $feedbackId,
            'status' => $status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Jika status salah, hapus data yang baru saja dimasukkan user di database testing
        if ($status === 'false') {
            // Contoh: hapus data dari tabel mk dengan kode_mk yang baru saja di-insert user
            // Anda bisa parsing $userInput untuk mendapatkan nilai yang di-insert, atau simpan nilai insert di session/variabel
            try {
                // Contoh sederhana untuk kasus INSERT INTO mk (kode_mk, nama_mk) VALUES ('02010', 'Basis Data');
                // Anda bisa gunakan regex atau parser sederhana untuk mengambil nilai kode_mk
                if (preg_match("/INSERT INTO mk.*VALUES\s*\(\s*'([^']+)'/", $userInput, $matches)) {
                    $kode_mk = $matches[1];
                    DB::connection('mysql_testing')->table('mk')->where('kode_mk', $kode_mk)->delete();
                }
            } catch (\Exception $e) {
                Log::error('Gagal menghapus data mk dari database testing: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('answer_status', $testResult);
    }

    public function getStudentProgressByTopic($userId, $topicId)
    {
        // Ambil semua id subtopik pada topik ini
        $subtopicIds = DB::table('mysql_topic_details')
            ->where('topic_id', $topicId)
            ->pluck('id');

        // Hitung total soal pada semua subtopik topik ini
        $totalQuestions = DB::table('mysql_questions')
            ->whereIn('topic_detail_id', $subtopicIds)
            ->count();

        // Hitung jumlah jawaban benar user pada semua soal topik ini
        $correctAnswers = DB::table('mysql_student_submissions')
            ->where('user_id', $userId)
            ->where('status', 'true')
            ->whereIn('question_id', function ($query) use ($subtopicIds) {
                $query->select('id')
                    ->from('mysql_questions')
                    ->whereIn('topic_detail_id', $subtopicIds);
            })
            ->distinct('question_id')
            ->count('question_id');

        $progressPercent = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;

        return $progressPercent;
    }
}

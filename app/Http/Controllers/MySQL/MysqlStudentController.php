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
        $progressPercent = $this->getStudentProgressByTopic($userId, $mysqlid);

        // Ambil detail subtopik
        $detail = MySqlTopicDetails::findOrFail($start);

        $page = (int) $request->get('page', 1); // halaman aktif, default 1
        $totalAnswer = $detail->total_answer;

        // Ambil submission terakhir untuk nomor ke-(page)
        $submission = DB::table('mysql_student_submissions')
            ->where('user_id', $userId)
            ->where('topic_detail_id', $start)
            ->where('answer_number', $page) // pastikan field answer_number ada dan diisi saat submit
            ->orderByDesc('id')
            ->first();

        $lastAnswer = '';
        $lastStatus = null;
        $lastSubmission = $submission;
        if ($submission) {
            $lastQuery = DB::table('mysql_queries')->where('id', $submission->query_id)->first();
            $lastAnswer = $lastQuery ? $lastQuery->query : '';
            $lastStatus = $submission->status ?? null;
        }

        $results = DB::select("select * from mysql_topic_details where topic_id = $mysqlid and id ='$start' ");
        $rows = DB::table('mysql_topic_details')->where('topic_id', $mysqlid)->get();
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

        if ($request->ajax()) {
            return view('mysql_dml.student.material._answer_section', compact(
                'detail',
                'topics',
                'topicsNavbar',
                'mysqlid',
                'html_start',
                'pdf_reader',
                'topicsCount',
                'detailCount',
                'output',
                'roleTeacher',
                'answerStatus',
                'lastAnswer',
                'lastStatus',
                'lastSubmission',
                'progressPercent',
                'totalAnswer',
                'page'
            ));
        }

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
            'lastSubmission' => $lastSubmission,
            'detail' => $detail,
            'progressPercent' => $progressPercent,
            'totalAnswer' => $totalAnswer,
            'page' => $page,
            'rows' => $rows,
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
        $answerNumber = $request->input('answer_number', 1);

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
        $shortFeedback = $this->getShortFeedback($testResult);
        $feedbackId = DB::table('mysql_feedbacks')->insertGetId([
            'query_id' => $queryId,
            'feedback' => $shortFeedback,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Simpan ke mysql_student_submissions
        $status = 'false';
        if (
            strpos($testResult, 'OK (1 test') !== false &&
            stripos($testResult, 'error') === false &&
            stripos($testResult, 'Exception') === false &&
            stripos($testResult, 'SQLSTATE') === false // tambahkan pengecekan SQLSTATE
        ) {
            $status = 'true';
        }

        DB::table('mysql_student_submissions')->insert([
            'user_id' => $userId,
            'topic_detail_id' => $topicDetailId,
            'query_id' => $queryId,
            'feedback_id' => $feedbackId,
            'status' => $status,
            'answer_number' => $answerNumber, // tambahkan ini
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // HAPUS DATA KETIKA STATUS QUERY SALAH. HAL INI UNTUK MENGHINDARI KESALAHAN PADA DATABASE TESTING
        // TETAPI KETIKA USER INPUT QUERY (NO 3) YANG SAMA DENGAN JAWABAN SEBELUMNYA (NO 2) AKAN TERJADI ERROR DUPLIKAT
        // KEMUDIAN DATA OTOMATIS AKAN DIHAPUS, DIMANA HAL INI SAMA SAJA MENGHAPUS HASIL QUERY SEBELUMNYA (NO 2)

        // Jika status salah, hapus data yang baru saja dimasukkan user di database testing
        // if ($status === 'false') {
        //     // Contoh: hapus data dari tabel mk dengan kode_mk yang baru saja di-insert user
        //     // Anda bisa parsing $userInput untuk mendapatkan nilai yang di-insert, atau simpan nilai insert di session/variabel
        //     try {
        //         // Contoh sederhana untuk kasus INSERT INTO mk (kode_mk, nama_mk) VALUES ('02010', 'Basis Data');
        //         // Anda bisa gunakan regex atau parser sederhana untuk mengambil nilai kode_mk
        //         if (preg_match("/INSERT INTO mk.*VALUES\s*\(\s*'([^']+)'/", $userInput, $matches)) {
        //             $kode_mk = $matches[1];
        //             DB::connection('mysql_testing')->table('mk')->where('kode_mk', $kode_mk)->delete();
        //         }
        //     } catch (\Exception $e) {
        //         Log::error('Gagal menghapus data mk dari database testing: ' . $e->getMessage());
        //     }
        // }

        return redirect()->route('showTopicDetail', [
            'mysqlid' => $request->input('mysqlid'),
            'start' => $request->input('start'),
            'page' => $request->input('answer_number', 1)
        ])->with('answer_status', $testResult);
    }

    private function getShortFeedback($testResult)
    {
        // Hilangkan karakter escape ANSI (warna terminal dsb)
        $testResult = preg_replace('/\e\[[\d;]*m/', '', $testResult);

        // Jika benar, ambil baris mengandung "OK (1 test"
        if (strpos($testResult, 'OK (1 test') !== false) {
            if (preg_match('/OK \(1 test, 0 assertions\)/', $testResult, $okMatch)) {
                return $okMatch[0];
            }
            return 'OK (1 test, 0 assertions)';
        }

        // Jika salah, ambil hanya pesan error SQLSTATE yang penting
        $lines = explode("\n", $testResult);
        $errorMsg = '';
        $summary = '';

        foreach ($lines as $line) {
            // Cari baris yang mengandung SQLSTATE
            if (preg_match('/SQLSTATE\[[^\]]+\]:.*$/', $line, $matches)) {
                $errorMsg = trim($matches[0]);
            }
            // Cari baris summary
            if (preg_match('/Tests:\s*\d+,\s*Assertions:\s*\d+,\s*Errors:\s*\d+\./', $line, $sumMatch)) {
                $summary = $sumMatch[0];
            }
            if ($errorMsg && $summary) break;
        }

        // Jika tidak ketemu, fallback ke pesan error lain yang mengandung "syntax error"
        if (!$errorMsg) {
            foreach ($lines as $line) {
                if (stripos($line, 'syntax error') !== false) {
                    $errorMsg = trim($line);
                    break;
                }
            }
        }

        // Jika masih tidak ketemu, ambil baris error pertama yang tidak kosong
        if (!$errorMsg) {
            foreach ($lines as $line) {
                if (trim($line) !== '') {
                    $errorMsg = trim($line);
                    break;
                }
            }
        }

        // Gabungkan errorMsg dan summary jika ada
        if ($summary) {
            return $errorMsg . '<br>' . $summary;
        }
        return $errorMsg;
    }

    public function getStudentProgressByTopic($userId, $topicId)
    {
        // Ambil semua id subtopik pada topik ini
        $subtopicIds = DB::table('mysql_topic_details')
            ->where('topic_id', $topicId)
            ->pluck('id');

        // Hitung total expected answer (total_answer) pada semua subtopik
        $totalAnswer = DB::table('mysql_topic_details')
            ->where('topic_id', $topicId)
            ->sum('total_answer');

        // Hitung jumlah submission status=true pada semua subtopik topik ini
        $correctSubmissions = DB::table('mysql_student_submissions')
            ->where('user_id', $userId)
            ->where('status', 'true')
            ->whereIn('topic_detail_id', $subtopicIds)
            ->count();

        // Hitung persentase progress
        $progressPercent = $totalAnswer > 0 ? round(($correctSubmissions / $totalAnswer) * 100) : 0;

        return $progressPercent;
    }

    public function runUserSelectQuery(Request $request)
    {
        $request->validate([
            'userSelectQuery' => 'required|string|max:255',
        ]);
        $query = trim($request->input('userSelectQuery'));

        // Hanya izinkan SELECT
        if (!preg_match('/^select\s+/i', $query)) {
            $html = '<div style="
                background-color: #f8d7da;
                color: #ff0000;
                border-radius: 0.5rem;
                display: inline-block;
                font-size: 14px;
                padding: 8px 10px;
                margin-bottom: 16px;
            ">Only <b>SELECT</b> queries are allowed!</div>';
            if ($request->ajax()) {
                return response()->json(['html' => $html]);
            }
            return back()->with('query_result', $html)->withInput();
        }

        try {
            $results = DB::connection('mysql_testing')->select($query);

            if (empty($results)) {
                $html = '<div style="
                    background-color: #f8d7da;
                    color: #ff0000;
                    border-radius: 0.5rem;
                    display: inline-block;
                    font-size: 14px;
                    padding: 8px 10px;
                    margin-bottom: 16px;
                ">Data Not found.</div>';
            } else {
                $columns = array_keys((array)$results[0]);
                $html = <<<HTML
                <style>
                    .iclop-table-custom thead th {
                        background: #288cff !important;
                        color: #ffffff !important;
                        font-weight: bold !important;
                        text-align: center !important;
                        white-space: nowrap;
                    }
                    .iclop-table-custom tbody tr:hover td {
                        background: #e3f0fb !important;
                        transition: background 0.2s;
                    }
                    .iclop-table-custom td, .iclop-table-custom th {
                        border: 1px solid #288cff !important;
                        white-space: nowrap;
                    }
                    .iclop-table-scroll {
                        max-height: 350px;
                        overflow-y: auto;
                        overflow-x: auto;
                        margin: 20px auto;
                        display: flex;
                        justify-content: center;
                        width: fit-content;
                        max-width: 100%;
                    }
                </style>
                <div class="iclop-table-scroll">
                <table class="iclop-table-custom" style="
                    width: auto;
                    min-width: 100px;
                    border-spacing:0;
                    border-radius:5px;
                    overflow:hidden;
                    background:#fff;
                    margin: 0 auto;
                ">
                    <thead>
                        <tr>
                HTML;
                foreach ($columns as $col) {
                    $html .= '<th style="padding:8px 14px;">' . htmlspecialchars($col) . '</th>';
                }
                $html .= '</tr></thead><tbody>';
                foreach ($results as $row) {
                    $html .= '<tr>';
                    foreach ($columns as $col) {
                        $html .= '<td style="padding:7px 14px; color:#222; background:#fff;">' . htmlspecialchars($row->$col) . '</td>';
                    }
                    $html .= '</tr>';
                }
                $html .= '</tbody></table></div>';
            }
        } catch (\Exception $e) {
            $html = '<div style="
                max-width: 85%;
                background-color: #f8d7da;
                color: #ff0000;
                border-radius: 0.5rem;
                display: inline-block;
                font-size: 14px;
                padding: 8px 10px;
                margin-bottom: 16px;
            ">Query error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }

        if ($request->ajax()) {
            return response()->json(['html' => $html]);
        }
        return back()->with('query_result', $html)->withInput();
    }

    public function getStudentProgressAjax(Request $request)
    {
        $userId = Auth::user()->id;
        $mysqlid = (int) $request->get('mysqlid');
        $progressPercent = $this->getStudentProgressByTopic($userId, $mysqlid);
        return response()->json(['progress' => $progressPercent]);
    }

    // Method untuk mengambil data sidebar checklist
    public function sidebarAjax(Request $request)
    {
        $mysqlid = $request->get('mysqlid');
        $detailId = $request->get('start');
        $progressPercent = $this->getStudentProgressByTopic(Auth::id(), $mysqlid);
        $detailCount = DB::table('mysql_topic_details')->where('topic_id', $mysqlid)->count();
        $detail = DB::table('mysql_topic_details')->where('id', $detailId)->first();
        $pdf_reader = 0; // atau sesuai kebutuhan
        $html_start = ''; // atau sesuai kebutuhan

        $rows = DB::table('mysql_topic_details')->where('topic_id', $mysqlid)->get();

        return view('mysql_dml.student.material.sidebar', compact(
            'mysqlid',
            'detail',
            'progressPercent',
            'detailCount',
            'pdf_reader',
            'html_start',
            'rows',
            'detailId'
        ))->render();
    }

    public function importSqlData(Request $request)
    {
        // Path file SQL (misal: database/iclop_v2_testing UPDATE.sql)
        $sqlFile = base_path('database/iclop_v2_testing UPDATE.sql');
        $sql = file_get_contents($sqlFile);

        // Jalankan SQL ke database testing
        try {
            DB::connection('mysql_testing')->unprepared($sql);
            return response()->json(['success' => true, 'message' => 'Data berhasil diimport!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}

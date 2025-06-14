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
        $totalAnswer = $detail->total_question;

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
        $countdownSeconds = $topicsNavbar->countdown_seconds ?? 3600;

        // Ambil waktu mulai dari tabel mysql_student_topic_times
        $topicTime = DB::table('mysql_student_topic_times')
            ->where('user_id', $userId)
            ->where('topic_id', $mysqlid)
            ->first();

        if ($topicTime && $topicTime->started_at) {
            $elapsed = now()->diffInSeconds(\Carbon\Carbon::parse($topicTime->started_at));
            $sisaDetik = max(0, $countdownSeconds - $elapsed);
        } else {
            $sisaDetik = $countdownSeconds;
        }

        $isFinished = $topicTime && $topicTime->is_finished == 1;

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
            'countdownSeconds' => $sisaDetik,
            'isFinished' => $isFinished,
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

        $this->setupStudentTestingDatabase($userId);

        // Simpan query ke file
        // file_put_contents(base_path('tests/query_user.sql'), $userInput);
        $queryFile = base_path("tests/query_user_{$userId}.sql");
        file_put_contents($queryFile, $userInput);

        // 1. Simpan query ke mysql_queries
        $queryId = DB::table('mysql_queries')->insertGetId([
            'query' => $userInput,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Jalankan Codeception (pastikan query user bisa diakses oleh UserQueryCest)
        // Kirim query_id sebagai environment variable
        $acceptanceConfig = file_get_contents(base_path('tests/Acceptance.suite.yml'));
        $acceptanceConfig = str_replace(
            "dbname=iclop_v2_testing",
            "dbname=iclop_user_{$userId}",
            $acceptanceConfig
        );

        // Tambahkan blok namespace dan support_namespace di bagian atas jika belum ada
        if (strpos($acceptanceConfig, 'namespace: Tests') === false) {
            $acceptanceConfig = "namespace: Tests\nsupport_namespace: Support\n\n" . $acceptanceConfig;
        }

        // Tambahkan blok paths absolut jika belum ada
        $pathsBlock = <<<YML
        paths:
            tests: .
            output: _output
            data: Support/Data
            support: Support
            envs: _envs

        YML;

        if (strpos($acceptanceConfig, 'paths:') === false) {
            $acceptanceConfig .= "\n" . $pathsBlock;
        }

        file_put_contents(base_path("tests/acceptance_user_{$userId}.suite.yml"), $acceptanceConfig);

        $projectPath = base_path();
        $codeceptPath = $projectPath . '\\vendor\\bin\\codecept.bat';
        // $command = "cd /d \"{$projectPath}\\tests\" && set QUERY_ID={$queryId} && \"{$projectPath}\\vendor\\bin\\codecept.bat\" run acceptance_user_{$userId} acceptance/UserQueryCest:testUserQuery -c acceptance_user_{$userId}.suite.yml --env testing 2>&1";
        $command = "cd /d \"{$projectPath}\\tests\" && set USER_ID={$userId} && set QUERY_ID={$queryId} && \"{$projectPath}\\vendor\\bin\\codecept.bat\" run acceptance_user_{$userId} acceptance/UserQueryCest:testUserQuery -c acceptance_user_{$userId}.suite.yml --env testing 2>&1";

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
        ])->with('answer_status', $shortFeedback);
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

        $lines = explode("\n", $testResult);
        $extraFeedback = [];
        $hasWhereError = false;

        foreach ($lines as $line) {
            $line = trim($line);

            // Tambahkan filter untuk pesan error baru (EN)
            if (stripos($line, 'The WHERE condition must have a clear comparison operator or condition') !== false) {
                $hasWhereError = true;
                $extraFeedback[] = $line;
            }
            // Tambahkan filter untuk pesan error lain jika perlu
            if (
                stripos($line, 'The number of columns and values in the INSERT statement must be the same') !== false ||
                stripos($line, 'Call to undefined method') !== false ||
                stripos($line, 'Undefined variable') !== false
            ) {
                $extraFeedback[] = $line;
            }
        }
        // --- END Tambahan ---

        // Jika salah, ambil hanya pesan error SQLSTATE yang penting dan Summary
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

        // Gabungkan feedback tambahan dan feedback lama
        $allFeedback = [];
        if (!empty($extraFeedback)) {
            $allFeedback[] = implode('<br>', $extraFeedback);
        }
        if ($errorMsg) {
            $allFeedback[] = $errorMsg;
        }
        if ($summary) {
            $allFeedback[] = $summary;
        }

        return implode('<br>', $allFeedback);
    }

    public function getStudentProgressByTopic($userId, $topicId)
    {
        // Ambil semua id subtopik pada topik ini
        $subtopicIds = DB::table('mysql_topic_details')
            ->where('topic_id', $topicId)
            ->pluck('id');

        // Hitung total expected answer (total_question) pada semua subtopik
        $totalAnswer = DB::table('mysql_topic_details')
            ->where('topic_id', $topicId)
            ->sum('total_question');

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
        $userId = Auth::user()->id;
        $this->setupStudentTestingDatabase($userId);

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
        $userId = Auth::user()->id;
        $this->setupStudentTestingDatabase($userId);

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

    public function resetTestingDatabase(Request $request)
    {
        $userId = Auth::id();
        $mysqlid = $request->get('mysqlid'); // pastikan dikirim dari AJAX
        
        // Nama database user
        $dbName = "iclop_user_" . $userId;

        // 1. Drop database user saja, tanpa create ulang atau import template
        try {
            DB::statement("DROP DATABASE IF EXISTS `$dbName`");
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal drop database: ' . $e->getMessage()]);
        }

        // 2. Hapus file acceptance_user dan query_user milik user
        $suiteFile = base_path("tests/acceptance_user_{$userId}.suite.yml");
        $queryFile = base_path("tests/query_user_{$userId}.sql");
        if (file_exists($suiteFile)) {
            @unlink($suiteFile);
        }
        if (file_exists($queryFile)) {
            @unlink($queryFile);
        }

        // 3. Simpan status reset
        DB::table('mysql_user_reset')->updateOrInsert(
            ['user_id' => $userId, 'topic_id' => $mysqlid],
            ['is_reset' => true]
        );

        return response()->json(['success' => true, 'message' => 'Database testing berhasil dihapus!']);
    }

    public function enrollTopic(Request $request)
    {
        $userId = Auth::id();
        $topicId = $request->input('mysqlid');
        $now = now();

        //Panggil setup database testing
        $this->setupStudentTestingDatabase($userId);

        $existing = DB::table('mysql_student_topic_times')
            ->where('user_id', $userId)
            ->where('topic_id', $topicId)
            ->first();

        // Jangan buat sesi baru jika sudah selesai
        if (!$existing) {
            DB::table('mysql_student_topic_times')->insert([
                'user_id' => $userId,
                'topic_id' => $topicId,
                'started_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
                'is_finished' => 0,
            ]);
        }
        // Jika sudah selesai, tidak perlu insert apa-apa

        return response()->json(['success' => true]);
    }

    public function finishTopic(Request $request)
    {
        $userId = Auth::id();
        $topicId = $request->input('mysqlid');
        $now = now();

        $topicTime = DB::table('mysql_student_topic_times')
            ->where('user_id', $userId)
            ->where('topic_id', $topicId)
            ->first();

        if ($topicTime && $topicTime->started_at && !$topicTime->duration_seconds) {
            $duration = $now->diffInSeconds(\Carbon\Carbon::parse($topicTime->started_at));
            DB::table('mysql_student_topic_times')
                ->where('id', $topicTime->id)
                ->update([
                    'duration_seconds' => $duration,
                    'is_finished' => 1, // <-- Tandai selesai
                    'updated_at' => $now
                ]);
        }

        return response()->json(['success' => true]);
    }

    private function setupStudentTestingDatabase($userId)
    {
        $dbName = "iclop_user_" . $userId;
        $dbUser = env('DB_TESTING_USERNAME', 'root');
        $dbPass = env('DB_TESTING_PASSWORD', '');
        $dbHost = env('DB_TESTING_HOST', '127.0.0.1');
        $dbPort = env('DB_TESTING_PORT', '3306');
        $templatePath = base_path('database/iclopTemplate.sql');

        // 1. Buat database jika belum ada
        DB::statement("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        // 2. Import template SQL jika tabel utama belum ada
        $tables = DB::select("SHOW TABLES FROM `$dbName`");
        if (count($tables) == 0) {
            $importCmd = "mysql -h $dbHost -P $dbPort -u $dbUser " . ($dbPass ? "-p\"$dbPass\"" : "") . " $dbName < \"$templatePath\"";
            shell_exec($importCmd);
        }

        // 3. Set koneksi dinamis ke database ini
        config(['database.connections.mysql_testing.database' => $dbName]);
        DB::purge('mysql_testing');
        DB::reconnect('mysql_testing');
    }
}

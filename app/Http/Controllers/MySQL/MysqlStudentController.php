<?php

namespace App\Http\Controllers\MySQL;

use App\Http\Controllers\Controller;
use App\Models\MySQL\MySqlTopicDetails;
use App\Models\MySQL\MySqlTopics;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpMyAdmin\SqlParser\Parser;
use PhpMyAdmin\SqlParser\Statements\DeleteStatement;
use PhpMyAdmin\SqlParser\Statements\InsertStatement;
use PhpMyAdmin\SqlParser\Statements\UpdateStatement;

class MysqlStudentController extends Controller
{
    public function showTopicDetail(Request $request)
    {
        $mysqlid = (int) $request->get('mysqlid');
        $start = (int) $request->get('start');
        $output = $request->get('output', '');
        $answerStatus = session('answer_status');
        $userId = Auth::user()->id;

        $detail = MySqlTopicDetails::findOrFail($start);
        $page = (int) $request->get('page', 1);
        $totalAnswer = $detail->total_question;

        $topics = MySqlTopics::all()->map(function ($topic) {
            $topic->has_schema = $topic->schema_file_name && $topic->schema_file_path ? true : false;
            return $topic;
        });
        $topicsNavbar = MySqlTopics::findOrFail($mysqlid);
        $countdownSeconds = $topicsNavbar->countdown_seconds ?? 3600;

        // 1. Ambil sesi enroll aktif
        $topicTime = DB::table('mysql_student_topic_times')
            ->where('user_id', $userId)
            ->where('topic_id', $mysqlid)
            ->where('is_finished', 0)
            ->orderByDesc('id')
            ->first();

        $isFinished = false;
        $sisaDetik = $countdownSeconds;

        // 2. Jika ada sesi aktif, cek waktu habis
        if ($topicTime && $topicTime->started_at) {
            $elapsed = now()->diffInSeconds(\Carbon\Carbon::parse($topicTime->started_at));
            $sisaDetik = max(0, $countdownSeconds - $elapsed);

            if ($sisaDetik <= 0 && $topicTime->is_finished == 0) {
                // Tandai sesi selesai
                DB::table('mysql_student_topic_times')
                    ->where('id', $topicTime->id)
                    ->update([
                        'duration_seconds' => $countdownSeconds,
                        'is_finished' => 1,
                        'updated_at' => now()
                    ]);
                DB::statement("DROP DATABASE IF EXISTS iclop_user_$userId");
                DB::table('mysql_user_reset')->updateOrInsert(
                    ['user_id' => $userId, 'topic_id' => $mysqlid],
                    ['is_reset' => true]
                );
                // Buat sesi baru
                $this->setupStudentTestingDatabase($userId);
                $now = now();
                DB::table('mysql_student_topic_times')->insert([
                    'user_id' => $userId,
                    'topic_id' => $mysqlid,
                    'started_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                    'is_finished' => 0,
                ]);
                DB::table('mysql_user_reset')->updateOrInsert(
                    ['user_id' => $userId, 'topic_id' => $mysqlid],
                    ['is_reset' => 0]
                );
                $sisaDetik = $countdownSeconds;
            }
        }

        // 3. Jika tidak ada sesi aktif, buat sesi baru
        $activeEnroll = DB::table('mysql_student_topic_times')
            ->where('user_id', $userId)
            ->where('topic_id', $mysqlid)
            ->where('is_finished', 0)
            ->orderByDesc('id')
            ->first();
        if (!$activeEnroll) {
            $this->setupStudentTestingDatabase($userId);
            $now = now();
            DB::table('mysql_student_topic_times')->insert([
                'user_id' => $userId,
                'topic_id' => $mysqlid,
                'started_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
                'is_finished' => 0,
            ]);
            DB::table('mysql_user_reset')->updateOrInsert(
                ['user_id' => $userId, 'topic_id' => $mysqlid],
                ['is_reset' => 0]
            );
            $sisaDetik = $countdownSeconds;
        }

        // 4. Ambil ulang enroll aktif terbaru
        $enroll = DB::table('mysql_student_topic_times')
            ->where('user_id', $userId)
            ->where('topic_id', $mysqlid)
            ->where('is_finished', 0)
            ->orderByDesc('id')
            ->first();
        $enrollId = $enroll ? $enroll->id : null;

        // 5. Ambil progress dan jawaban dari enrollId terbaru
        $progressPercent = $this->getStudentProgressByEnroll($userId, $mysqlid, $enrollId);

        $submission = DB::table('mysql_student_submissions')
            ->where('user_id', $userId)
            ->where('topic_detail_id', $start)
            ->where('answer_number', $page)
            ->where('enroll_id', $enrollId)
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
                'page',
                'enrollId'
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
            'enrollId' => $enrollId,
        ]);
    }

    public function getStudentProgressByEnroll($userId, $topicId, $enrollId)
    {
        // Ambil semua id subtopik pada topik ini
        $subtopicIds = DB::table('mysql_topic_details')
            ->where('topic_id', $topicId)
            ->pluck('id');

        // Hitung total expected answer (total_question) pada semua subtopik
        $totalAnswer = DB::table('mysql_topic_details')
            ->where('topic_id', $topicId)
            ->sum('total_question');

        // Hitung jumlah submission status=true pada semua subtopik topik ini dan enroll aktif
        $correctSubmissions = DB::table('mysql_student_submissions')
            ->where('user_id', $userId)
            ->where('status', 'true')
            ->whereIn('topic_detail_id', $subtopicIds)
            ->where('enroll_id', $enrollId)
            ->count();

        // Hitung persentase progress
        $progressPercent = $totalAnswer > 0 ? round(($correctSubmissions / $totalAnswer) * 100) : 0;

        return $progressPercent;
    }

    public function enrollTopic(Request $request)
    {
        $userId = Auth::id();
        $topicId = $request->input('mysqlid');
        $now = now();

        // Cek sesi enroll aktif (belum selesai)
        $activeEnroll = DB::table('mysql_student_topic_times')
            ->where('user_id', $userId)
            ->where('topic_id', $topicId)
            ->where('is_finished', 0)
            ->orderByDesc('id')
            ->first();

        // Cek status reset
        $userReset = DB::table('mysql_user_reset')
            ->where('user_id', $userId)
            ->where('topic_id', $topicId)
            ->value('is_reset');

        if ($activeEnroll && !$userReset) {
            // Sesi aktif, tidak perlu create database/enroll baru
            return response()->json(['success' => true, 'enroll_id' => $activeEnroll->id]);
        } else {
            // Sesi tidak aktif, buat database testing baru & enroll baru
            $this->setupStudentTestingDatabase($userId);

            $enrollId = DB::table('mysql_student_topic_times')->insertGetId([
                'user_id' => $userId,
                'topic_id' => $topicId,
                'started_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
                'is_finished' => 0,
            ]);

            // Reset status user_reset
            DB::table('mysql_user_reset')->updateOrInsert(
                ['user_id' => $userId, 'topic_id' => $topicId],
                ['is_reset' => 0]
            );

            return response()->json(['success' => true, 'enroll_id' => $enrollId]);
        }
    }

    private function setupStudentTestingDatabase($userId)
    {
        $dbName = "iclop_user_" . $userId;
        $dbUser = env('DB_TESTING_USERNAME', 'root');
        $dbPass = env('DB_TESTING_PASSWORD', '');
        $dbHost = env('DB_TESTING_HOST', '127.0.0.1');
        $dbPort = env('DB_TESTING_PORT', '3306');
        $topicId = request()->input('mysqlid');
        $topic = MySqlTopics::find($topicId);

        // Hanya gunakan file schema dari dosen (yang sudah diupload)
        if ($topic && $topic->schema_file_name && $topic->schema_file_path) {
            // Jika path diawali 'public/' atau 'mysql/schema/', gunakan public_path
            if (
                str_starts_with($topic->schema_file_path, 'public/') ||
                str_starts_with($topic->schema_file_path, 'mysql/schema/')
            ) {
                $relativePath = ltrim(str_replace('public/', '', $topic->schema_file_path), '/\\');
                $templatePath = public_path($relativePath . $topic->schema_file_name);
            } else {
                // Jika path tidak valid, tolak
                throw new Exception('Invalid schema file path for this topic.');
            }
        } else {
            // Tidak ada schema, hentikan proses atau lempar error
            throw new Exception('Schema database untuk topik ini belum tersedia.');
        }

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

        // Panggil setup database user sebelum transaksi
        $this->setupStudentTestingDatabase($userId);

        // Ambil enroll aktif
        $enroll = DB::table('mysql_student_topic_times')
            ->where('user_id', $userId)
            ->where('topic_id', $request->input('mysqlid'))
            ->where('is_finished', 0)
            ->orderByDesc('id')
            ->first();
        $enrollId = $enroll ? $enroll->id : null;

        // Simpan query ke file
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
        $command = "cd /d \"{$projectPath}\\tests\" && set USER_ID={$userId} && set QUERY_ID={$queryId} && \"{$projectPath}\\vendor\\bin\\codecept.bat\" run acceptance_user_{$userId} acceptance/UserQueryCest:testUserQuery -c acceptance_user_{$userId}.suite.yml --env testing 2>&1";

        Log::info("Codeception command: " . $command);
        $testResult = shell_exec($command);

        Log::info("Codeception output: " . $testResult);

        // Debug: log atau tampilkan hasil
        if ($testResult === null || trim($testResult) === '') {
            return back()->with('answer_status', "Codeception tidak berjalan. Cek perintah: $command");
        }

        Log::info("Codeception output: " . $testResult);

        // 3. Simpan feedback ke mysql_feedbacks
        $validationError = null;
        $shortFeedback = $this->getShortFeedback($testResult);
        $feedbackId = DB::table('mysql_feedbacks')->insertGetId([
            'query_id' => $queryId,
            'feedback' => $shortFeedback,
            'validation_error' => $validationError,
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

        if ($status === 'true') {
            $expected = DB::table('mysql_expected_queries')
                ->where('topic_detail_id', $topicDetailId)
                ->where('answer_number', $answerNumber)
                ->first();

            if ($expected) {
                // --- Tambahan validasi struktur query ---
                if (!$this->isQueryStructureEquivalent($userInput, $expected->expected_query)) {
                    $status = 'false';
                    $validationError = 'Struktur query Anda tidak sesuai dengan kunci jawaban.';
                } else {
                    try {
                        // --- 1. Jalankan query user dalam transaksi, ambil hasil, rollback ---
                        DB::connection('mysql_testing')->beginTransaction();
                        try {
                            DB::connection('mysql_testing')->statement($userInput);
                            $studentResult = DB::connection('mysql_testing')->select("SELECT * FROM {$expected->expected_table}");
                            DB::connection('mysql_testing')->rollBack();
                            $validationError = null;
                        } catch (\Exception $e) {
                            DB::connection('mysql_testing')->rollBack();
                            $status = 'false';
                            $validationError = $e->getMessage(); // Simpan pesan error MySQL di sini!
                        }

                        // Jika terjadi error pada eksekusi query user, langsung simpan feedback dan return
                        if ($status === 'false') {
                            // Simpan feedback ke mysql_feedbacks
                            $shortFeedback = $this->getShortFeedback($testResult);
                            $feedbackId = DB::table('mysql_feedbacks')->insertGetId([
                                'query_id' => $queryId,
                                'feedback' => $shortFeedback,
                                'validation_error' => $validationError,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                            // Simpan ke mysql_student_submissions
                            DB::table('mysql_student_submissions')->insert([
                                'user_id' => $userId,
                                'enroll_id' => $enrollId,
                                'topic_detail_id' => $topicDetailId,
                                'query_id' => $queryId,
                                'feedback_id' => $feedbackId,
                                'status' => $status,
                                'answer_number' => $answerNumber,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                            return redirect()->route('showTopicDetail', [
                                'mysqlid' => $request->input('mysqlid'),
                                'start' => $request->input('start'),
                                'page' => $request->input('answer_number', 1)
                            ])->with('answer_status', $shortFeedback);
                        }

                        // --- 2. Jalankan query expected dalam transaksi, ambil hasil, rollback ---
                        DB::connection('mysql_testing')->beginTransaction();
                        DB::connection('mysql_testing')->statement($expected->expected_query);
                        $expectedResult = DB::connection('mysql_testing')->select("SELECT * FROM {$expected->expected_table}");
                        DB::connection('mysql_testing')->rollBack();

                        // --- 3. Normalisasi hasil ---
                        function normalizeResult($result)
                        {
                            $arr = array_map(function ($row) {
                                return (array) $row;
                            }, $result);
                            usort($arr, function ($a, $b) {
                                return strcmp($a['kode_mk'], $b['kode_mk']);
                            });
                            return $arr;
                        }
                        $studentResultNorm = normalizeResult($studentResult);
                        $expectedResultNorm = normalizeResult($expectedResult);

                        // --- 4. Bandingkan hasil ---
                        if ($studentResultNorm == $expectedResultNorm) {
                            // --- 5. Jalankan query user sekali lagi (commit) agar data benar-benar masuk ---
                            DB::connection('mysql_testing')->beginTransaction();
                            try {
                                DB::connection('mysql_testing')->statement($userInput);
                                DB::connection('mysql_testing')->commit();
                                $status = 'true';
                                $validationError = null;
                            } catch (\Exception $e) {
                                DB::connection('mysql_testing')->rollBack();
                                $status = 'false';
                                $validationError = $e->getMessage(); // Simpan pesan error MySQL
                            }
                        } else {
                            $status = 'false';
                            $validationError = null;
                        }
                    } catch (\Exception $e) {
                        DB::connection('mysql_testing')->rollBack();
                        $status = 'false';
                        $validationError = $e->getMessage(); // Simpan pesan error MySQL
                    }
                }
            }
        }

        // Tambahan validasi: hanya izinkan CREATE TABLE pada nomor yang memang DDL
        if (
            stripos($userInput, 'CREATE TABLE') !== false
        ) {
            // Hanya soal nomor 6 yang boleh CREATE TABLE
            if (!($answerNumber == 6 && isset($expected) && stripos($expected->expected_query, 'CREATE TABLE') !== false)) {
                $status = 'false';
            } else {
                // Jika memang soal DDL, cek apakah tabel berhasil dibuat
                $expectedTable = $expected->expected_table ?? null;
                if ($expectedTable) {
                    $tables = DB::connection('mysql_testing')->select("SHOW TABLES LIKE '$expectedTable'");
                    if (!empty($tables)) {
                        $status = 'true';
                    } else {
                        $status = 'false';
                    }
                }
            }
        }

        DB::table('mysql_student_submissions')->insert([
            'user_id' => $userId,
            'enroll_id' => $enrollId, // tambahkan ini!
            'topic_detail_id' => $topicDetailId,
            'query_id' => $queryId,
            'feedback_id' => $feedbackId,
            'status' => $status,
            'answer_number' => $answerNumber, // tambahkan ini
            'created_at' => now(),
            'updated_at' => now(),
        ]);

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

        // Jika benar, tampilkan hanya "OK (1 test)"
        if (strpos($testResult, 'OK (1 test') !== false) {
            return 'OK (1 test)';
        }

        // Jika salah, ambil error tanpa "Assertions"
        $lines = explode("\n", $testResult);
        $feedbackLines = [];
        foreach ($lines as $line) {
            $line = trim($line);
            // Hilangkan baris yang mengandung "Assertions"
            if (stripos($line, 'Assertions') !== false) {
                continue;
            }
            // Ambil baris error penting
            if (
                stripos($line, 'error') !== false ||
                stripos($line, 'Exception') !== false ||
                stripos($line, 'SQLSTATE') !== false ||
                stripos($line, 'syntax error') !== false
            ) {
                $feedbackLines[] = $line;
            }
        }
        // Jika tidak ada error khusus, tampilkan baris non-kosong selain assertions
        if (empty($feedbackLines)) {
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line !== '' && stripos($line, 'Assertions') === false) {
                    $feedbackLines[] = $line;
                }
            }
        }
        return implode('<br>', $feedbackLines);
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

    public function getStudentProgressByTopic($userId, $topicId)
    {
        // Ambil enroll aktif
        $enroll = DB::table('mysql_student_topic_times')
            ->where('user_id', $userId)
            ->where('topic_id', $topicId)
            ->where('is_finished', 0)
            ->orderByDesc('id')
            ->first();
        $enrollId = $enroll ? $enroll->id : null;

        // Ambil semua id subtopik pada topik ini
        $subtopicIds = DB::table('mysql_topic_details')
            ->where('topic_id', $topicId)
            ->pluck('id');

        // Hitung total expected answer (total_question) pada semua subtopik
        $totalAnswer = DB::table('mysql_topic_details')
            ->where('topic_id', $topicId)
            ->sum('total_question');

        // Hitung jumlah submission status=true pada semua subtopik topik ini dan enroll aktif
        $correctSubmissions = DB::table('mysql_student_submissions')
            ->where('user_id', $userId)
            ->where('status', 'true')
            ->whereIn('topic_detail_id', $subtopicIds)
            ->where('enroll_id', $enrollId)
            ->count();

        // Hitung persentase progress
        $progressPercent = $totalAnswer > 0 ? round(($correctSubmissions / $totalAnswer) * 100) : 0;

        return $progressPercent;
    }

    public function getStudentProgressAjax(Request $request)
    {
        $userId = Auth::user()->id;
        $mysqlid = (int) $request->get('mysqlid');
        $enroll = DB::table('mysql_student_topic_times')
            ->where('user_id', $userId)
            ->where('topic_id', $mysqlid)
            ->where('is_finished', 0)
            ->orderByDesc('id')
            ->first();
        $enrollId = $enroll ? $enroll->id : null;
        $progressPercent = $this->getStudentProgressByEnroll($userId, $mysqlid, $enrollId);
        return response()->json(['progress' => $progressPercent]);
    }

    // Method untuk mengambil data sidebar checklist
    public function sidebarAjax(Request $request)
    {
        $mysqlid = $request->get('mysqlid');
        $detailId = $request->get('start');
        $userId = Auth::id();

        // Ambil enroll aktif terbaru
        $enroll = DB::table('mysql_student_topic_times')
            ->where('user_id', $userId)
            ->where('topic_id', $mysqlid)
            ->where('is_finished', 0)
            ->orderByDesc('id')
            ->first();
        $enrollId = $enroll ? $enroll->id : null;

        // Pastikan progressPercent dihitung dari enrollId terbaru
        $progressPercent = $this->getStudentProgressByEnroll($userId, $mysqlid, $enrollId);

        $detailCount = DB::table('mysql_topic_details')->where('topic_id', $mysqlid)->count();
        $detail = DB::table('mysql_topic_details')->where('id', $detailId)->first();
        $pdf_reader = 0;
        $html_start = '';
        $rows = DB::table('mysql_topic_details')->where('topic_id', $mysqlid)->get();

        return view('mysql_dml.student.material.sidebar', compact(
            'mysqlid',
            'detail',
            'progressPercent',
            'detailCount',
            'pdf_reader',
            'html_start',
            'rows',
            'detailId',
            'enrollId'
        ))->render();
    }

    public function finishTopic(Request $request)
    {
        $userId = Auth::id();
        $topicId = $request->input('mysqlid');
        $now = now();

        // Ambil enroll aktif (belum selesai)
        $topicTime = DB::table('mysql_student_topic_times')
            ->where('user_id', $userId)
            ->where('topic_id', $topicId)
            ->where('is_finished', 0)
            ->orderByDesc('id')
            ->first();

        if ($topicTime && $topicTime->started_at && !$topicTime->duration_seconds) {
            $duration = $now->diffInSeconds(\Carbon\Carbon::parse($topicTime->started_at));
            DB::table('mysql_student_topic_times')
                ->where('id', $topicTime->id)
                ->update([
                    'duration_seconds' => $duration,
                    'is_finished' => 1,
                    'updated_at' => $now
                ]);

            // Tambahkan update is_reset di sini
            DB::table('mysql_user_reset')->updateOrInsert(
                ['user_id' => $userId, 'topic_id' => $topicId],
                ['is_reset' => true]
            );
        }

        return response()->json(['success' => true]);
    }

    public function resetTestingDatabase(Request $request)
    {
        $userId = Auth::id();
        $mysqlid = $request->get('mysqlid');

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

    private function isQueryStructureEquivalent($userQuery, $expectedQuery)
    {
        try {
            $userParser = new Parser($userQuery);
            $expectedParser = new Parser($expectedQuery);

            $userStmt = $userParser->statements[0];
            $expectedStmt = $expectedParser->statements[0];

            // Contoh: Untuk DELETE, cek tabel dan ada/tidaknya WHERE
            if ($userStmt instanceof DeleteStatement && $expectedStmt instanceof DeleteStatement) {
                // Cek nama tabel
                if ($userStmt->from[0]->table !== $expectedStmt->from[0]->table) {
                    return false;
                }
                // Cek ada/tidaknya WHERE
                $userHasWhere = !empty($userStmt->where);
                $expectedHasWhere = !empty($expectedStmt->where);
                if ($userHasWhere !== $expectedHasWhere) {
                    return false;
                }
                // Bisa tambahkan cek isi WHERE jika ingin lebih ketat
                // return true jika sudah cukup
                return true;
            }

            // Contoh: Untuk INSERT, cek tabel dan kolom
            if ($userStmt instanceof InsertStatement && $expectedStmt instanceof InsertStatement) {
                if ($userStmt->into->table !== $expectedStmt->into->table) {
                    return false;
                }
                // Cek kolom (urutan boleh berbeda)
                $userCols = array_map('strtolower', $userStmt->columns);
                $expectedCols = array_map('strtolower', $expectedStmt->columns);
                sort($userCols);
                sort($expectedCols);
                if ($userCols !== $expectedCols) {
                    return false;
                }
                return true;
            }

            // Contoh: Untuk UPDATE, cek tabel dan ada/tidaknya WHERE
            if ($userStmt instanceof UpdateStatement && $expectedStmt instanceof UpdateStatement) {
                if ($userStmt->table->table !== $expectedStmt->table->table) {
                    return false;
                }
                $userHasWhere = !empty($userStmt->where);
                $expectedHasWhere = !empty($expectedStmt->where);
                if ($userHasWhere !== $expectedHasWhere) {
                    return false;
                }
                return true;
            }

            // Untuk jenis query lain, bisa tambahkan sesuai kebutuhan

            // Default: fallback ke false
            return false;
        } catch (\Exception $e) {
            // Jika parsing gagal, anggap tidak sama
            return false;
        }
    }
}

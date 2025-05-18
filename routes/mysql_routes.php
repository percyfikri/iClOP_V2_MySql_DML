<?php

use App\Http\Controllers\React\ReactController;
use App\Http\Controllers\React\ReactDosenController;
use App\Http\Controllers\React\Student\ReactLogicalController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MySQL\MysqlController;
use App\Http\Controllers\MySQL\MysqlTeacherQuestionController;
use App\Http\Controllers\MySQL\MysqlTeacherTopicsController;
use App\Http\Controllers\MySQL\TopicDetailController;
use App\Http\Controllers\PHP\PHPController;
use App\Http\Controllers\PHP\PHPDosenController;
use App\Http\Controllers\PHP\Student\DashboardUnitControllers;
use App\Http\Controllers\PHP\Student\StudikasusController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

Route::group(['middleware' => ['auth']], function () {
    Route::prefix('mysql')->group(function () {
        //-----------------CHANGED-----------------
        Route::get('/start', [MysqlController::class, 'index'])->name('mysql_welcome');
        Route::get('/detail-topics', [MysqlController::class, 'mysql_material_detail'])->name('mysql_material_detail');
        Route::post('/submit_user_input', [MysqlController::class, 'submit_user_input'])->name('mysql_submit_user_input');
        //-----------------CHANGED-----------------

        // Route::get('/start', [ReactController::class, 'index'])->name('react_welcome');
        // Route::get('/detail-topics', [ReactController::class, 'php_material_detail'])->name('react_material_detail');
        Route::get('/php-admin', [ReactController::class, 'php_admin'])->name('php_admin');
        Route::post('/uploadimage', [ReactController::class, 'upload'])->name('uploadimage');
        Route::get('/send-task', [ReactController::class, 'send_task'])->name('send_task');
        Route::post('/session_progress', [ReactController::class, 'session_progress'])->name('session_progress');
        Route::post('/task/submission', [ReactController::class, 'task_submission'])->name('task_submission');
        Route::get('/result-task', [ReactController::class, 'result_task'])->name('result_task');
        Route::get('/result-test-student', [ReactController::class, 'result_test'])->name('phpunit.result-test-student');
        Route::any('/akhir-ujian', [ReactController::class, 'unittesting'])->name('unittesting');
        Route::any('/baru/submit_score', [ReactController::class, 'submit_score_baru'])->name('submit_score_baru');
        Route::post('/upload-file', [ReactLogicalController::class, 'uploadFile'])->name('upload_file');
    });
});

Route::group(['middleware' => ['auth', 'teacher']], function () {
    Route::prefix('mysql')->group(function () {
        //-----------------CHANGED-----------------
        Route::get('/teacher/materials', [MysqlTeacherTopicsController::class, 'index'])->name('mysql_teacher');
        Route::get('/teacher/topics-table', [MysqlTeacherTopicsController::class, 'topicsTable'])->name('teacher.topics.table');
        Route::post('/teacher/topics/add-topic-subtopic', [MysqlTeacherTopicsController::class, 'addTopicSubtopic'])->name('teacher.topics.addTopicSubtopic');
        Route::delete('/teacher/topics/{id}/delete', [MysqlTeacherTopicsController::class, 'deleteTopic'])->name('teacher.topics.delete');
        // Route untuk ambil data topic & subtopic
        Route::get('/teacher/topics/{id}/edit', [MysqlTeacherTopicsController::class, 'editTopicAjax']);
        Route::put('/teacher/topics/{id}', [MysqlTeacherTopicsController::class, 'updateTopicAjax']);

        Route::get('/mysql/teacher/questions/table', [MysqlTeacherQuestionController::class, 'questionsTable'])->name('teacher.questions.table');
        Route::get('/mysql/teacher/questions/{id}/edit', [MysqlTeacherQuestionController::class, 'editQuestionAjax']);

        Route::post('/teacher/questions', [MysqlTeacherQuestionController::class, 'storeQuestion']);
        Route::get('/teacher/questions/{id}', [MysqlTeacherQuestionController::class, 'show'])->name('teacher.questions.show');
        //-----------------CHANGED-----------------
    });
});

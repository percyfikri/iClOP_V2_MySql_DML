<?php

use App\Http\Controllers\React\ReactController;
use App\Http\Controllers\React\ReactDosenController;
use App\Http\Controllers\React\Student\ReactLogicalController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MySQL\MysqlController;
use App\Http\Controllers\MySQL\MysqlStudentController;
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
        Route::get('/detail-topics', [MysqlStudentController::class, 'showTopicDetail'])->name('showTopicDetail');
        Route::post('/submit', [MysqlStudentController::class, 'submitUserInput'])->name('submitUserInput');
        Route::post('/run-user-select-query', [MysqlStudentController::class, 'runUserSelectQuery'])->name('runUserSelectQuery');
        Route::get('/student/progress', [MysqlStudentController::class, 'getStudentProgressAjax'])->name('student.progress.ajax');
        //-----------------CHANGED-----------------
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
        Route::delete('/teacher/subtopics/{id}/delete', [MysqlTeacherTopicsController::class, 'deleteSubtopic'])->name('teacher.subtopics.delete');

        Route::get('/mysql/teacher/questions/table', [MysqlTeacherQuestionController::class, 'questionsTable'])->name('teacher.questions.table');
        Route::get('/teacher/questions/check-duplicate', [MysqlTeacherQuestionController::class, 'checkDuplicate']);
        Route::post('/teacher/questions', [MysqlTeacherQuestionController::class, 'storeQuestion']);
        Route::put('/teacher/questions/{id}', [MysqlTeacherQuestionController::class, 'update']);
        Route::post('/teacher/questions/{id}', [MysqlTeacherQuestionController::class, 'update']); // Untuk AJAX _method=PUT
        Route::get('/teacher/questions/{id}', [MysqlTeacherQuestionController::class, 'show'])->name('teacher.questions.show');
        Route::delete('/teacher/questions/{id}', [MysqlTeacherQuestionController::class, 'destroy'])->name('teacher.questions.destroy');
        Route::get('/teacher/subtopics/{id}/modul', [MysqlTeacherQuestionController::class, 'getSubtopicModul']);
        //-----------------CHANGED-----------------
    });
});

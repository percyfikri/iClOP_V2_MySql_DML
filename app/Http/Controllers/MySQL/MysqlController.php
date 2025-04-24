<?php

namespace App\Http\Controllers\MySQL;

use App\Http\Controllers\Controller;
use App\Models\MySQL\MySqlTopicDetails;
use App\Models\MySQL\MySqlTopics;
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
            // return redirect('mysql/start');
            return view('mysql_dml.student.material.index', 
            compact(
                'topics', 
                'role', 
                'topicsCount', 
                'topicDetails', 
            ));
        }
    }

    function mysql_material_detail()
    {
        $mysqlid = (int)$_GET['mysqlid'] ?? '';
        $start = (int)$_GET['start'] ?? '';
        $output = $_GET['output'] ?? '';

        $results = DB::select("select * from mysql_topic_details where topic_id = $mysqlid and id ='$start' ");
        $html_start = '';
        $pdf_reader = '';

        foreach ($results as $r) {
            if ($mysqlid == $r->topic_id) {
                $html_start = empty($r->file_name) ? $r->description : $r->file_name;
                $pdf_reader = !empty($r->file_name) ? 1 : 0;
                break;
            }
        }
// {{-- CHANGE 1 --}}
        $listTask = DB::select(
            "select aa.*, us.name from php_user_submits aa 
            join users us on aa.userid = us.id 
            where php_id = $mysqlid and php_id_topic = $start ");

            // =========Rencana Query=========
            // select from mysql_student_submissions
            // where user_id and topic_detail_id
            // where user_id = Auth::user()->id

        $idUser = Auth::user()->id;
        $roleTeacher = DB::select("select role from users where id = $idUser");

        $topics = MySqlTopics::all();
        $detail = MySqlTopicDetails::findOrFail($mysqlid);
        $topicsCount = count($topics);
        $detailCount = ($topicsCount / $topicsCount) * 10;

        return view('mysql_dml.student.material.topic_detail', [
            'row' => $detail,
            'topics' => $topics,
            'mysqlid' => $mysqlid,
            'html_start' => $html_start,
            'pdf_reader' => $pdf_reader,
            'topicsCount' => $topicsCount,
            'detailCount' => $detailCount,
            'output' => $output,
            'flag' => isset($results[0]) ? $results[0]->flag : 0,
            // 'listTask' => $listTask,
            'role' => isset($roleTeacher[0]) ? $roleTeacher[0]->role : '',
        ]);
    }
}
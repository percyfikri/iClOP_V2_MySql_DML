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

    public function show()
    {
        return view('mysql_dml.student.material.show');
    }
}

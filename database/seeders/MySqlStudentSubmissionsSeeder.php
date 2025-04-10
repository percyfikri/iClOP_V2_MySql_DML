<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MySqlStudentSubmissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user_id = 1631; // ID pengguna yang ada di tabel users

        // Ambil ID dari query pertama
        $firstQueryId = DB::table('mysql_queries')->orderBy('id', 'asc')->first()->id;

        // Ambil ID dari task pertama
        $firstTaskId = DB::table('mysql_topics')->orderBy('id', 'asc')->first()->id;

        // Daftar data yang akan disimpan
        $submissions = [
            [
                'user_id' => $user_id,
                'topic_detail_id' => $firstTaskId,
                'query_id' => $firstQueryId,
            ],
            [
                'user_id' => $user_id,
                'topic_detail_id' => $firstTaskId,
                'query_id' => $firstQueryId + 1, // Query kedua
            ],
        ];

        // Loop melalui setiap data dan gunakan updateOrInsert
        foreach ($submissions as $submission) {
            DB::table('mysql_student_submissions')->updateOrInsert(
                [
                    'user_id' => $submission['user_id'],
                    'topic_detail_id' => $submission['topic_detail_id'],
                    'query_id' => $submission['query_id'],
                ], // Kondisi unik: kombinasi user_id, task_id, dan query_id
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

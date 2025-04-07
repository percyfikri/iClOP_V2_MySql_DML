<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MySqlTopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the ID of the first topic (e.g., "INSERT")
        $firstTopicId = DB::table('mysql_topic_details')->orderBy('id', 'asc')->first()->id;

        // Daftar data yang akan disimpan
        $tasks = [
            [
                'topic_id' => $firstTopicId,
                'title' => 'Data Manipulation Language',
            ],
        ];

        // Loop melalui setiap data dan gunakan updateOrInsert
        foreach ($tasks as $task) {
            DB::table('mysql_topics')->updateOrInsert(
                ['title' => $task['title']], // Kondisi: Cari berdasarkan kolom 'title'
                [
                    'topic_id' => $task['topic_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MySqlTopicDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Daftar data yang akan disimpan
        $topics = [
            [
                'title' => 'INSERT',
                'description' => 'Learn how to insert data into a database table.',
                'folder_path' => '/topics/insert',
            ],
            [
                'title' => 'UPDATE',
                'description' => 'Learn how to update existing data in a database table.',
                'folder_path' => '/topics/update',
            ],
            [
                'title' => 'DELETE',
                'description' => 'Learn how to delete data from a database table.',
                'folder_path' => '/topics/delete',
            ],
        ];

        // Loop melalui setiap data dan gunakan updateOrInsert
        foreach ($topics as $topic) {
            DB::table('mysql_topic_details')->updateOrInsert(
                ['title' => $topic['title']], // Kondisi: Cari berdasarkan kolom 'title'
                [
                    'description' => $topic['description'],
                    'folder_path' => $topic['folder_path'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

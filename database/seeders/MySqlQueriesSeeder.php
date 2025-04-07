<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MySqlQueriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Daftar data yang akan disimpan
        $queries = [
            [
                'user_id' => 1631, // Pastikan user_id sesuai dengan ID pengguna yang ada di tabel users
                'query' => 'SELECT * FROM mysql_topic_detail;',
            ],
            [
                'user_id' => 1631,
                'query' => 'INSERT INTO mysql_topics (topic_id, title) VALUES (1, "DML Example");',
            ],
            [
                'user_id' => 1631,
                'query' => 'UPDATE mysql_topics SET title = "Updated DML" WHERE id = 1;',
            ],
        ];

        // Loop melalui setiap data dan gunakan updateOrInsert
        foreach ($queries as $query) {
            DB::table('mysql_queries')->updateOrInsert(
                ['query' => $query['query']], // Kondisi: Cari berdasarkan kolom 'query'
                [
                    'user_id' => $query['user_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

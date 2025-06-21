<?php
class UserQueryCest
{

    public function testUserQuery(\Tests\Support\AcceptanceTester $I, \Codeception\Module\Db $db)
    {
        $userId = trim(getenv('USER_ID'));
        $queryFile = codecept_root_dir() . "query_user_{$userId}.sql";

        // Cek apakah file query_user.sql ada
        if (!file_exists($queryFile)) {
            // Jika tidak ada, maka failed test
            $I->fail('File query_user.sql tidak ditemukan: ' . $queryFile);
        }

        // Baca isi file query_user.sql
        $query = file_get_contents($queryFile);

        // Jika isi file query_user.sql kosong, maka failed test
        if (!$query) {
            $I->comment('Query user kosong');
        }

        // Validasi WHERE pada DELETE/UPDATE agar harus ada operator setelah kolom
        if (preg_match('/\b(DELETE|UPDATE)\b.+\bWHERE\b\s+([a-zA-Z0-9_]+)\s*;?$/i', $query)) {
            throw new \Exception('The WHERE condition must have a clear comparison operator or condition (e.g. =, IS NOT NULL, LIKE, etc.)');
        }

        // Validasi WHERE tanpa operator (=, <>, !=, IS, LIKE, BETWEEN, IN, >, <, >=, <=)
        if (preg_match('/\bWHERE\b\s+[a-zA-Z0-9_]+\s*($|;)/i', $query)) {
            throw new \Exception('The WHERE condition must have a clear comparison operator or condition (e.g. =, IS NOT NULL, LIKE, etc.)');
        }

        // Validasi INSERT kolom dan nilai
        if (preg_match('/^\s*INSERT\s+INTO\s+(\w+)\s*\(([^)]+)\)\s*VALUES\s*\(([^)]+)\)/i', $query, $matches)) {
            $columns = array_map('trim', explode(',', $matches[2]));
            $values = array_map('trim', explode(',', $matches[3]));
            if (count($columns) !== count($values)) {
                throw new \Exception('The number of columns and values in the INSERT statement must be the same');
            }
        }

        // Jalankan query jika lolos validasi
        $db->_getDbh()->exec($query);
    }
}






<?php
class UserQueryCest
{

    public function testUserQuery(\Tests\Support\AcceptanceTester $I, \Codeception\Module\Db $db)
    {
        // Tentukan nama file query yang akan dijalankan
        $queryFile = codecept_root_dir() . 'tests/query_user.sql';
        
        // Cek apakah file query_user.sql ada
        if (!file_exists($queryFile)) {
            // Jika tidak ada, maka failed test
            $I->fail('File query_user.sql tidak ditemukan: ' . $queryFile);
        }
        
        // Baca isi file query_user.sql
        $query = file_get_contents($queryFile);
        
        // Jika isi file query_user.sql kosong, maka failed test
        if (!$query) {
            $I->fail('Query user kosong');
        }
        
        // Jalankan query yang ada di file query_user.sql
        $db->_getDbh()->exec($query);
    }
}


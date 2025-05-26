<?php
class UserQueryCest
{
    public function testInsertAndSelect(\Tests\Support\AcceptanceTester $I)
    {

        // $I->haveInDatabase('mk', [
        //     'kode_mk' => '02010',
        //     'nama_mk' => 'Basis Data'
        // ]);

        // // Eksekusi query SELECT dari user
        // $I->seeInDatabase('mk', [
        //     'kode_mk' => '02010',
        //     'nama_mk' => 'Basis Data'
        // ]);

        $I->comment('Insert data ke tabel mk');
        $I->haveInDatabase('mk', [
            'kode_mk' => '02010',
            'nama_mk' => 'Basis Data'
        ]);
        $I->comment('Cek data di tabel mk');
        $I->seeInDatabase('mk', [
            'kode_mk' => '02010',
            'nama_mk' => 'Basis Data'
        ]);
    }

    // public function testUserQuery(\Tests\Support\AcceptanceTester $I)
    // {
    //     $queryId = getenv('QUERY_ID') ?: (isset($_ENV['query_id']) ? $_ENV['query_id'] : null);
    //     if (!$queryId) {
    //         $I->fail('Query ID tidak ditemukan');
    //     }
    //     // Ambil query dari database utama (gunakan koneksi Laravel/Db utama jika perlu)
    //     $query = $I->grabFromDatabase('mysql_queries', 'query', ['id' => $queryId]);

    //     // Jalankan query user di database iclop_v2_testing
    //     try {
    //         $I->executeQuery($query); // gunakan $I->executeQuery jika runSql tidak ada
    //         // Lakukan pengujian, misal cek data di tabel mk
    //         // $I->seeInDatabase('mk', ['kode_mk' => '02010']);
    //     } catch (\Exception $e) {
    //         $I->fail('Query gagal dijalankan: ' . $e->getMessage());
    //     }
    // }
    public function testUserQuery(\Tests\Support\AcceptanceTester $I, \Codeception\Module\Db $db)
    {
        $queryFile = codecept_root_dir() . 'tests/query_user.sql';
        if (!file_exists($queryFile)) {
            $I->fail('File query_user.sql tidak ditemukan: ' . $queryFile);
        }
        $query = file_get_contents($queryFile);
        if (!$query) {
            $I->fail('Query user kosong');
        }
        // $I->executeQuery($query);
        // // Lakukan pengujian hasil query di database testing

        // Jalankan query user
        // $I->getModule('Db')->_getDbh()->exec($query);
        $db->_getDbh()->exec($query);

        // Lakukan pengujian hasil query di database testing
        // $I->seeInDatabase('mk', ['kode_mk' => '02010']);
    }
}

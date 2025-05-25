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
}

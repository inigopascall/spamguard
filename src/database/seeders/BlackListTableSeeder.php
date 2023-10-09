<?php

namespace InigoPascall\SpamGuard\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlackListTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('spamguard_blacklist')->insert([
            [ 'text' => 'Eric Jones' ],
            [ 'text' => 'my name is Eric' ],
            [ 'text' => 'My name is Eric' ],
            [ 'text' => 'my name’s Eric' ],
            [ 'text' => 'My name’s Eric' ],
            [ 'text' => 'ericjonesmyemail@gmail.com' ],
            [ 'text' => '7 out of 10 visitors' ],
            [ 'text' => 'http://jumboleadmagnet.com' ],
            [ 'text' => 'https://advanceleadgeneration.com' ],
            [ 'text' => 'Henryviago' ],
            [ 'text' => 'https://bit.ly/3E4oyBg']
        ]);
    }
}

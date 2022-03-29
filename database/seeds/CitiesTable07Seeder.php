<?php

use Illuminate\Database\Seeder;

class CitiesTable07Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('mtb_cities')->truncate();
        
        DB::table('mtb_cities')->insert([
            [
                'pref_id' => 7,
                'name' => '福島市',
            ],
            [
                'pref_id' => 7,
                'name' => '会津若松市',
            ],
            [
                'pref_id' => 7,
                'name' => '郡山市',
            ],
            [
                'pref_id' => 7,
                'name' => 'いわき市',
            ],
            [
                'pref_id' => 7,
                'name' => '白河市',
            ],
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
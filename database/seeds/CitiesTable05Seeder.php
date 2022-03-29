<?php

use Illuminate\Database\Seeder;

class CitiesTable05Seeder extends Seeder
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
                'pref_id' => 5,
                'name' => '秋田市',
            ],
            [
                'pref_id' => 5,
                'name' => '能代市',
            ],
            [
                'pref_id' => 5,
                'name' => '横手市',
            ],
            [
                'pref_id' => 5,
                'name' => '大館市',
            ],
            [
                'pref_id' => 5,
                'name' => '男鹿市',
            ],
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
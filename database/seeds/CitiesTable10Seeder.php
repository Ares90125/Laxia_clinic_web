<?php

use Illuminate\Database\Seeder;

class CitiesTable10Seeder extends Seeder
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
                'pref_id' => 10,
                'name' => '前橋市',
            ],
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
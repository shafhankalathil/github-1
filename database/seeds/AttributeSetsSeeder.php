<?php

use Illuminate\Database\Seeder;
use App\AttributeSets;
class AttributeSetsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('attribute_sets')->insert([
            ['attribute_sets_name'=>'Default'],
        ]);

    }
}

<?php

use Illuminate\Database\Seeder;
use App\Attributes;

class AttributesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('attributes')->insert([
            ['attribute_sets_id'=>'1', 'attribute_name'=> 'color'],
            ['attribute_sets_id'=>'1', 'attribute_name'=> 'size'],
        ]);
    }
}

<?php

use Illuminate\Database\Seeder;

class FaqCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faq_category = array(
            ['id' => 1, 'category_name' => 'ABOUT ALCHEMY WINGS', 'created_at' => new DateTime],
            ['id' => 2, 'category_name' => 'HOW TO USE ALCHEMY WINGS', 'created_at' => new DateTime],
            ['id' => 3, 'category_name' => 'QUESTIONS ABOUT MY ORDER', 'created_at' => new DateTime],
            ['id' => 4, 'category_name' => 'ALL THE OTHER STUFF', 'created_at' => new DateTime],
            ['id' => 5, 'category_name' => 'HOW WE PARTNER WITH STORES', 'created_at' => new DateTime],
            //
           
        );
        DB::table('faqs_category')->insert($faq_category);
    }
}

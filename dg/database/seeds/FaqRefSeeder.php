<?php

use Illuminate\Database\Seeder;

class FaqRefSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userGroup = config('faq.user_group');
        $faqs = DB::table('faqs')
                ->addSelect('faqs.id')
                ->get();
        foreach ($faqs as $faq) {
            $faqId = $faq->id;
            foreach ($userGroup as $userGroupId => $userGroupName) {
                $faqGroup = array(
                    ['fk_faq_id' => $faqId, 'fk_user_group_id' => $userGroupId],
                );
                DB::table('faq_group_mapping')->insert($faqGroup);
            }
        }
    }
}

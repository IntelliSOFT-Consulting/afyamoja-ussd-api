<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FeedbackClassificationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('feedback_classifications')->delete();
        
        \DB::table('feedback_classifications')->insert(array (
            0 => 
            array (
                'id' => 1,
                'classification' => 'detractors',
                'feedback' => 'Thanks for your feedback. We highly value all ideas and suggestions from our customers, whether they’re positive or critical. In the future, our team might reach out to you to learn more about how we can further improve our product so that it exceeds your expectations',
                'status' => 1,
                'created_at' => '2020-08-21 14:29:20',
                'updated_at' => '2020-08-21 14:28:43',
            ),
            1 => 
            array (
                'id' => 2,
                'classification' => 'passive',
                'feedback' => 'Thanks for your feedback. Our goal is to create the best possible product, and your thoughts, ideas, and suggestions play a major role in helping us identify opportunities to improve.',
                'status' => 1,
                'created_at' => '2020-08-21 14:28:43',
                'updated_at' => '2020-08-21 14:28:43',
            ),
            2 => 
            array (
                'id' => 3,
                'classification' => 'promoters',
                'feedback' => 'Thanks for your feedback. It’s great to hear that you’re a fan of Afya Moja. Your feedback helps us discover new opportunities to improve our product and make sure you have the best possible experience.',
                'status' => 1,
                'created_at' => '2020-08-21 14:29:10',
                'updated_at' => '2020-08-21 14:29:10',
            ),
        ));
        
        
    }
}
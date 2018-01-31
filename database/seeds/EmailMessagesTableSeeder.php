<?php

use Illuminate\Database\Seeder;

use App\Models\EmailMessage;

class EmailMessagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $emailMessages = factory(EmailMessage::class)
                            ->times(40)
                            ->make();

        EmailMessage::insert($emailMessages->toArray());
    }
}

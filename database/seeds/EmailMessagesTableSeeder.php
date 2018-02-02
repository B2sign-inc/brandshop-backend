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
        factory(EmailMessage::class, 40)->create();
    }
}

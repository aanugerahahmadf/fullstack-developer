<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contact;

class TestContact extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:contact';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test contact retrieval';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $contacts = Contact::all();
        $this->info('Contacts count: ' . $contacts->count());

        if ($contacts->count() > 0) {
            $this->info('First contact: ' . $contacts->first()->email);
        } else {
            $this->info('No contacts found');
        }

        return 0;
    }
}

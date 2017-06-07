<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class PrepareGoogleFeeds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make_feed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Cron Started at ' . date('Y-m-d H:i:s'));
        $url = env('PUBLIC_URL').'/productfeed';
        $fileContents = file_get_contents($url);
        file_put_contents(public_path().'/productfeed.xml', $fileContents );
        $this->info('file extracted ' . date('Y-m-d H:i:s'));
    }
}

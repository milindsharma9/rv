<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PrepareBlogFeeds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make_feed_blog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to generate blog feed';

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
        $url = env('PUBLIC_URL').'/blogfeed';
        $fileContents = file_get_contents($url);
        file_put_contents(public_path().'/blogfeed.xml', $fileContents );
        $this->info('file extracted ' . date('Y-m-d H:i:s'));
    }
}

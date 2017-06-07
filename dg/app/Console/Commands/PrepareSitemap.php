<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PrepareSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prepare_sitemap';

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
        $this->info('sitemap creation started at ' . date('Y-m-d H:i:s'));
        $url = env('PUBLIC_URL').'/sitemap1';
        $fileContents = file_get_contents($url);
        file_put_contents(public_path().'/sitemap.xml', $fileContents );
        $this->info('file extracted ' . date('Y-m-d H:i:s'));
    }
}

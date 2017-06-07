<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Commands\Inspire::class,
        Commands\Product::class,
        Commands\ImportProductImage::class,
        Commands\ImportPostcodes::class,
        Commands\PrepareGoogleFeeds::class,
        Commands\PrepareSitemap::class,
        Commands\MigrateProductImages::class,
        Commands\SetProductImageCache::class,
        Commands\PrepareBlogFeeds::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('set_product_image_cache')
                  ->everyMinute();
        $schedule->command('make_feed_blog')
                  ->hourly();
        $schedule->command('make_feed')
                  ->daily();
    }
}

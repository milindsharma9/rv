<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ProductImage;

class SetProductImageCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set_product_image_cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set Product images in cache';

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
        $this->info('Product Image Cache Cron Started at ' . date('Y-m-d H:i:s'));
        $productImageModel = new ProductImage();
        $productImageModel->setProductsImageInCache();
        $this->info('Product Image Cache Cron Ended at ' . date('Y-m-d H:i:s') );
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateProductImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate_product_images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate Product images in  table';

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
        $this->info('Image Import Cron Started at ' . date('Y-m-d H:i:s'));
        $publicPath = public_path();
        //echo $publicPath;
        $oldDirectory = $publicPath."/alchemy/images/product-images";
        if (!file_exists(public_path('uploads/product_images'))) {
            if (!file_exists(public_path('uploads'))) {
                mkdir(public_path('uploads'), 0777);
            }
            mkdir(public_path('uploads/product_images'), 0777);
            mkdir(public_path('uploads/product_images/thumb'), 0777);
        }
        $newDirectory = $publicPath."/uploads/product_images";
        //copy($oldDirectory, $newDirectory);
        $products           = \App\Product::all();
        $prodImageModel     = new \App\ProductImage();
        foreach ($products as $product) {
            $productId = $product->id;
            $imageName = $product->barcode.".png";
            //echo $productId."|".$imageName.PHP_EOL;
            $prodImageModel->insertProductImage($productId, $imageName, $isPrimary = 1);
        }
        $this->info('Image Import Cron Ended at ' . date('Y-m-d H:i:s'));
    }
}

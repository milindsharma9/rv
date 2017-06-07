<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Exception;

class ImportProductImage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import_product_image {--thumb=false}';

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
        $overRide = false;
        if ($this->confirm('Do you want to over ride existing files? [y|N]')) {
            $overRide = true;
        }
        $this->info('Image Import Cron Started at ' . date('Y-m-d H:i:s'));
        $options    = $this->option();
        $thumb      = $options['thumb'];
        $isThumb    = false;
        if ($thumb == 'true') {
            $isThumb = true;
        }
        //$directory = public_path()."/alchemy/images/product-images/alchemy_wings_images";
        $publicPath = public_path();
        $publicPath = substr($publicPath, 0, -7);
        $directory = $publicPath."/data/product_images/main";
        if ($isThumb) {
            //$directory = $directory ."/thumb";
            $directory = $publicPath."/data/product_images/thumb";
        }
        $files = File::allFiles($directory);
        foreach ($files as $file) {
            $newImageName = "image";
            try {
                $imagePath = $file;
                $aImagePath = explode("/", $imagePath);
                $fileName = $aImagePath[count($aImagePath) -1];
                $aFileName = explode("_", $fileName);
                $newImageName = $aFileName[0];
                $newPathWithName = public_path()."/alchemy/images/product-images/".$newImageName.".png";
                if ($isThumb) {
                    $newPathWithName = public_path()."/alchemy/images/product-images/thumb/".$newImageName.".png";
                }
                if (File::exists($newPathWithName)
                        && !$overRide) {
                    $this->info("Success|Image Already Present For Product|" . $newImageName);
                } else {
                    if (File::copy($imagePath , $newPathWithName)) {
                        $this->info("Success|Successfully Copied for|" . $newImageName);
                    } else {
                        $this->error("Error|Error while Copying for|" . $newImageName);
                    }
                }
            } catch (Exception $ex) {
                $this->error("Error|Exception Error while Copying for|" . $newImageName);
            }
        }
        $this->info('Image Import Cron Ended at ' . date('Y-m-d H:i:s') );
    }
}

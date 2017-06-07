<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Product as Prod;
use DB;
use Exception;

class Product extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    //protected $signature = 'import_product';
    protected $signature = 'import_product {--deletePrevious=false}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /*
     *
     */
    private $categoryData = array();

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->getCategoryData();
        parent::__construct();
    }

    /**
     * Execute the console command. Method for Importing Product into system
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Cron Started at ' . date('Y-m-d H:i:s'));
        $options = $this->option();
        if ($options['deletePrevious'] == 'true') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            $this->info('Truncating Products table');
            DB::table('products')->truncate();
            $this->info('Truncated Products Meta table');
            $this->info('Truncating Products Meta table');
            DB::table('products_meta')->truncate();
            $this->info('Truncated Products Meta table');
            //$this->info('Truncating Products Category Mapping table');
            //DB::table('xref_product_categories')->truncate();
            //$this->info('Truncated Products Category Mapping table');
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
        $file = fopen('product.csv', 'r');
        $flag = true;
        while (($line = fgetcsv($file)) !== FALSE) {
            if($flag) { $flag = false; continue; }
            $barcode            = $line[1];
            try {
                DB::beginTransaction();
                $this->info('Importing Product|' . $barcode);
                $lastInsertedProductId        = $this->importProduct($line, $barcode);
                /*$aCat['p1'] = $line[11];
                $aCat['p2'] = $line[12];
                $aCat['p3'] = $line[13];
                $this->createProductMapping($lastInsertedProductId, $aCat);*/
                $this->info('Imported Product|' . $barcode);
                DB::commit();
            } catch (Exception $ex) {
                $this->error('Error while importing Product|' . $barcode . '|'.$ex->getMessage());
                DB::rollBack();
            }
        }
        fclose($file);
        $this->info('Cron Ended at ' . date('Y-m-d H:i:s') );
    }

    /**
     * Method to prepare array of all category data
     *
     */
    private function getCategoryData() {
        $categories = DB::table('categories')
                     ->select('id', 'name')
                     ->whereNull('deleted_at')
                     ->get();
        $aCatTree = array();
        foreach ($categories as $category) {
            $aCatTree[$this->modifyCategoryName($category->name)] = $category->id;
        }
        $this->categoryData = $aCatTree;
    }

    /**
     * Method to modify category Name to proper machine name
     * @param string $catName Category name
     * @return string Modified catName
     */
    private function modifyCategoryName($catName) {
        return strtolower(str_replace(" ", "_", $catName));
    }

    /**
     * Method to create Product Category Mapping
     * @param int $productId ProductId
     * @param array $aCat Array of catgeory name
     */
    private function createProductMapping($productId, $aCat) {
        $this->info("Creating Product Cat mapping");
        $insertData = array();
        foreach ($aCat as $cat) {
            $modifiedCatName = $this->modifyCategoryName($cat);
            if (isset($this->categoryData[$modifiedCatName])) {
                $categoryData = array(
                    'fk_product_id'     => $productId,
                    'fk_category_id'    => $this->categoryData[$modifiedCatName],
                );
                array_push($insertData, $categoryData);
            }
        }
        if (!empty($insertData)) {
            DB::table('xref_product_categories')->insert($insertData);
            $this->info("Created Product Cat mapping|" . $productId);
        } else {
            $this->error("No Mapping Found for Product|" . $productId);
        }
    }

    /**
     * Method to import Product into DB table
     * @param array $csvData Single Line data of CSV
     * @param int $barcode Unique Barcode of product
     * @return int Inserted Product Id
     */
    private function importProduct($csvData, $barcode) {
        $productExists      = Prod::where('barcode', $barcode)->first();
        $updateProduct      = false;
        $description        = $csvData[5];
        $brand              = $csvData[2];
        $price              = $csvData[11];
        $aParam             = array(
            'name'              => $brand,
            'description'       => $description,
            'price'             => $price,
            'barcode'           => $barcode,
            'created_at'        => new \DateTime,
            'updated_at'        => new \DateTime,
        );
        if (empty($productExists)) {
            $product                = Prod::create($aParam);
            $lastInsertedProductId  = $product->id;
            $updateProduct          = false;
            $this->info('Inserting Product Details for |' . $barcode);
        } else {
            $lastInsertedProductId  = $productExists->id;
            $data = DB::table('products')->where('id','=', $lastInsertedProductId)
                ->update($aParam);
            $updateProduct          = true;
            $this->info('Updating Product Details for |' . $barcode);
        }
        $this->importProductMeta($csvData, $barcode, $lastInsertedProductId, $updateProduct);
        return $lastInsertedProductId;
    }

    /**
     * Method to import Product Meta into DB table
     * @param array $csvData Single Line data of CSV
     * @param int $barcode Unique Barcode of product
     * @param int $lastInsertedProductId Product Id
     * @param boolean $updateProduct Decides whether to import or insert.
     * @return null
     */
    private function importProductMeta($csvData, $barcode, $lastInsertedProductId, $updateProduct) {
        //$date           = \DateTime::createFromFormat('d/m/Y', $csvData[4]);
        //$versionDate    = $date->format('Y-m-d').PHP_EOL;
        $insertData             = array(
            'fk_product_id'                     => $lastInsertedProductId,
            /*'subscriber_code'                   => $csvData[3],
            'pvid'                              => "",//$csvData[9],
            'version_date'                      => $versionDate,
            'product_group_1'                   => $csvData[49],
            'product_group_2'                   => $csvData[50],
            'product_group_3'                   => $csvData[51],
            'standardised_brand'                => $csvData[6],
            'sub_brand'                         => "",//$csvData[16],*/
            'product_marketing'                 => $csvData[22], // l
            /*'brand_marketing'                   => $csvData[10],
            'regulated_product_name'            => $csvData[99],
            'manufacturers_address'             => $csvData[15],
            'return_to'                         => $csvData[16],
            'company_name'                      => "",//$csvData[43],
            'company_address'                   => "",//$csvData[44],
            'telephone_helpline'                => "",//$csvData[45],
            'email_helpline'                    => "",//$csvData[46],
            'web_address'                       => "",//$csvData[47],*/
            'lower_age_limit'                   => $csvData[29], // l
            /*'recycling'                         => $csvData[29],
            'lifestyle'                         => $csvData[34],
            'lifestyle_other_text'              => $csvData[35],
            'height'                            => $csvData[44],
            'width'                             => $csvData[43],
            'depth'                             => $csvData[45],
            'weight'                            => $csvData[48],*/
            'ingredients'                       => $csvData[34], // l
            //'nut_statement'                     => $csvData[28],
            'allergy_advice'                    => $csvData[30], // l
            'safety_warnings'                   => $csvData[31], // addded later // l
            /*'allergy_other_text'                => $csvData[27], // added later
            'additives'                         => $csvData[24],
            'nutrition'                         => $csvData[53],
            'per100_portiontype'                => $csvData[54],
            'per100_energy_kj'                  => $csvData[56],*/
            'per100_energy_kcal'                => $csvData[37], // l
            'per100_fat'                        => $csvData[42], // l
            'per100_thereof_sat_fat'            => $csvData[43], // l
            //'per100_carbohydrates'              => $csvData[58],
            'per100_thereof_total_sugar'        => $csvData[41], // l
            /*'per100_protein'                    => $csvData[57],
            'per100_fibre'                      => $csvData[62],
            'per100_sodium'                     => $csvData[63],*/
            'per100_salt'                       => $csvData[46], // l
            /*'per100_salt_equivalent'            => $csvData[65],
            'perServing_portiontype'            => $csvData[78],
            'perServing_energy_kj'              => $csvData[80],
            'perServing_energy'                 => $csvData[79],
            'perServing_fat_kcal'               => $csvData[84],
            'perServing_thereof_sat_fat'        => $csvData[85],
            'perServing_carbohydrates'          => $csvData[82],
            'perServing_thereof_total_sugar'    => $csvData[83],
            'perServing_protein'                => $csvData[81],
            'perServing_fibre'                  => $csvData[86],
            'perServing_sodium'                 => $csvData[87],
            'perServing_salt'                   => $csvData[88],
            'perServing_salt_equivalent'        => $csvData[89],
            'front_of_pack_nutrition'           => "",//$csvData[113],*/
            'servings_washes'                   => $csvData[25], // l //$csvData[116],
            /*'alcohol_alcohol_type'              => "",//$csvData[118],
            'alcohol_units'                     => "",//$csvData[120],
            'alcohol_taste_category'            => "",//$csvData[123],
            'alcohol_tasting_notes'             => "",//$csvData[124],
            'alcohol_serving_suggestion'        => "",//$csvData[125],
            'alcohol_wine_colour'               => "",//$csvData[126],
            'alcohol_region_of_origin'          => "",//$csvData[127],
            'alcohol_current_vintage'           => "",//$csvData[132],
            'alcohol_producer'                  => "",//$csvData[133],*/
            'alcohol_grape_variety'             => $csvData[27], // l //$csvData[135],
            'created_at'                        => new \DateTime,
            'updated_at'                        => new \DateTime,
        );
        if ($updateProduct) {
            DB::table('products_meta')->where('fk_product_id','=', $lastInsertedProductId)
                ->update($insertData);
        } else {
            DB::table('products_meta')->insert($insertData);
        }
    }
}

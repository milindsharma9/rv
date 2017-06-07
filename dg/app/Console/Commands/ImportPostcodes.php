<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\ValidPostcode;
use DB;
use Exception;

class ImportPostcodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import_postcodes {--deletePrevious=false}';

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
        $options = $this->option();
        if ($options['deletePrevious'] == 'true') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            $this->info('Truncating Postcode Table');
            DB::table('valid_postcodes')->truncate();
            $this->info('Truncated Postcode table');
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
        $file = fopen('postcode.csv', 'r');
        $flag = true;
        while (($line = fgetcsv($file)) !== FALSE) {
            if($flag) { $flag = false; continue; }
            $postCode            = $line[1];
            try {
                $this->info('Importing Postcode|' . $postCode);
                $this->importPostcode($line);
                $this->info('Imported Postcode|' . $postCode);
            } catch (Exception $ex) {
                $this->error('Error while importing Postcode|' . $postCode . '|'.$ex->getMessage());
                DB::rollBack();
            }
        }
        fclose($file);
        $this->info('Cron Ended at ' . date('Y-m-d H:i:s') );
    }

    /**
     * Method to import Postcode into DB table
     *
     * @param array $csvData Single Line data of CSV
     * @param int $barcode Unique Barcode of product
     * @return int Inserted Product Id
     */
    private function importPostcode($csvData) {
        $postcode        = $csvData[1];
        $postcodeExists      = ValidPostcode::where('postcode', $postcode)->first();
        
        $lat              = $csvData[2];
        $lng              = str_replace(array("(", ")"), array("-", ""), $csvData[3]);
        $aParam             = array(
            'postcode'          => $postcode,
            'lat'               => $lat,
            'lng'               => $lng,
            'created_at'        => new \DateTime,
            'updated_at'        => new \DateTime,
        );
        if (empty($postcodeExists)) {
            $postcodes                = ValidPostcode::create($aParam);
            $this->info('Inserting Postcode Details for |' . $postcode);
        } else {
            $lastInsertedPostcodeId  = $postcodeExists->id;
            $data = DB::table('valid_postcodes')->where('id','=', $lastInsertedPostcodeId)
                ->update($aParam);
            $this->info('Updating Postcode Details for |' . $postcode);
        }
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products_meta', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fk_product_id', false)->unsigned();
            $table->string('subscriber_code', 16);
            $table->integer('pvid', false)->unsigned();
            $table->date('version_date');
            $table->string('product_group_1', 64);
            $table->string('product_group_2', 64);
            $table->string('product_group_3', 64);
            $table->string('standardised_brand', 64);
            $table->string('sub_brand', 64);
            $table->text('product_marketing');
            $table->text('brand_marketing');
            $table->string('regulated_product_name', 256);
            $table->text('manufacturers_address');
            $table->string('return_to', 512);
            $table->string('company_name', 512);
            $table->string('company_address', 512);
            $table->string('telephone_helpline', 32);
            $table->string('email_helpline', 64);
            $table->string('web_address', 128);
            $table->string('lower_age_limit', 128);
            $table->string('recycling', 512);
            $table->string('lifestyle', 512);
            $table->text('lifestyle_other_text');
            $table->string('height', 8);
            $table->string('width', 8);
            $table->string('depth', 8);
            $table->string('weight', 8);
            $table->text('ingredients');
            $table->text('nut_statement');
            $table->text('allergy_advice');
            $table->text('additives');
            $table->text('nutrition');
            $table->string('per100_portiontype', 32);
            $table->string('per100_energy_kj', 16);
            $table->string('per100_energy_kcal', 16);
            $table->string('per100_fat', 16);
            $table->string('per100_thereof_sat_fat', 16);
            $table->string('per100_carbohydrates', 16);
            $table->string('per100_thereof_total_sugar', 16);
            $table->string('per100_protein', 16);
            $table->string('per100_fibre', 16);
            $table->string('per100_sodium', 16);
            $table->string('per100_salt', 16);
            $table->string('per100_salt_equivalent', 16);
            $table->string('perServing_portiontype', 16);
            $table->string('perServing_energy_kj', 16);
            $table->string('perServing_energy', 16);
            $table->string('perServing_fat_kcal', 16);
            $table->string('perServing_thereof_sat_fat', 16);
            $table->string('perServing_carbohydrates', 16);
            $table->string('perServing_thereof_total_sugar', 16);
            $table->string('perServing_protein', 16);
            $table->string('perServing_fibre', 16);
            $table->string('perServing_sodium', 16);
            $table->string('perServing_salt', 16);
            $table->string('perServing_salt_equivalent', 16);
            $table->string('front_of_pack_nutrition', 16);
            $table->string('servings_washes', 128);
            $table->string('alcohol_alcohol_type', 128);
            $table->string('alcohol_units', 16);
            $table->string('alcohol_taste_category', 128);
            $table->text('alcohol_tasting_notes');
            $table->text('alcohol_serving_suggestion');
            $table->string('alcohol_wine_colour', 128);
            $table->string('alcohol_region_of_origin', 64);
            $table->string('alcohol_current_vintage', 32);
            $table->string('alcohol_producer', 512);
            $table->string('alcohol_grape_variety', 512);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('products_meta');
    }
}

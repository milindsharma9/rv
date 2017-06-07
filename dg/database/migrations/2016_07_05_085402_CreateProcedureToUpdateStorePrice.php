<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcedureToUpdateStorePrice extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared("DROP PROCEDURE IF EXISTS updateStorePrice");
        DB::unprepared("CREATE PROCEDURE updateStorePrice ()
BEGIN

declare storePrice DECIMAL(5,2);
DECLARE done INT;
DECLARE id INT;
DECLARE productId INT;

declare cur1 cursor for 
        select id_sales_order_item, fk_product_id
        from sales_order_item where store_flag=0;
declare continue handler for not found set done=1;

    set done = 0;
    open cur1;
    igmLoop: loop
        fetch cur1 into id,productId;
        if done = 1 then leave igmLoop; end if;
SELECT 
    store_price
FROM
    products
WHERE
    products.id = productId INTO storePrice;
UPDATE sales_order_item 
SET 
    store_price = storePrice,store_flag=1
WHERE
    id_sales_order_item = id;
    end loop igmLoop;
    close cur1;
END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared("DROP PROCEDURE IF EXISTS updateStorePrice");
    }

}

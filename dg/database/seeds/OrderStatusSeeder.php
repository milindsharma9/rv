<?php

use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $orderStatus = array(
            ['id_order_status' => 1, 'name' => 'Confirmed', 'description' => 'Confirmed', 'is_manual' => 0, 'lifecycle_order' => 2],
            ['id_order_status' => 2, 'name' => 'Collected', 'description' => 'Collected', 'is_manual' => 0, 'lifecycle_order' => 3],
            ['id_order_status' => 3, 'name' => 'Completed', 'description' => 'Completed', 'is_manual' => 1 , 'lifecycle_order' => 4],
            ['id_order_status' => 4, 'name' => 'New', 'description' => 'For newly created orders without payment', 'is_manual' => 0, 'lifecycle_order' => 1],
            ['id_order_status' => 5, 'name' => 'Rejected', 'description' => 'Rejected By Admin', 'is_manual' => 1, 'lifecycle_order' => 5],
            ['id_order_status' => 6, 'name' => 'Refunded', 'description' => 'Refund done by Mangopay', 'is_manual' => 0, 'lifecycle_order' => 6],
        );
        DB::table('order_status')->insert($orderStatus);
    }

}

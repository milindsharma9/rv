<?php

namespace App\Api\V1\Model;

use App\Occasion;
use App\Event;
use App\User;
use JWTAuth;
use App\Product;
use App\Category;
use App\Http\Helper\CommonHelper;
use App\ValidPostcode;
use App\SalesOrder;
use App\PaymentModel;

class CustomerModel {

  /**
   *
   * @var App\Product
   */
  private $_productModel = NULL;

  /**
   *
   * @var App\PaymentModel 
   */
  private $_paymentModel = NULL;

  /**
   * Function to Instatntiate Product Model.
   *
   * @return object App\Product Model
   *
   */
  private function getProductModel() {
    if ($this->_productModel == NULL) {
      $this->_productModel = new Product();
    }
    return $this->_productModel;
  }

  /**
   * Function to Instatntiate Payment Model.
   * 
   * @package CustomerController
   * @return object App\PaymentModel
   */
  private function getPaymentModel() {
    if ($this->_paymentModel == NULL) {
      //$this->_paymentModel = new PaymentModel($this->mangopay);
      $this->_paymentModel = \App::make('\App\PaymentModel');
    }
    return $this->_paymentModel;
  }

  /**
   * Method to return array of card detail and order detail
   *
   * @param $orderNumber
   * @return array $response
   *
   */
  public function trackorderpage($orderNumber) {
    $cardDetails = [];
    $orderModel = new SalesOrder();
    $orderData = $orderModel->getOrderDetails($orderNumber);
    if (empty($orderData)) {
      $response = ['exist' => FALSE, 'orderData' => $orderData, 'cardDetails' => $cardDetails];
      return $response;
    }
    $orderDetails = $orderData;
    unset($orderData['products']);
    unset($orderData['bundle']);
    $orderData['orderTotal']    =  (float) $orderData['orderTotal'];
    $orderData['driverCharges'] =  (float) $orderData['driverCharges'];
    foreach ($orderDetails['products'] as $key => $value) {
        $value['price'] = (float) $value['price'];
        $orderData['products'][] = $value;
    }
    foreach ($orderDetails['bundle'] as $key => $value) {
        $aProduct = array();
        foreach ($value['product'] as $product) {
            $product['price'] = (float) $product['price'];
            $aProduct[] = $product;
        }
        $value['product'] = $aProduct;
        $orderData['bundle'][] = $value;
    }
    $cardId = $orderModel->getOrderCardId($orderData['orderId']);
    if ($cardId) {
      $cardDetails = $this->getPaymentModel()->getCardDetails($cardId);
      $cardDetails = $cardDetails->CardProvider . ' ending with ' . substr($cardDetails->Alias, -4);
    }
    $response = ['exist' => TRUE, 'orderData' => $orderData, 'cardDetails' => $cardDetails];
    return $response;
  }

}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Entities\PaymentGateways\Gateways;

use App\Entities\PaymentGateways\PaymentGateway;
use App\Entities\BillingSubscriberTransaction;
class Wallet extends PaymentGateway{
    
    private $model;
    
    public function __construct($request=null) {
        parent::__construct($request);
    }
    
    public function debit() {
        
        if(empty($this->model)){
           throw new \Exception("Wallet Model Empty", 400);
        }
        
        $transaction = $this->_saveTransaction();
        
        return $transaction;
    }
    
    public function setPaymentModel($paymentModel)
    {
        $this->model = $paymentModel;
    }
    
    private function _saveTransaction()
    {
        $billingSubscriberTransaction = new BillingSubscriberTransaction();
        
        $billingSubscriberTransaction->pairing_id                  = 0;
        $billingSubscriberTransaction->subscriber_id               = $this->model->getCustomerId();
        $billingSubscriberTransaction->lco_id                      = $this->model->getOperatorId();
        $billingSubscriberTransaction->package_id                  = $this->model->getPackageId();
        $billingSubscriberTransaction->item_type                   = $this->model->getItemType();
        $billingSubscriberTransaction->payment_method_id           = $this->model->getPaymentMethodType();
        $billingSubscriberTransaction->transaction_types           = $this->model->getTransactionType();
        $billingSubscriberTransaction->payment_type                = $this->model->getPaymentType();
        $billingSubscriberTransaction->credit                      = $this->model->getCredit();
        $billingSubscriberTransaction->debit                       = $this->model->getDebit();
        $billingSubscriberTransaction->balance                     = $this->model->getBalance();
        $billingSubscriberTransaction->user_package_assign_type_id = $this->model->getPackageAssignType();
        $billingSubscriberTransaction->transaction_date            = $this->model->getTransactionDate();
        $billingSubscriberTransaction->parent_id                   = $this->model->getParentId();
        $billingSubscriberTransaction->created_by                  = $this->model->getParentId();
        $billingSubscriberTransaction->save();
        return $billingSubscriberTransaction;
    }
    
    

//put your code here
}

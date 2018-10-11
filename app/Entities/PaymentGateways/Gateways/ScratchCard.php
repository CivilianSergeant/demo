<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11/22/2016
 * Time: 1:44 PM
 */

namespace App\Entities\PaymentGateways\Gateways;

use App\Entities\PaymentGateways\PaymentGateway;
use App\Entities\BillingSubscriberTransaction;
use App\Entities\Transaction\ScratchCardTransaction;
class ScratchCard extends PaymentGateway
{
    private $model; 
    
    public function __construct($request=null) {
        parent::__construct($request);
    }
    
    public function credit()
    {
        if(empty($this->model)){
           throw new \Exception("Scratch Card Model Empty", 400);
        }
        
        $transaction = $this->_saveTransaction();
        if(!empty($transaction->id)){
            $this->_saveScratchCardTransaction($transaction->id);
        }
        

        return $transaction;
        
    }
    
    private function _saveTransaction()
    {
        $billingSubscriberTransaction = new BillingSubscriberTransaction();
        $billingSubscriberTransaction->pairing_id = 0;
        $billingSubscriberTransaction->subscriber_id = $this->model->getCustomerId();
        $billingSubscriberTransaction->lco_id = $this->model->getOperatorId();
        $billingSubscriberTransaction->package_id = $this->model->getPackageId();
        $billingSubscriberTransaction->item_type = $this->model->getItemType();
        $billingSubscriberTransaction->payment_method_id = $this->model->getPaymentMethodType();
        $billingSubscriberTransaction->transaction_types = $this->model->getTransactionType();
        $billingSubscriberTransaction->credit = $this->model->getCredit();
        $billingSubscriberTransaction->debit = $this->model->getDebit();
        $billingSubscriberTransaction->balance = $this->model->getBalance();
        $billingSubscriberTransaction->user_package_assign_type_id = $this->model->getPackageAssignType();
        $billingSubscriberTransaction->transaction_date = $this->model->getTransactionDate();
        $billingSubscriberTransaction->parent_id = $this->model->getParentId();
        $billingSubscriberTransaction->created_at = $this->model->getTransactionDate();
        $billingSubscriberTransaction->created_by = $this->model->getParentId();
        $billingSubscriberTransaction->save();
        return $billingSubscriberTransaction;
    }
    
    private function _saveScratchCardTransaction($transactionId)
    {
        $scratchTransaction = new ScratchCardTransaction();
        $scratchTransaction->subscriber_transaction_id = $transactionId;
        $scratchTransaction->subscriber_id = $this->model->getCustomerId();
        $scratchTransaction->lco_id = $this->model->getOperatorId();
        $scratchTransaction->parent_id = $this->model->getParentId();
        $scratchTransaction->serial_no = $this->model->getSerialNo();
        $scratchTransaction->card_no = $this->model->getCardNo();
        $scratchTransaction->amount = $this->model->getCredit();
        $scratchTransaction->created_at = $this->model->getTransactionDate();
        $scratchTransaction->created_by = $this->model->getParentId();
        $scratchTransaction->save();
        
    }

    public function debit()
    {
        // TODO: Implement debit() method.
    }
    
    public function setModel($model) {
        $this->model = $model;
    }



    
}
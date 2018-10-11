<?php

namespace App\Entities\PaymentGateways\GatewayModels;
use App\Entities\PaymentGateways\PaymentGateway;
class WalletModel {
    
    private $customerId;
    private $operatorId;
    private $packageId;
    private $contentId;
    private $itemType;
    private $paymentMethodType;
    private $transactionType;
    private $credit;
    private $debit;
    private $balance;
    private $transactionDate;
    private $packageAssignType;
    private $parentId;
    private $paymentType;
    
    public function setPurchasePackageModel($user,$package,$balance,$currentTimestamp)
    {
        $this->setCustomerId($user->id);
        $this->setOperatorId($user->parent_id);
        $this->setPackageId($package->id);
        $this->setItemType(PaymentGateway::ITEM_TYPE_PKG);
        $this->setPaymentMethodType(PaymentGateway::WALLET);
        $this->setTransactionType(PaymentGateway::TRANSACTION_TYPE_D);
        $this->setCredit(0);
        $this->setDebit($package->price);
        $this->setBalance(($balance - $package->price));
        $this->setPackageAssignType(PaymentGateway::PKG_ASSIGN_TYPE_1);
        $this->setTransactionDate(date("Y-m-d H:i:s",$currentTimestamp));
        $this->setParentId($user->parent_id);
    }
    
    public function setPurchaseProgramModel($user,$content,$balance,$currentTimestamp)
    {
        $this->setCustomerId($user->id);
        $this->setOperatorId($user->parent_id);
        $this->setPackageId($content->id);
        $this->setItemType(PaymentGateway::ITEM_TYPE_IND);
        $this->setPaymentMethodType(PaymentGateway::WALLET);
        $this->setTransactionType(PaymentGateway::TRANSACTION_TYPE_D);
        $this->setCredit(0);
        $this->setDebit($content->individual_price);
        $this->setBalance(($balance - $content->individual_price));
        $this->setPackageAssignType(PaymentGateway::PKG_ASSIGN_TYPE_10);
        $this->setTransactionDate(date("Y-m-d H:i:s",$currentTimestamp));
        $this->setParentId($user->parent_id);
    }
    
    public function getCustomerId() {
        return $this->customerId;
    }

    public function getOperatorId() {
        return $this->operatorId;
    }

    public function getPackageId() {
        return $this->packageId;
    }
    
    public function getContentId(){
        return $this->contentId;
    }

    public function getItemType() {
        return $this->itemType;
    }

    public function getPaymentMethodType() {
        return $this->paymentMethodType;
    }

    public function getTransactionType() {
        return $this->transactionType;
    }

    public function getCredit() {
        return $this->credit;
    }

    public function getDebit() {
        return $this->debit;
    }

    public function getBalance() {
        return $this->balance;
    }

    public function getTransactionDate() {
        return $this->transactionDate;
    }
    
    public function getPackageAssignType() {
        return $this->packageAssignType;
    }

    public function getParentId() {
        return $this->parentId;
    }

    public function getPaymentType() {
        return $this->paymentType;
    }
    
    public function setCustomerId($customerId) {
        $this->customerId = $customerId;
    }

    public function setOperatorId($operatorId) {
        $this->operatorId = $operatorId;
    }

    public function setPackageId($packageId) {
        $this->packageId = $packageId;
    }
    
    public function setContentId($contentId) {
        $this->contentId = $contentId;
    }

    public function setItemType($itemType) {
        $this->itemType = $itemType;
    }

    public function setPaymentMethodType($paymentMethodType) {
        $this->paymentMethodType = $paymentMethodType;
    }

    public function setTransactionType($transactionType) {
        $this->transactionType = $transactionType;
    }

    public function setCredit($credit) {
        $this->credit = $credit;
    }

    public function setDebit($debit) {
        $this->debit = $debit;
    }

    public function setBalance($balance) {
        $this->balance = $balance;
    }

    public function setTransactionDate($transactionDate) {
        $this->transactionDate = $transactionDate;
    }
    
    public function setPackageAssignType($packageAssignType) {
        $this->packageAssignType = $packageAssignType;
    }

    public function setParentId($parentId) {
        $this->parentId = $parentId;
    }
    
    public function setPaymentType($paymentType) {
        $this->paymentType = $paymentType;
    }




}

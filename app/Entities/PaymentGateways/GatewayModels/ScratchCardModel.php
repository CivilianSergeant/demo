<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Entities\PaymentGateways\GatewayModels;
use App\Entities\PaymentGateways\PaymentGateway;
class ScratchCardModel {
   
    private $customerId;
    private $operatorId;
    private $packageId;
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
    private $serialNo;
    private $cardNo;
    
    public function getCreditModel($user,$scratchCard)
    {
        $currentBalance = $user->getBalance();
        $this->setCustomerId($user->id);
        $this->setOperatorId($user->parent_id);
        $this->setPaymentMethodType(PaymentGateway::SCRATCH_CARD);
        $this->setTransactionType(PaymentGateway::TRANSACTION_TYPE_C);
        $this->setCredit($scratchCard->value);
        $this->setDebit(0);
        $this->setBalance($currentBalance+$scratchCard->value);
        $this->setPackageAssignType(PaymentGateway::PKG_ASSIGN_TYPE_8);
        $this->setTransactionDate(date("Y-m-d H:i:s",time()));
        $this->setParentId($user->parent_id);
        $this->setSerialNo($scratchCard->serial_no);
        $this->setCardNo($scratchCard->card_no);
        return $this;
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
    
    public function getSerialNo() {
        return $this->serialNo;
    }

    public function getCardNo() {
        return $this->cardNo;
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
    
    public function setSerialNo($serialNo) {
        $this->serialNo = $serialNo;
    }

    public function setCardNo($cardNo) {
        $this->cardNo = $cardNo;
    }




}

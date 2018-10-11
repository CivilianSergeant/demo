<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Entities\PaymentGateways;
use App\Interfaces\Transaction;

class PaymentGateway implements Transaction{
    
    protected $request = null;
    const ITEM_TYPE_PKG = 'PACKAGE';
    const ITEM_TYPE_IND = 'INDIVIDUAL';
    
    const PKG_ASSIGN_TYPE_1 = 1;   // Package Assign
    const PKG_ASSIGN_TYPE_2 = 2;   // Package Migrate
    const PKG_ASSIGN_TYPE_3 = 3;   // Charge Fee
    const PKG_ASSIGN_TYPE_4 = 4;   // Cash Receive
    const PKG_ASSIGN_TYPE_5 = 5;   // Package Re-assign
    const PKG_ASSIGN_TYPE_6 = 6;   // Bank Receive
    const PKG_ASSIGN_TYPE_7 = 7;   // POS Receive
    const PKG_ASSIGN_TYPE_8 = 8;   // Scratch Card Receive
    const PKG_ASSIGN_TYPE_9 = 9;   // Bkash Receive
    const PKG_ASSIGN_TYPE_10 = 10; // Content Assign
    const TRANSACTION_TYPE_C = 'C';
    const TRANSACTION_TYPE_D = 'D';
    
    const WALLET = 8;
    const SCRATCH_CARD = 3;
    
    public function __construct($request) {
        $this->request = $request;
    }
    
    public  function credit() {
        // Add method defination here
    }

    public function debit() {
        // Add method defination here
    }


}

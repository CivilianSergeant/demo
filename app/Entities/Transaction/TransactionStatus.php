<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Entities\Transaction;

/**
 * Description of TransactionStatus
 *
 * @author Himel
 */
class TransactionStatus {
    
    
    private $status;
    private $transaction;
    
    public function __construct($status,$transaction) {
        $this->status = $status;
        $this->transaction = $transaction;
    }
    
    function getStatus() {
        return $this->status;
    }

    function getTransaction() {
        return $this->transaction;
    }


}

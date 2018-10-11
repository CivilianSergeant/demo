<?php

namespace App\Interfaces;

interface Transaction
{
    public function credit();

    public function debit();
}
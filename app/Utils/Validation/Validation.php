<?php
namespace App\Utils\Validation;
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11/24/2016
 * Time: 12:36 PM
 */
abstract class Validation
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public abstract function validate();
}
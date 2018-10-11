<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10/13/2016
 * Time: 3:51 PM
 */

namespace App\Utils\Email;


class PhpmailerException extends  \Exception
{
    /**
     * Prettify error message output
     * @return string
     */
    public function errorMessage() {
        $errorMsg = '<strong>' . $this->getMessage() . "</strong><br />\n";
        return $errorMsg;
    }
}
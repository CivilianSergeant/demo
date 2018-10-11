<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 8/8/2016
 * Time: 1:08 PM
 */

namespace App\Utils;


use Illuminate\Support\Facades\Config;

class Response
{
    private $status;
    private $product;
    private $version;
    private $apiName;
    private $response;
    private $errorCode;
    private $errorMsgTitle;
    private $errorMsg;
    private $debugCode;
    private $debugMsg;

    const NOTIFICATION = "NOTIFICATION";
    const TOAST = "TOAST";
    const DIALOG = "DIALOG";
    const VIEW = "VIEW";
    const NONE = "NONE";


    public function __construct($apiName="")
    {
        $this->status = 0;
        $this->product = Config::get('app.product.title');
        $this->version = Config::get('app.product.version');
        $this->apiName = $apiName;
        $this->response = null;
        $this->errorCode = 0;
        $this->errorMsgTitle = "Error Occured";
        $this->errorMsg = "";
        $this->debugCode = 0;
        $this->debugMsg = "";
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param mixed $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param mixed $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return mixed
     */
    public function getApiName()
    {
        return $this->apiName;
    }

    /**
     * @param mixed $apiName
     */
    public function setApiName($apiName)
    {
        $this->apiName = $apiName;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param mixed $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * @return mixed
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @param mixed $errorCode
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;
    }
    
    public function getErrorMsgTitle() {
        return $this->errorMsgTitle;
    }

    public function setErrorMsgTitle($errorMsgTitle) {
        $this->errorMsgTitle = (!empty($errorMsgTitle))? $errorMsgTitle : 'Error Occured';
    }

    
    /**
     * @return mixed
     */
    public function getErrorMsg()
    {
        return $this->errorMsg;
    }

    /**
     * @param mixed $errorMsg
     */
    public function setErrorMsg($errorMsg)
    {
        $this->errorMsg = $errorMsg;
    }

    /**
     * @return mixed
     */
    public function getDebugCode()
    {
        return $this->debugCode;
    }

    /**
     * @param mixed $debugCode
     */
    public function setDebugCode($debugCode)
    {
        $this->debugCode = $debugCode;
    }

    /**
     * @return mixed
     */
    public function getDebugMsg()
    {
        return $this->debugMsg;
    }

    /**
     * @param mixed $debugMsg
     */
    public function setDebugMsg($debugMsg)
    {
        $this->debugMsg = $debugMsg;
    }



    public function __toString()
    {
        $array = array(
            'status'     => $this->status,
            'product'    => $this->product,
            'version'    => $this->version,
            'apiName'    => $this->apiName,
            'response'   => $this->response,
            'errorCode'  => $this->errorCode,
            'errorMsg'   => $this->errorMsg,
            'debugCode'  => $this->debugCode,
            'debugMsg'   => $this->debugMsg

        );

        return json_encode($array);
    }




}
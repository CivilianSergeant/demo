<?php

namespace App\Entities\PaymentGateways\Gateways;

use App\Entities\ApiSetting;
use App\Entities\OrganizationInfo;
use App\Entities\PaymentGateways\PaymentGateway;
use Illuminate\Support\Facades\Config;

class Bkash extends PaymentGateway
{
    protected $info;
    public function __construct($request=null) {
        parent::__construct($request);
    }

    public function getInfo()
    {
        $organization = OrganizationInfo::find(1);
        $apiSettings = ApiSetting::find(1);
        $this->info = $apiSettings->default_image_path.$organization->bkash_info_img_url;

        return ['info'=>$this->info,'merchantNumber'=>Config::get('app.bkashMerchantNumber')];
    }

    public function credit()
    {
        // TODO: Implement credit() method.
    }

    public function debit()
    {
        // TODO: Implement debit() method.
    }
}
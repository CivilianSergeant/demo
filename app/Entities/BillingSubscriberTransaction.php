<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10/17/2016
 * Time: 3:03 PM
 */

namespace App\Entities;


use Illuminate\Database\Eloquent\Model;

class BillingSubscriberTransaction extends Model
{

    const INDIVIDUAL = "INDIVIDUAL";
    const PACKAGE    = "PACKAGE";

    protected $table = "billing_subscriber_transactions";

    protected $fillable = array(
        'pairing_id','subscriber_id','lco_id','package_id','item_type','payment_method_id','transaction_types','credit',
        'debit','discount','vat_amount','balance','payment_type','vod','user_package_assign_type_id','demo',
        'collection_date','transaction_date','parent_id','created_by'
    );
}
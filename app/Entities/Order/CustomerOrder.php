<?php
namespace App\Entities\Order;
use Illuminate\Database\Eloquent\Model;

class CustomerOrder extends Model{
    
    protected $table = "customer_orders";
    
    const ITEM_TYPE_PROGRAM = 'PROGRAM';
    const ITEM_TYPE_PACKAGE = 'PACKAGE';
    const TRS_STATUS_NEW    = 'NEW';
    const TRS_STATUS_PENDING = 'PENDING';
    const TRS_STATUS_COMPLETED = 'COMPLETED';
    const TRS_STATUS_CANCELED  = 'CANCELED';
    const TRS_STATUS_FALIED    = 'FALIED';
    const TRS_STATUS_VALID     = 'VALID';
    const TRS_STATUS_INVALID   = 'INVALID';
    
    protected $fillable = ['transaction_id','customer_id','item_id','item_type','amount','success_url','cancel_url','fail_url','store_id'];
    

    

}

<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10/8/2016
 * Time: 1:10 PM
 */

namespace App\Entities;


use Illuminate\Database\Eloquent\Model;

class ServiceOperator extends Model
{
    protected $table="service_operators";

    protected $hidden=['id','parent_id','created_by','updated_by','created_at','updated_at'];
}
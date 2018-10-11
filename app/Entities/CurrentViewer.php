<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10/2/2016
 * Time: 12:59 PM
 */

namespace App\Entities;


use Illuminate\Database\Eloquent\Model;

class CurrentViewer extends Model
{
    protected $table = "current_viewers";

    protected $fillable = ['customer_id','content_id','content_type','lat','lon'];
}
<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10/6/2016
 * Time: 1:44 PM
 */

namespace App\Entities;


use Illuminate\Database\Eloquent\Model;

class DbVersion extends Model
{
    protected $table = "db_versions";
}
<?php

namespace App\Entities;


use Illuminate\Database\Eloquent\Model;

class SmsPushLog extends Model
{
    protected $table = "log_sms_push";

    protected $fillable = array('response','content','send_number');
}
<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class FcmDeviceGroup extends Model
{
    protected $table = "fcm_device_groups";

    const DEVICE_LIMIT=1000;
}

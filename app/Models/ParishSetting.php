<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParishSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];
}

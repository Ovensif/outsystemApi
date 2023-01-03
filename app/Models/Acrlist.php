<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acrlist extends Model
{
    protected $table = 'acr_list';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'cr_number',
        'user_displayname',
        'group_name',
        'status',
        'regional',
        'type'
    ];

    use HasFactory;
}

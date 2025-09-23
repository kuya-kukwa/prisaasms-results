<?php

namespace App\Models\Layer1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


class Division extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name'];
}

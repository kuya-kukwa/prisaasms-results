<?php

namespace App\Models\Layer4;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Layer3\Result;

class Medal extends Model
{
    use HasFactory;

    protected $fillable = ['result_id', 'type']; // gold, silver, bronze

    public function result()
    {
        return $this->belongsTo(Result::class);
    }
}

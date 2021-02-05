<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class ModTestES extends Model
{
    use HasFactory,Searchable;

    public $table = "test_es";
}

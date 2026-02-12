<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{ 
    //esto no es buena practica en entornos productivos
    use HasFactory;
    protected $guarded = [];
}

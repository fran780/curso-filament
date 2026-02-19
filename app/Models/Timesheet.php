<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Timesheet extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $fillable = [
        'calendar_id',
        'user_id',
        'type',
        'day_in',
        'day_out',
    ];

    protected $casts = [
        'day_in' => 'datetime',
        'day_out' => 'datetime',
    ];
    
    //Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function calendar()
    {
        return $this->belongsTo(Calendar::class);
    }
}

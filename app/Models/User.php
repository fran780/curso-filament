<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Altwaireb\World\Models\Country;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    //esto sirve para mandar al usuario al panel que le corresponde
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->hasRole('super_admin');
        }
        if ($panel->getId() === 'personal') {
            return true;
        }
        return false;
    }

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;
    use HasPanelShield;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'country_id',
        'state_id',
        'city_id',
        'address',
        'postal_code'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    //relación para que funcione country con nombre
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function calendars()
    {
        return $this->belongsToMany(Calendar::class);
    }

    public function departments()
    {
        return $this->belongsToMany(Departament::class); //uno o varios
    }

    public function holidays()
    {
        return $this->hasMany(Holiday::class);
    }

    public function timesheets()
    {
        return $this->hasMany(Timesheet::class); //muchos
    }
}

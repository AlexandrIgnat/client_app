<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    /** @use HasFactory<\Database\Factories\AppointmentFactory> */
    use HasFactory;
    protected $fillable = ['name', 'email', 'phone']; // пример полей

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}

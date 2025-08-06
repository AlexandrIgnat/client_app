<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    /** @use HasFactory<\Database\Factories\AppointmentFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'start_time',
        'price',
        'comments',
        'client_id',
        'open',
    ];

    protected $casts = [
        'start_time' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
    
    public function scopePaid($query)
    {
        return $query->whereNotNull('price')->where('price', '>', 0);
    }
}

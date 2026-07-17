<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppMessage extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_message_history';

    protected $fillable = [
        'appointment_id',
        'phone_number',
        'message_type',
        'message_content',
        'status',
        'message_id',
        'error_message',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}

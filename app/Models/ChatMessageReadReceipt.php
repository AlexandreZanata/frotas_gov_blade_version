<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessageReadReceipt extends Model {
    use HasFactory, HasUuids;
    protected $fillable = ['chat_message_id', 'user_id', 'read_at'];
    protected $casts = ['read_at' => 'datetime'];
}

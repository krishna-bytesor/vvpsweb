<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstantChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'swami_id',
        'latest_message',
    ];

    protected $appends = ['last_seen'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function swami()
    {
        return $this->belongsTo(User::class, 'swami_id');
    }

    public function getLastSeenAttribute()
    {
        return $this->updated_at->diffForHumans();
    }
}

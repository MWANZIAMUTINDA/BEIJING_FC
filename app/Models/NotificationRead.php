<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationRead extends Model
{
    protected $table = 'notification_reads';

    protected $fillable = ['announcement_id', 'user_id', 'read_at'];

    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }

    public function announcement() { return $this->belongsTo(Announcement::class); }
    public function user()         { return $this->belongsTo(User::class); }
}

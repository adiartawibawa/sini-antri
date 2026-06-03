<?php

use App\Models\Queue;
use Illuminate\Support\Facades\Broadcast;

// Channel publik - tidak perlu auth
Broadcast::channel('operator-dashboard', fn () => true);
Broadcast::channel('display-screen', fn () => true);

// Channel privat per tiket pengunjung
// Pengunjung mana pun yang punya UUID yang valid boleh subscribe
Broadcast::channel('ticket.{uuid}', function ($user, $uuid) {
    // Channel ini public (tidak perlu login), cukup validasi UUID ada di DB
    return Queue::where('uuid', $uuid)->exists();
});

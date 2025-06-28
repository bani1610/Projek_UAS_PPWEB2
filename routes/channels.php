<?php

use App\Models\Group;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{receiver_id}', function ($user, $receiver_id) {
    return (int) $user->id === (int) $receiver_id;
});


Broadcast::channel('group.{group_id}', function ($user, $group_id) {
    $group = Group::find($group_id);
    return $group && $group->members->contains($user->id);
});

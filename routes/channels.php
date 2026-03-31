<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return $user->id === $id;
});

Broadcast::channel('tenant.{tenantId}', function ($user, $tenantId) {
    return $user->tenant_id === $tenantId;
});

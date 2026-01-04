<?php
$users = \App\Models\User::all();
foreach($users as $user) {
    echo "ID: " . $user->id . " | Name: " . $user->name . " | Username: " . $user->username . "\n";
}

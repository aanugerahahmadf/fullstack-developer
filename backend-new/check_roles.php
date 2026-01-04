<?php
$roles = \Spatie\Permission\Models\Role::all();
foreach($roles as $role) {
    echo "ID: " . $role->id . " | Name: " . $role->name . "\n";
}

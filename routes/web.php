<?php

use App\Models\Staff;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    //return view('welcome');
    return redirect('/backoffice');
});

Route::get('staff/{staff_id}', function ($staff_id) {
    $staff = Staff::where('staff_id', $staff_id)->first();
    return view('staff_verify', compact('staff'));
});

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Region;
use Alert;
use Validator;
use Session;

use Illuminate\Support\Facades\Artisan;

class PullController extends Controller
{
    public function runRefreshCommand()
    {
        try {
            // Run the 'app:refresh' command
            Artisan::call('app:refresh');

            // Get the output of the command
            $output = Artisan::output();

            return response()->json(['success' => true, 'output' => $output]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}

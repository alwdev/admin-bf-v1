<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Logs;

class PaymenthubController extends Controller
{
    // Example method for payment hub logic
    public function index(Request $request)
    {
        // Placeholder: implement payment hub logic here
        return response()->json(['message' => 'PaymenthubController index reached.'], 200);
    }

    public function processPayment(Request $request)
    {
        $log = new Logs;
        $log->log = $request->sms;
        $log->save();

        return response()->json([
            'message' => 'Payment processed successfully.',
            'request' => $request->all()
        ], 200);
    }
}

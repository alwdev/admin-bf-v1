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
        $log->log ="processPayment : " .$request->sms." | " .$request->web." ,".$request->all();
        $log->save();
        if ($request->sms != null && $request->sms != '' && $request->web != '' && $request->web != null) {
            // Process the payment logic here
            // For example, you might want to validate the request, process the payment, etc.
            // This is just a placeholder for demonstration purposes.

            $url = "https://".$request->web.'/api/smsRequest?sms='.$request->sms;
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
            ));

            $response = curl_exec($curl);
            if (curl_errno($curl)) {
                $error_msg = curl_error($curl);
                return response()->json(['error' => 'Curl error: ' . $error_msg], 500);
            }
            curl_close($curl);
            return response()->json($request->all(), 200);
        } else {
            return response()->json(['message' => 'No SMS data provided.'], 400);
        }


    }
}

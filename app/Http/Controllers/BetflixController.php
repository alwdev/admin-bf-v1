<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Members;

class BetflixController extends Controller
{
    public function RandomStringRef($length = 5) {
		$characters = '0123456789';
		$charactersLength = strlen($characters);
		$randomString1 = '';
		$randomString2 = '';
		$randomString3 = '';
		$randomString4 = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString1 .= $characters[rand(0, $charactersLength - 1)];
			$randomString2 .= $characters[rand(0, $charactersLength - 1)];
			$randomString3 .= $characters[rand(0, $charactersLength - 1)];
			$randomString4 .= $characters[rand(0, $charactersLength - 1)];
		}
		return "TermTem88_" . $randomString1 . "_" . $randomString2 . "_" . $randomString3 . "_" . $randomString4;
	}
    public function Balance($username){

		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://api.bfx.fail/v4/user/balance',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS =>'username='.env('BF_AGENT').$username,
			CURLOPT_HTTPHEADER => array(
				'x-api-cat: '.env('API_CAT'),
				'x-api-key: '.env('API_KEY'),
				'Content-Type: application/x-www-form-urlencoded'
			),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		if(curl_errno($curl)){
			return "0.00";
		}else{
			$status_response = json_decode($response);
			if($status_response->status == 'success'){
                $member = Members::where('username', '=', auth()->user()->username)->first();
                $member->update(['wallet_balance' => $status_response->data->balance]);
				return $status_response->data->balance;
			}else{
				return "0.00";
			}
		}
	}

	public function Master_Withdraw($username,$credit){
		$ran = $this->RandomStringRef();

		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://api.bfx.fail/v4/user/transfer',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => 'username='.env('BF_AGENT').$username.'&amount=-'.$credit.'&ref=with'.$ran,
			CURLOPT_HTTPHEADER => array(
				'x-api-cat: '.env('API_CAT'),
				'x-api-key: '.env('API_KEY'),
				'Content-Type: application/x-www-form-urlencoded'
			),
		));
		$response = curl_exec($curl);
		if(curl_errno($curl)){
			$status = "error";
		}else{
			$status_response = json_decode($response);
			if($status_response->status == 'success'){
				$status = "success";
			}else{
				$status = "error";
			}
		}
		curl_close($curl);
		return $status;

	}

	public function Master_Deposit($username,$credit){
		$ran = $this->RandomStringRef();

		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://api.bfx.fail/v4/user/transfer',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => 'username='.env('BF_AGENT').$username.'&amount='.$credit.'&ref=dps'.$ran,
			CURLOPT_HTTPHEADER => array(
				'x-api-cat: '.env('API_CAT'),
				'x-api-key: '.env('API_KEY'),
				'Content-Type: application/x-www-form-urlencoded'
			),
		));
		$response = curl_exec($curl);
		if(curl_errno($curl)){
			$status = "error";
		}else{
			$status_response = json_decode($response);
			if($status_response->status == 'success'){
				$status = "success";
			}else{
				$status = "error";
			}
		}
		curl_close($curl);
		return $status;

	}

	public function Master_Agent(){

		$headers = array();
		$headers[] = 'Content-Type: application/x-www-form-urlencoded';
        $headers[] = 'x-api-cat: '.env('API_CAT');
		$headers[] = 'x-api-key: '.env('API_KEY');

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://api.bfx.fail/v4/agent/balance?upline='.env('BF_AGENT'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        $response = curl_exec($curl);
		curl_close($curl);
		if(curl_errno($curl)){
			return "error";
		}else{
			$status_response = json_decode($response);
			if($status_response->status == 'success'){
				return $status_response->data->total_credit;
			}else{
				return "error";
			}
		}

	}

	public function Master_TurnOver($username){

		date_default_timezone_set("Asia/Bangkok");
		$start_date=date('Y-m-d',strtotime('-1 day'));
		$end_date=date('Y-m-d',strtotime('-1 day'));

		$headers = array();
		$headers[] = 'Content-Type: application/x-www-form-urlencoded';
        $headers[] = 'x-api-cat: '.env('API_CAT');
		$headers[] = 'x-api-key: '.env('API_KEY');

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://api.bfx.fail/v4/report/summary?start='.$start_date.'&end='.$end_date.'&username='.env('BF_AGENT').$username);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        $response = curl_exec($curl);
		curl_close($curl);
		if(curl_errno($curl)){
			return "error";
		}else{
			$status_response = json_decode($response);
			if($status_response->status == 'success'){
				return $status_response->data->winloss;
			}else{
				return "error";
			}
		}

	}
	public function Master_TurnOver22($username){

		date_default_timezone_set("Asia/Bangkok");
		$start_date=date('Y-m-d',strtotime('-7 day'));
		$end_date=date('Y-m-d',strtotime('-1 day'));

		$headers = array();
		$headers[] = 'Content-Type: application/x-www-form-urlencoded';
        $headers[] = 'x-api-cat: '.env('API_CAT');
		$headers[] = 'x-api-key: '.env('API_KEY');

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://api.bfx.fail/v4/report/summary?start='.$start_date.'&end='.$end_date.'&username='.env('BF_AGENT').$username);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        $response = curl_exec($curl);
		curl_close($curl);
		if(curl_errno($curl)){
			return "error";
		}else{
			$status_response = json_decode($response);
			if($status_response->status == 'success'){
				return $status_response->data->winloss;
			}else{
				return "error";
			}
		}

	}
}

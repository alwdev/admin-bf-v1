<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Members;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class BetflixController extends Controller
{
	public function RandomStringRef($length = 5)
	{
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
	public function Balance($username)
	{

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
			CURLOPT_POSTFIELDS => 'username=' . env('BF_AGENT') . $username,
			CURLOPT_HTTPHEADER => array(
				'x-api-cat: ' . env('API_CAT'),
				'x-api-key: ' . env('API_KEY'),
				'Content-Type: application/x-www-form-urlencoded'
			),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		if (curl_errno($curl)) {
			return "0.00";
		} else {
			$status_response = json_decode($response);
			if ($status_response->status == 'success') {
				return $status_response->data->balance;
			} else {
				return "0.00";
			}
		}
	}

	public function Master_Withdraw($username, $credit)
	{
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
			CURLOPT_POSTFIELDS => 'username=' . env('BF_AGENT') . $username . '&amount=-' . $credit . '&ref=with' . $ran,
			CURLOPT_HTTPHEADER => array(
				'x-api-cat: ' . env('API_CAT'),
				'x-api-key: ' . env('API_KEY'),
				'Content-Type: application/x-www-form-urlencoded'
			),
		));
		$response = curl_exec($curl);
		if (curl_errno($curl)) {
			$status = "error";
		} else {
			$status_response = json_decode($response);
			if ($status_response->status == 'success') {
				$status = "success";
			} else {
				$status = "error " . $status_response->msg;
			}
		}
		curl_close($curl);
		return $status;
	}

	public function Master_Deposit($username, $credit)
	{
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
			CURLOPT_POSTFIELDS => 'username=' . env('BF_AGENT') . $username . '&amount=' . $credit . '&ref=dps' . $ran,
			CURLOPT_HTTPHEADER => array(
				'x-api-cat: ' . env('API_CAT'),
				'x-api-key: ' . env('API_KEY'),
				'Content-Type: application/x-www-form-urlencoded'
			),
		));
		$response = curl_exec($curl);
		// Log::info(json_encode($response));
		if (curl_errno($curl)) {
			$status = "curl error";
		} else {
			$status_response = json_decode($response);
			if ($status_response->status == 'success') {
				$status = "success";
			} else {
				$status = "error " . $status_response->msg;
			}
		}
		curl_close($curl);
		return $status;
	}

	public function Master_Agent()
	{

		$headers = array();
		$headers[] = 'Content-Type: application/x-www-form-urlencoded';
		$headers[] = 'x-api-cat: ' . env('API_CAT');
		$headers[] = 'x-api-key: ' . env('API_KEY');

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'https://api.bfx.fail/v4/agent/balance?upline=' . env('BF_AGENT'));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

		$response = curl_exec($curl);
		curl_close($curl);
		if (curl_errno($curl)) {
			return "error";
		} else {
			$status_response = json_decode($response);
			if ($status_response->status == 'success') {
				return $status_response->data->total_credit;
			} else {
				return $status_response;
			}
		}
	}

	public function lastDay_TurnOver($username)
	{

		date_default_timezone_set("Asia/Bangkok");;

		$start_date = Carbon::yesterday()->isoFormat('YYYY-MM-DD') . '%2000%3A00%3A00';
		$end_date = Carbon::yesterday()->isoFormat('YYYY-MM-DD') . '%2023%3A59%3A59';

		error_log($start_date);
		error_log($end_date);

		$headers = array();
		$headers[] = 'Content-Type: application/x-www-form-urlencoded';
		$headers[] = 'x-api-cat: ' . env('API_CAT');
		$headers[] = 'x-api-key: ' . env('API_KEY');

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'https://api.bfx.fail/v4/report/summarer?username=' . env('BF_AGENT') . $username . '&end=' . $end_date . '&start=' . $start_date);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

		$response = curl_exec($curl);
		curl_close($curl);
		if (curl_errno($curl)) {
			return "curl error";
		} else {
			$status_response = json_decode($response);

			if ($status_response->status == 'success') {
				return $status_response->data;
			} else {
				return $status_response;
			}
		}
	}
	public function last7Day_TurnOver($username)
	{

		date_default_timezone_set("Asia/Bangkok");
		$start_date = Carbon::now()->subDays(7)->isoFormat('YYYY-MM-DD') . '%2000%3A00%3A00';
		$end_date = Carbon::yesterday()->isoFormat('YYYY-MM-DD') . '%2023%3A59%3A59';

		error_log($start_date);
		error_log($end_date);

		Log::info("last7Day_TurnOver start_date= " . $start_date . ", end_date=" . $end_date . ", username=" . $username);

		$headers = array();
		$headers[] = 'Content-Type: application/x-www-form-urlencoded';
		$headers[] = 'x-api-cat: ' . env('API_CAT');
		$headers[] = 'x-api-key: ' . env('API_KEY');

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'https://api.bfx.fail/v4/report/summarer?username=' . env('BF_AGENT') . $username . '&end=' . $end_date . '&start=' . $start_date);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

		$response = curl_exec($curl);
		curl_close($curl);
		if (curl_errno($curl)) {
			return "error";
		} else {
			$status_response = json_decode($response);
			if ($status_response->status == 'success') {
				return $status_response->data;
			} else {
				return "error";
			}
		}
	}


	public function Multiple_Member_Report($day)
	{

		date_default_timezone_set("Asia/Bangkok");
		$start_date = $day->isoFormat('YYYY-MM-DD');

		error_log($start_date);

		$headers = array();
		$headers[] = 'Content-Type: application/x-www-form-urlencoded';
		$headers[] = 'x-api-cat: ' . env('API_CAT');
		$headers[] = 'x-api-key: ' . env('API_KEY');

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'https://api.bfx.fail/v4/report/summariez?date=' . $start_date . '&page=1&upline=' . env('BF_AGENT'));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

		$response = curl_exec($curl);
		curl_close($curl);
		if (curl_errno($curl)) {
			return "error";
		} else {
			$status_response = json_decode($response);
			if ($status_response->status == 'success') {
				return $status_response->data;
			} else {
				return "error";
			}
		}
	}

	public function Single_Member_Report_all_Provider($username, $start_day, $end_day)
	{

		date_default_timezone_set("Asia/Bangkok");
		$start_date = date('Y-m-d', strtotime($start_day . ' day'));
		$end_date = date('Y-m-d', strtotime($end_day . ' day'));

		// Log::info("Single_Member_Report_all_Provider start_date= ".$start_date.", end_date=".$end_date.", username=".$username);

		$headers = array();
		$headers[] = 'Content-Type: application/x-www-form-urlencoded';
		$headers[] = 'x-api-cat: ' . env('API_CAT');
		$headers[] = 'x-api-key: ' . env('API_KEY');

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'https://api.bfx.fail/v4/report/summaryNEW?start=' . $start_date . '&end=' . $end_date . '&username=' . env('BF_AGENT') . $username);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

		$response = curl_exec($curl);
		curl_close($curl);
		if (curl_errno($curl)) {
			return "curl error";
		} else {
			$status_response = json_decode($response);
			if ($status_response->status == 'success') {
				return $status_response->data;
			} else {
				error_log(json_encode($status_response));
				return $status_response;
			}
		}
	}

	public function Single_Member_Report_all_Provider_EX_Sport($username, $start_day, $end_day)
	{

		date_default_timezone_set("Asia/Bangkok");
		$start_date = date('Y-m-d', strtotime($start_day . ' day'));
		$end_date = date('Y-m-d', strtotime($end_day . ' day'));

		// Log::info("Single_Member_Report_all_Provider start_date= ".$start_date.", end_date=".$end_date.", username=".$username);

		$headers = array();
		$headers[] = 'Content-Type: application/x-www-form-urlencoded';
		$headers[] = 'x-api-cat: ' . env('API_CAT');
		$headers[] = 'x-api-key: ' . env('API_KEY');

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'https://api.bfx.fail/v4/report/summaryNEW?start=' . $start_date . '&end=' . $end_date . '&username=' . env('BF_AGENT') . $username . '&ag=1&aws=1&bg=1&bs=1&cg=1&cq9=1&dg=1&dgs=1&eg=1&ep=1&fc=1&funky=1&gamatron=1&gd88=1&gdg=1&hg=1&jl=1&joker=1&kg=1&km=1&mg=1&netent=1&ng=1&pg=1&pp=1&ps=1&1x2=1&bng=1&bpg=1&ds=1&elk=1&fng=1&ga=1&hab=1&hak=1&ids=1&kgl=1&mav=1&nge=1&nlc=1&png=1&prs=1&pug=1&qs=1&red=1&rlx=1&swl=1&tk=1&waz=1&ygg=1&r88=1&sa=1&sexy=1&sn=1&sp=1&swg=1&ttg=1&we=1&wm=1&xg=1&bonus=1');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

		$response = curl_exec($curl);
		curl_close($curl);
		if (curl_errno($curl)) {
			return "curl error";
		} else {
			$status_response = json_decode($response);
			if ($status_response->status == 'success') {
				return $status_response->data;
			} else {
				error_log(json_encode($status_response));
				return $status_response;
			}
		}
	}

	public function Single_ReportTimeProvider($username, $start_day, $end_day)
	{

		date_default_timezone_set("Asia/Bangkok");
		$start_date = $start_day->isoFormat('YYYY-MM-DD') . '%2000%3A00%3A00';
		$end_date = $end_day->isoFormat('YYYY-MM-DD') . '%2023%3A59%3A59';

		// Log::info("Single_ReportTimeProvider start_date= ".$start_date.", end_date=".$end_date.", username=".$username);

		$headers = array();
		$headers[] = 'Content-Type: application/x-www-form-urlencoded';
		$headers[] = 'x-api-cat: ' . env('API_CAT');
		$headers[] = 'x-api-key: ' . env('API_KEY');

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'https://api.bfx.fail/v4/report/summaroo?start=' . $start_date . '&end=' . $end_date . '&username=' . $username);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

		$response = curl_exec($curl);
		curl_close($curl);
		if (curl_errno($curl)) {
			return "curl error";
		} else {
			$status_response = json_decode($response);
			if ($status_response->status == 'success') {
				return $status_response->data;
			} else {
				return $status_response;
			}
		}
	}

	public function set_user_status($username, $status)
	{

		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://api.bfx.fail/v4/user/statusSetting',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => 'username=' . env('BF_AGENT') . $username . '&status=' . $status,
			CURLOPT_HTTPHEADER => array(
				'x-api-cat: ' . env('API_CAT'),
				'x-api-key: ' . env('API_KEY'),
				'Content-Type: application/x-www-form-urlencoded'
			),
		));
		$response = curl_exec($curl);
		// Log::info(json_encode($response));
		if (curl_errno($curl)) {
			$status = "curl error";
		} else {
			$status_response = json_decode($response);
			if ($status_response->status == 'success') {
				$status = "success";
			} else {
				$status = "error " . $status_response->msg;
			}
		}
		curl_close($curl);
		return $status;
	}
}

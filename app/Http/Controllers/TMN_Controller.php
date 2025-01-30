<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Members;
use App\Models\Transfer;
use App\Models\Bank;
use NotificationChannels\Telegram\TelegramMessage;

class TMN_Controller extends Controller
{
    // private $tmn_key_id = 'x4378025e7a'; //Key ID จากระบบ TMNOne
    // private $mobile_number = '0610687808'; //เบอร์
    // private $login_token = 'L-9b241fd5-efc9-4aba-84cf-dc8e00e66e13'; //login_token จากขั้นตอนการเพิ่มเบอร์
    // private $pin = '168168'; //PIN 6 หลัก
    // private $tmn_id = 'tmn.10136962981'; //tmn_id จากขั้นตอนการเพิ่มเบอร์

    public function index(){

        $TMNOne = new TMNOne();
        $TMNOne->setData(env('TMN_KEY_ID'), env('TMN_MOBILE_NUMBER'), env('TMN_TOKEN'), env('TMN_ID'));
        //$TMNOne->setProxy('proxy_ip:proxy_port', 'proxy_username', 'proxy_password'); //เปิดใช้งาน HTTP Proxy สำหรับเชื่อมต่อกับระบบ
        //$TMNOne->enableDebugging(); //เปิดการใช้ debugging
        $TMNOne->loginWithPin6(env('TMN_PIN'));

        $balance = $TMNOne->getBalance();

        return $balance;
    }

    public function transfer_to_Bank(Request $request){
        Log::info("Transfer to Bank ".$request->getContent());

        try{
            $transfer = Transfer::where('id',$request->transfer_id)->first();
            $transfer->status = 4;
            $transfer->status_code ="กำลังดำเนินการ";
            $transfer->save();

            $TMNOne = new TMNOne();
            $TMNOne->setData(env('TMN_KEY_ID'), env('TMN_MOBILE_NUMBER'), env('TMN_TOKEN'), env('TMN_ID'));
            $TMNOne->loginWithPin6(env('TMN_PIN'));
            $transfer = $TMNOne->transferBankAC($request->bank_code,$request->bank_ac,$request->amount,env('TMN_PIN'));
            $transactionHistory = $TMNOne->fetchTransactionHistory(date('Y-m-d',time()-86400), date('Y-m-d',time()+86400));
            // return response()->json([$transfer,$ransactionHistory[0]],200);

            if ($transfer["transfer_status"] === 'PROCESSING'){
                Log::info("Transfer to Mobile report_id :".$transactionHistory[0]["report_id"]);
                $approve=$this->approvewithdraw($request->transfer_id,$transactionHistory[0]["report_id"]);
                error_log($approve);
                // return response()->json([$transfer,$transactionHistory[0],$approve],200);
                return response()->json(0);
            }else{
                TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                    ->line('BOT '.env('APP_NAME'))
                    ->line('พบข้อผิดพลาดในการ Transfer '.$transfer)
                    ->send();
                return response()->json(400);
            }
        } catch (\Exception $e) {
                Log::info("Transfer to Bank error ".$e->getMessage());
                TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                    ->line('BOT '.env('APP_NAME'))
                    ->line('พบข้อผิดพลาดในการ Transfer '.$e->getMessage())
                    ->send();
                return response()->json(400);
        }
    }

    public function transfer_to_Mobile(Request $request){
        Log::info("Transfer to Mobile ".$request->getContent());
        try{
            $transfer = Transfer::where('id',$request->transfer_id)->first();
            $transfer->status = 4;
            $transfer->status_code ="กำลังดำเนินการ";
            $transfer->save();

            $TMNOne = new TMNOne();
            $TMNOne->setData(env('TMN_KEY_ID'), env('TMN_MOBILE_NUMBER'), env('TMN_TOKEN'), env('TMN_ID'));
            $TMNOne->loginWithPin6(env('TMN_PIN'));
            $transfer = $TMNOne->transferP2P($request->mobile_number,$request->amount,"");
            $transactionHistory = $TMNOne->fetchTransactionHistory(date('Y-m-d',time()-86400), date('Y-m-d',time()+86400));

            if ($transfer["transfer_status"] === 'PROCESSING'){
                Log::info("Transfer to Mobile report_id :".$transactionHistory[0]["report_id"]);
                error_log("Transfer to Mobile report_id :".$transactionHistory[0]["report_id"]);
                $approve = $this->approvewithdraw($request->transfer_id,$transactionHistory[0]["report_id"]);
                error_log($approve);
                // return response()->json([$transfer,$transactionHistory[0],$approve],200);
                return response()->json(0);
            }else{
                TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                    ->line('BOT '.env('APP_NAME'))
                    ->line('พบข้อผิดพลาดในการ Transfer '.$transfer)
                    ->send();
                return response()->json(400);
            }
        } catch (\Exception $e) {
                Log::info("Transfer to Mobile error ".$e->getMessage());
                TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                    ->line('BOT '.env('APP_NAME'))
                    ->line('พบข้อผิดพลาดในการ Transfer '.$e->getMessage())
                    ->send();
                return response()->json(400);
        }

    }

    public function lastTransactionHistory(){
        $TMNOne = new TMNOne();
        $TMNOne->setData(env('TMN_KEY_ID'), env('TMN_MOBILE_NUMBER'), env('TMN_TOKEN'), env('TMN_ID'));
        $TMNOne->loginWithPin6(env('TMN_PIN'));
        $history = $TMNOne->fetchTransactionHistory(date('Y-m-d',time()-86400), date('Y-m-d',time()+86400));
        return $history[0];
    }
    public function fetchTransactionHistory(){
        $TMNOne = new TMNOne();
        $TMNOne->setData(env('TMN_KEY_ID'), env('TMN_MOBILE_NUMBER'), env('TMN_TOKEN'), env('TMN_ID'));
        $TMNOne->loginWithPin6(env('TMN_PIN'));
        $history = $TMNOne->fetchTransactionHistory(date('Y-m-d',time()-86400), date('Y-m-d',time()+86400));
        return $history;
    }

    public function fetchTransactionInfo(Request $request){
        $TMNOne = new TMNOne();
        $TMNOne->setData(env('TMN_KEY_ID'), env('TMN_MOBILE_NUMBER'), env('TMN_TOKEN'), env('TMN_ID'));
        $TMNOne->loginWithPin6(env('TMN_PIN'));
        $history = $TMNOne->fetchTransactionInfo($request->report_id);
        return $history;
    }

    public function approvewithdraw($transfer_id,$ref)
    {
        Log::info("TMN approvewithdraw id ".$transfer_id." ref ".$ref);
        try{
            $transfer = Transfer::where('id',$transfer_id)->first();
            $member = Members::where('id',$transfer->member_id)->first();

            if($transfer->status == 2 || $transfer->status == 3){
                return response()->json(['message' => 'Transfer approved !!!'], 401);
            }


            $transfer->old_balance = $member->wallet_balance;
            $old_balance = $member->wallet_balance;

            $bf_deposit=  app(\App\Http\Controllers\BetflixController::class)->Master_Withdraw($member->username,floor($transfer->amount));
            Log::info('Betflix Withdraw '.$bf_deposit.' '.floor($transfer->amount).' User =  '.$member->username);

            $transfer->ref_id = $ref;
            $transfer->status = 2;
            $transfer->status_code ="BOT.อนุมัติ";
            $transfer->old_balance = $old_balance;
            $transfer->new_balance = $member->wallet_balance;
            $transfer->withdraw_slip = "";
            $transfer->save();

            $truewallet = Bank::where('active',1)->where('bank_name','TrueMoney Wallet')->where('account_no',env('TMN_MOBILE_NUMBER'))->first();
              if($truewallet){
                $truewallet->balance = app(\App\Http\Controllers\TMN_Controller::class)->index();
                $truewallet->save();
            }

            TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                ->line('BOT '.env('APP_NAME'))
                ->line('TMN API ทำรายการโอนสำเร็จ')
                ->line('username :'.$member->username)
                ->line('Bank :'.$member->bank_name)
                ->line('ACC NUMBER :'.$transfer->withdraw_bank_no)
                ->line('จำนวน :'.$transfer->amount)
                ->send();
            error_log('TMN API โอนเงินให้ '.$member->username);
            return response()->json(["status"=>0],200);
        } catch (\Exception $e) {
            TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                ->line('BOT '.env('APP_NAME'))
                ->line('พบข้อผิดพลาดในการ update Transfer '.$e->getMessage())
                ->send();
            error_log('พบข้อผิดพลาดในการ update Transfer '.$e->getMessage());
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }
}

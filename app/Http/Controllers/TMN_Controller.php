<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TMN_Controller extends Controller
{
    private $tmn_key_id = 'x4378025e7a'; //Key ID จากระบบ TMNOne
    private $mobile_number = '0610687808'; //เบอร์
    private $login_token = 'L-9b241fd5-efc9-4aba-84cf-dc8e00e66e13'; //login_token จากขั้นตอนการเพิ่มเบอร์
    private $pin = '168168'; //PIN 6 หลัก
    private $tmn_id = 'tmn.10136962981'; //tmn_id จากขั้นตอนการเพิ่มเบอร์

    public function index(){

        $TMNOne = new TMNOne();
        $TMNOne->setData($this->tmn_key_id, $this->mobile_number, $this->login_token, $this->tmn_id);
        //$TMNOne->setProxy('proxy_ip:proxy_port', 'proxy_username', 'proxy_password'); //เปิดใช้งาน HTTP Proxy สำหรับเชื่อมต่อกับระบบ
        //$TMNOne->enableDebugging(); //เปิดการใช้ debugging
        $TMNOne->loginWithPin6($this->pin); //Login เข้าระบบด้วย PIN

        $balance = $TMNOne->getBalance(); //ตรวจสอบยอดเงินคงเหลือ

        // var_dump($TMNOne->fetchTransactionHistory(date('Y-m-d',time()-86400), date('Y-m-d',time()+86400))); //ดึงรายการเงินเข้าออก

        // var_dump($TMNOne->fetchTransactionInfo('umk1678000000')); //ดึงข้อมูล transaction จาก report_id


        return $balance;
    }

    public function transfer_to_Bank(Request $request){
        $TMNOne = new TMNOne();
        $TMNOne->setData($this->tmn_key_id, $this->mobile_number, $this->login_token, $this->tmn_id);
        $TMNOne->loginWithPin6($this->pin);

        $transfer = $TMNOne->transferBankAC($request->bank_code,$request->bank_ac,$request->amount,$this->pin);

        return $transfer;
    }

    public function transfer_to_Mobile(Request $request){
        $TMNOne = new TMNOne();
        $TMNOne->setData($this->tmn_key_id, $this->mobile_number, $this->login_token, $this->tmn_id);
        $TMNOne->loginWithPin6($this->pin);
        $transfer = $TMNOne->transferP2P($request->mobile_number,$request->amount,"");
        $ransactionHistory = $TMNOne->fetchTransactionHistory(date('Y-m-d',time()-86400), date('Y-m-d',time()+86400));
        return response()->json([$transfer,$ransactionHistory],200);
    }

    public function fetchTransactionHistory(){
        $TMNOne = new TMNOne();
        $TMNOne->setData($this->tmn_key_id, $this->mobile_number, $this->login_token, $this->tmn_id);
        $TMNOne->loginWithPin6($this->pin);
        $history = $TMNOne->fetchTransactionHistory(date('Y-m-d',time()-86400), date('Y-m-d',time()+86400));
        return $history[0];
    }

    public function fetchTransactionInfo(Request $request){
        $TMNOne = new TMNOne();
        $TMNOne->setData($this->tmn_key_id, $this->mobile_number, $this->login_token, $this->tmn_id);
        $TMNOne->loginWithPin6($this->pin);
        $history = $TMNOne->fetchTransactionInfo($request->report_id);
        return $history;
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\BigGameCallbaclk;
use App\Models\History;
use App\Models\CommonCallback;
use App\Models\Payout;
use App\Models\Members;
use App\Models\SAgaming;
use App\Models\SexyCallback;
use App\Models\WmCallback;
use App\Models\Logs;
use App\Models\ProductList;
class HistoryController extends Controller
{
    protected $big_gameID = ["",
        "Baccarat",
        "Roulette",
        "Sicbo",
        "DragonTiger",
        "Redblack",
        "SpeedSicbo",
        "SpeedBaccarat",
        "",
        "Sedie",
        "MiCardBaccarat",
        "FullColorBaccarat",
        "",
        "BullBull",
        "WinThreeCards",
        "CasinoWar"
    ];
    public function sync_history(){
        $this->get_biggame();
        $this->get_common();
        $this->get_supergame(1000);
        $this->get_SAhistory();
        $this->get_Sexyhistory();
        $this->get_wmhistory();
    }
    public function get_biggame(){
        set_time_limit(30000);
        $big_games = BigGameCallbaclk::where('sync',0)->limit(1000)->get();
        $roundId ="";
        foreach($big_games as $big_game){
            $check_data = History::where('request_id',$big_game->request_id)
            ->where('type',$big_game->type)
            ->where('amount',abs($big_game->orders_amount))
            ->first();
            if($big_game->issueId){ $roundId = $big_game->issueId;}
            if(!$check_data){
                $big_game->update(['sync' => 1]);
                $member = Members::where('username', '=', $big_game->loginId)->first();
                $balance = 0;
                if($member){
                    $balance = $member->balance;
                }
                History::create([
                    "request_id" => $big_game->request_id,
                    "roundId" => $roundId,
                    "username" => $big_game->loginId,
                    "game" => $this->big_gameID[$big_game->gameId],
                    "provider" => "Big Gaming",
                    "amount" => abs($big_game->orders_amount),
                    "winlose" => $big_game->orders_amount,
                    "type" => $big_game->type,
                    "playtime" => $big_game->created_at,
                    "balanceBefore" =>  $balance,
                    "balanceAfter" =>  $balance,
                ]);
            }
        }
        echo '*****sync biggame Complete***** = '.count($big_games)."<br>\n";
    }

    public function get_common(){
        set_time_limit(30000);
        $commons = CommonCallback::where('sync',0)->limit(1000)->get();
        $roundId ="";
        $type = "";
        $gameCode = "";
        $amount = 0;
        $winlose = 0;
        foreach($commons as $common){
            $check_data = History::where('request_id',$common->request_id)
            ->where('type',$common->status)
            ->first();
            if(!$check_data){
                $common->update(['sync' => 1]);

                if($common->status == "SETTLED"){
                    if($common->payoutAmount){
                        $winlose = $common->payoutAmount;
                    }
                }else{
                    $winlose = 0;
                    if($common->betAmount){
                        $amount = $common->betAmount;
                    }
                }

                if($common->gameCode){
                    $gameCode = $common->gameCode.'/ '.$common->playInfo;
                }

                if($common->status){
                    $type = $common->status;
                }

                if($common->roundId){ $roundId = $common->roundId;}
                $member = Members::where('username', '=', $common->username)->first();
                $balance = 0;
                if($member){
                    $balance = $member->balance;
                }
                History::create([
                    "request_id" => $common->request_id,
                    "roundId" => $roundId,
                    "username" => $common->username,
                    "game" => $gameCode,
                    "provider" => $common->productId,
                    "amount" => $amount,
                    "winlose" => $winlose,
                    "type" => $type,
                    "playtime" => $common->created_at,
                    "balanceBefore" =>  $balance,
                    "balanceAfter" =>  $balance,
                ]);
            }
        }
        echo '*****sync commons Complete***** = '.count($commons)."<br>\n";
    }

    public function get_supergame($limit){
        set_time_limit(30000);
            $super_games = Payout::where('sync',0)->limit($limit)->get();
            foreach($super_games as $super_game){
                $check_data = History::where('request_id',$super_game->refId)
                ->first();
                if(!$check_data){
                    $super_game->update(['sync' => 1]);
                    $member = Members::where('username', '=', $super_game->username)->first();
                    $balance = 0;
                    if($member){
                        $balance = $member->balance;
                    }
                    History::create([
                        "request_id" => $super_game->refId,
                        "roundId" => $super_game->roundId,
                        "username" => $super_game->username,
                        "game" => $super_game->game,
                        "provider" => $super_game->product,
                        "amount" => $super_game->amount,
                        "winlose" => $super_game->winlose,
                        "type" => $super_game->type,
                        "playtime" => $super_game->created_at,
                        "balanceBefore" =>  $balance,
                        "balanceAfter" =>  $balance,
                    ]);
                }
                sleep(1);
            }
            Logs::create([ "username" => "" ,"log" => 'sync Supergame = '.count($super_games) ]);
    }

    public function get_SAhistory(){
        set_time_limit(30000);
        $saGaming = SAgaming::where('sync',0)->limit(1000)->get();
        foreach($saGaming as $sa_game){
            $check_data = History::where('request_id',$sa_game->txnid)
            ->first();
            if(!$check_data){
                $sa_game->update(['sync' => 1]);
                $member = Members::where('username', '=', $sa_game->username)->first();
                $balance = 0;
                if($member){
                    $balance = $member->balance;
                }
                $winlose = 0;
                $amount = 0;
                if($sa_game->amount){
                    if($sa_game->type == 'win'){
                        $winlose = $sa_game->amount;
                        $amount = 0;
                    }else{
                        $winlose = 0;
                        $amount = $sa_game->amount;
                    }
                }

                History::create([
                    "request_id" => $sa_game->txnid,
                    "roundId" => $sa_game->gameid,
                    "username" => $sa_game->username,
                    "game" => $sa_game->gametype,
                    "provider" => "SA Gaming",
                    "amount" => $amount,
                    "winlose" => $winlose,
                    "type" => $sa_game->type,
                    "playtime" => $sa_game->created_at,
                    "balanceBefore" =>  $balance,
                    "balanceAfter" =>  $balance,
                ]);
            }
        }
        echo '*****sync SA Gaming Complete*****'.count($saGaming)."<br>\n";
    }

    public function get_Sexyhistory(){
        set_time_limit(30000);
        $SexyGaming = SexyCallback::where('sync',0)->limit(1000)->get();
        foreach($SexyGaming as $sexy_game){
            $check_data = History::where('request_id',$sexy_game->platformTxId)
            ->first();
            if(!$check_data){
                $sexy_game->update(['sync' => 1]);
                $member = Members::where('username', '=', $sexy_game->userId)->first();
                $balance = 0;
                if($member){
                    $balance = $member->balance;
                }
                $winlose = 0;
                $amount = 0;
                if($sexy_game->action == 'settle'){
                    $winlose = $sexy_game->betAmount;
                    $amount = 0;
                }else{
                    $winlose = 0;
                    $amount = $sexy_game->betAmount;
                }

                History::create([
                    "request_id" => $sexy_game->platformTxId,
                    "roundId" => $sexy_game->roundId,
                    "username" => $sexy_game->userId,
                    "game" => $sexy_game->gameCode,
                    "provider" => $sexy_game->platform,
                    "amount" => $amount,
                    "winlose" => $winlose,
                    "type" => $sexy_game->action,
                    "playtime" => $sexy_game->created_at,
                    "balanceBefore" =>  $balance,
                    "balanceAfter" =>  $balance,
                ]);

            }
        }
        echo '*****sync SexyGaming Complete***** = '.count($SexyGaming)."<br>\n";
    }

    public function get_WMhistory(){
        set_time_limit(30000);
        $WMGaming = WmCallback::where('sync',0)->limit(1000)->get();
        foreach($WMGaming as $wm_game){
            $check_data = History::where('request_id',$wm_game->betId)
            ->first();
            if(!$check_data){
                $wm_game->update(['sync' => 1]);
                $member = Members::where('username', '=', $wm_game->username)->first();
                $balance = 0;
                if($member){
                    $balance = $member->balance;
                }
                $winlose = 0;
                $amount = 0;
                if($wm_game->type == 'Payout'){
                    $winlose = $wm_game->amount;
                    $amount = 0;
                }else{
                    $winlose = 0;
                    $amount = $wm_game->amount;
                }
                History::create([
                    "request_id" => $wm_game->betId,
                    "roundId" => $wm_game->roundId,
                    "username" => $wm_game->username,
                    "game" => $wm_game->gameId,
                    "provider" => "WM Casino",
                    "amount" => $amount,
                    "winlose" => $winlose,
                    "type" => $wm_game->type,
                    "playtime" => $wm_game->created_at,
                    "balanceBefore" => $balance,
                    "balanceAfter" => $balance,
                ]);
            }
        }
        echo '*****sync WM Gaming Complete*****'.count($WMGaming)."<br>\n";
    }

    //Query Replay
    public function QueryReplay($username,$productId,$betId){
        if($productId == 'SEXYBCRT'){
            $priduct_id = 'SEXY';
        }else{
            $priduct_id = $productId;
        }

        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', env('APP_ASK_API_URL') . '/betReplay',
        [
            'headers' => [
                'Authorization' => 'Basic ' . $this->auth_basic(),
                'Content-Type' => 'application/json'
            ],
            'json' => [
                "username" => $username,
                "productId" => $priduct_id,
                "dataGetBetDetail" => [
                    "betId" => $betId,
                    "walletType" => ""
                ]
            ]
        ]);
        $data = json_decode($response->getBody());
        // dd($data,$productId,$betId);
        if($data->code == 0){
         if(isset($data->data->url)){
                return redirect($data->data->url);
            }else{
                return redirect()->back()->with('error',$data->message);
            }
        }else{
            return redirect()->back()->with('error',$data->message);
        }

    }
    public function auth_basic()
    {
        $auth = base64_encode(env('APP_ASK_AGENT') . ":" . env('APP_ASK_API_SECRET'));
        return $auth;
    }
}

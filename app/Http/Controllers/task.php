    public function index(){
        Log::info("Run cash_back");

        // $members = Members::get();

        // foreach ($members as $member) {
        //     $last_deposit = Transfer::where('member_id',$member->id)->where('status',2)->whereDate('created_at', Carbon::yesterday())->orderby('created_at','desc')->first();
        //     $member_play = Payout::where('username',$member->username)->whereDate('created_at', Carbon::yesterday())->get();
        //     if($last_deposit && $member_play){
        //         $total_lose = 0;
        //         $cash_back=0;
        //         foreach($member_play as $t){
        //             $total_lose += $t->winlose;
        //         }

        //        if(abs($total_lose) > $last_deposit->amount) {
        //         $cash_back = (float) ($last_deposit->amount * 0.05);


        //             Transfer::create([
        //                 'member_id' => $member->id,
        //                 'amount' => $cash_back,
        //                 'status' => 1,
        //                 'status_code' => 'รออนุมัติ',
        //                 'type' => 'cashback',
        //                 'promotion' => 'cashback',
        //                 'old_balance' => (float) $member->wallet_balance,
        //                 'transfer_date' => strtotime(now()),
        //             ]);
        //             $member->wallet_balance = (float) ($member->wallet_balance + $cash_back);
        //             $member->save();

        //         }
        //     }

        // }

    }

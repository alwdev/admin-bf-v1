<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bank;
use App\Models\Bank_forward_balance;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //update True wallet
        $truewallet = Bank::where('active',1)->where('bank_name','TrueMoney Wallet')->first();
        $truewallet->balance = app(\App\Http\Controllers\TMN_Controller::class)->index();
        $truewallet->save();

        $banks = Bank::where('active',1)->get();
        return view('bank.index',compact('banks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('bank.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'bank_name' => ['required'],
            'account_name' => ['required'],
            'account_no' => ['required', 'unique:'.Bank::class],
        ]);

        $bank = new Bank;
        $bank->bank_name = $request->bank_name;
        $bank->account_name = $request->account_name;
        $bank->account_no = $request->account_no;
        $bank->logo = $request->bank_logo;
        // $bank->balance = $request->balance;
        if(isset($request->enable)){
            $bank->enable = $request->enable;
        }else{
            $bank->enable = 0;
        }

        if($request->qr_code){
            $fileName = rand().'.'.$request->qr_code->extension();
            $request->qr_code->move(public_path('images/bank'), $fileName);
            $bank->qr_code = "/images/bank/".$fileName;
        }

        $bank->active = 1;
        $bank->user_id = auth()->user()->id;
        $bank->save();

        return redirect()->route('bankaccount.index')->with('banksave','200');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $bank = Bank::find($id);
        return view('bank.edit',compact('bank'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $bank = Bank::find($id);
        $bank->bank_name = $request->bank_name;
        $bank->account_name = $request->account_name;
        $bank->account_no = $request->account_no;
        $bank->logo = $request->bank_logo;
        // $bank->balance = $request->balance;
        if(isset($request->enable)){
            $bank->enable = $request->enable;
        }else{
            $bank->enable = 0;
        }
        if($request->qr_code){
            $fileName = rand().'.'.$request->qr_code->extension();
            $request->qr_code->move(public_path('images/bank'), $fileName);
            $bank->qr_code = "/images/bank/".$fileName;
        }
        $bank->active = 1;
        $bank->user_id = auth()->user()->id;
        $bank->save();

        return redirect()->route('bankaccount.index')->with('banksave','200');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        //
        $b = Bank::find($request->id);
        $b->active = 0;
        $b->user_id = auth()->user()->id;
        $b->save();
        return redirect()->route('bankaccount.index')->with('banksave','200');
    }

    public function bank_forward_balance_create(Request $request){

        $bank = Bank::find($request->bank_to);

        $data = new Bank_forward_balance;
        $data->bank_no = $bank->account_no;
        $data->bank_name = $bank->bank_name;
        $data->bank_account = $bank->account_name;
        $data->type = $request->type;
        $data->note = $request->note;
        $data->amount = $request->amount;
        $data->created_by = auth()->user()->id;
        $data->save();


        if($request->type == 'เงินเข้า'){
            $bank->balance = (float) $bank->balance + (float) $request->amount;
        }else{
            if((float) $request->amount > (float) $bank->balance){
                $bank->balance = 0;
            }else{
                $bank->balance = (float) $bank->balance - (float) $request->amount;
            }

        }
        $bank->save();

        return redirect()->route('bankaccount.index')->with('banksave','200');
    }
}

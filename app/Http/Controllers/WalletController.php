<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\User;
use App\Rules\isUserLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function deposit(Request $request){
        $validator = Validator::make($request->all(), [
            'document' => ['required', 'numeric', new isUserLogin],
            'phone' => ['required', 'numeric', new isUserLogin],
            'amount' => ['required', 'numeric'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error'=>$validator->errors(),
                "success" => false
            ], 422);
        }

        $id = Auth::user()->id;
        $user = User::find($id);

        try {
            $user->amount = $user->amount + $request->amount;
            if($user->save()){
                return response()->json([
                    'mensaje'=>'Se ha actualizado el saldo en la wallet',
                    "amount"=> $user->amount,
                    "success" => true
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'error'=>'Error al actualizar el saldo en la wallet',
                "success" => false
            ], 500);
        }

    }
    
    public function pay(Request $request){

    }
}



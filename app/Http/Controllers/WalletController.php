<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\User;
use App\Mail\OrderShipped;
use App\Rules\isUserLogin;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Rules\validateAmount;
use App\Models\PaymentHistory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class WalletController extends Controller
{

    public function Assets(Request $request){
        $validator = Validator::make($request->all(), [
            'document' => ['required', 'numeric', new isUserLogin],
            'phone' => ['required', 'numeric', new isUserLogin],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error'=>$validator->errors(),
                "success" => false
            ], 422);
        }

        $id = Auth::user()->id;
        $user = User::find($id);

        return response()->json([
            "amount"=> $user->amount,
            "success" => true
        ], 200);
    }

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
        $validator = Validator::make($request->all(), [
            'amount' => ['required', 'numeric', new validateAmount],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error'=>$validator->errors(),
                "success" => false
            ], 422);
        }

        try {
            $id = Auth::user()->id;
            $user = User::find($id);

            $paymentHistory = new PaymentHistory;
            $paymentHistory->user_id = Auth::user()->id;
            $paymentHistory->token = Auth::user()->token()['id'];
            $paymentHistory->email_token = Str::random(6);
            $paymentHistory->amount = $request->get('amount');
            if($paymentHistory->save()){
                $email = [
                    'title' => "Confirmacion de Pago",
                    'body' => $paymentHistory->email_token
                ];
                Mail::to($user->email)->send(new OrderShipped($email));
                return response()->json([
                    'mensaje'=>'Se ha creado la orden de pago, se envio un email para confirmar',
                    'id_session' => Auth::user()->token()['id'],
                    "amount"=> $paymentHistory->amount,
                    "success" => true
                ], 200);
            }
        } catch (\Throwable $th) {
            Log::debug($th->getMessage());
            return response()->json([
                'error'=>'Error al pagar el producto',
                "success" => false
            ], 500);
        }


    }

    public function confirmPay(Request $request){
        $validator = Validator::make($request->all(), [
            'id_session' => ['required'],
            'token_email' => ['required']
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error'=>$validator->errors(),
                "success" => false
            ], 422);
        }



        try {
            $id = Auth::user()->id;

            //Se busca el registro en el historial de pago que coincida con los codigos
            $paymentHistory = PaymentHistory::where([
                'token'=> $request->get('id_session'),
                'email_token'=> $request->get('token_email')
            ])->first();

            // Se valida de que exista algun registro
            if($paymentHistory == null){
                return response()->json([
                    'error'=> "Los codigos no concuerdan con ninguna compra",
                    "success" => false
                ], 422);
            }

            //Se valida que la compra pertenesca al usuario loggeado
            if($paymentHistory->user_id != $id){
                return response()->json([
                    'error'=> "La compra no le pertenece al usuario",
                    "success" => false
                ], 422);
            }

            //Se valida que el usuario cuente con los fondos suficientes para realizar la compra
            $user = User::find($id);
            if($user->amount < $paymentHistory->amount){
                return response()->json([
                    'error'=> "No tiene los fondos necesarios para esta compra. Porfavor Recargue",
                    "success" => false
                ], 422);
            }

            //Se descuenta el dinero y se actualiza el estatus del pago
            $user->amount = $user->amount - $paymentHistory->amount;
            $paymentHistory->pay = 1;

            if ($user->save() and $paymentHistory->save()){
                return response()->json([
                    'mensaje'=>'Se ha confirmado la compra y se ha descontado el dinero de su cuenta',
                    "success" => true
                ], 200);
            }

        } catch (\Throwable $th) {
            Log::debug($th->getMessage());
            return response()->json([
                'error'=>'Error al confirmar la compra',
                "success" => false
            ], 500);
        }


    }
}



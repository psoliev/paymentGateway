<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;

class PaymentApiController extends Controller
{
    public function index()
    {
        $payments = Payment::all();
        return PaymentResource::collection($payments);
    }

    public function store(Request $request)
    {
        $params = $request->all();
        ksort($params);

        if($request->header('content-type')=='application/json') {
            if($merchant = User::find($params['merchant_id'])){
                $sign = $params['sign'];
                unset($params['sign']);
                $dataStr = hash('sha256', $this->signatureRules($params, ':', $merchant->key));
                if($sign === $dataStr){
                    $sumOfAmountInDay = Payment::where(['merchant_id'=>$merchant->id])
                        ->whereBetween('created_at', [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')])
                        ->sum('amount');
                    if($sumOfAmountInDay<$merchant->amount_limit) {
                        $payment = new Payment();
                        $payment->merchant_id = $params['merchant_id'];
                        $payment->payment_id = $params['payment_id'];
                        $payment->status = $params['status'];
                        $payment->amount = $params['amount'];
                        $payment->amount_paid = $params['amount_paid'];
                        $payment->timestamp = $params['timestamp'];
                        $payment->sign = $sign;
                        if ($payment->save()) {
                            return true;
                        }
                    }
                }
            }
        }elseif(strstr($request->header('content-type'),'multipart/form-data')){
            if($merchant = User::find($params['project'])){
                $sign = $request->header('authorization');
                $dataStr = md5($this->signatureRules($params, '.', $merchant->key));
                if($sign === $dataStr){
                    $sumOfAmountInDay = Payment::where(['merchant_id'=>$merchant->id])
                        ->whereBetween('created_at', [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')])
                        ->sum('amount');
                    if($sumOfAmountInDay<$merchant->amount_limit){
                        $payment = new Payment();
                        $payment->merchant_id = $params['project'];
                        $payment->payment_id = $params['invoice'];
                        $payment->status = $params['status'];
                        $payment->amount = $params['amount'];
                        $payment->amount_paid = $params['amount_paid'];
                        $payment->rand = $params['rand'];
                        $payment->sign = $sign;
                        if($payment->save()){
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    public function signatureRules($params, $separator, $key){
        $dataStr = '';
        foreach ($params as $param){
            $dataStr .= $param.$separator;
        }
        $dataStr = trim($dataStr, $separator);
        $dataStr .= $key;
        return $dataStr;
    }
}

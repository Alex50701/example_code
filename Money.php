<?php


namespace app\components;


use app\models\Debit;
use app\models\Payment;

class Money
{
    public static function getBalanc(int $user_id): float
    {
        $payment_total = Payment::getAllPayment($user_id);
        $debit_total = Debit::getAllDebit($user_id);
        return $payment_total - $debit_total;
    }

    public static function addPayment(array $data): bool
    {
        $payment =new Payment();
        $payment->user_id = $data['user_id'];
        $payment->status = 0;
        $payment->sum = $data['sum'];
        return $payment->save();
    }

    public static function updateStatusPayment(int $id): bool
    {
        $payment = Payment::findOne($id);
        if (!$payment){
            return false;
        }
        $payment->status = 1;
        return $payment->save();
    }

    public static function addDebit(array $data): bool
    {
        $debit =new Debit();
        $debit->user_id = $data['user_id'];
        $debit->sum = $data['sum'];
        return $debit->save();
    }
}
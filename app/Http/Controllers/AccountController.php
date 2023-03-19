<?php

namespace App\Http\Controllers;

use App\Models\Account;

class AccountController extends Controller
{
    public function sacar($number, $value)
    {
        $account = Account::where('number', $number)->first();

        if (!$account) {
            return response()->json(['error' => 'Conta não encontrada'], 404);
        }

        if ($value > $account->balance) {
            return response()->json(['error' => 'Saldo insuficiente'], 400);
        }

        $account->balance -= $value;
        $account->save();

        return response()->json([
            'conta' => $account->number,
            'saldo' => $account->balance
        ]);
    }

    public function depositar($number, $value)
    {
        $account = Account::where('number', $number)->first();

        if (!$account) {
            return response()->json(['error' => 'Conta não encontrada'], 404);
        }

        $account->balance += $value;
        $account->save();

        return response()->json([
            'conta' => $account->number,
            'saldo' => $account->balance]
        );
    }

    public function saldo($number)
    {
        $account = Account::where('number', $number)->first();

        if (!$account) {
            return response()->json(['error' => 'Conta não encontrada'], 404);
        }

        return response()->json([
            'conta' => $account->number,
            'saldo' => $account->balance
        ]);
    }

    public function listar_contas()
    {
        $accounts = Account::all();

        return response()->json($accounts);
    }
}

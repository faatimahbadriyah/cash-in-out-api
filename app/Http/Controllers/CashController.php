<?php

namespace App\Http\Controllers;

use App\Http\Resources\CashResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CashController extends Controller
{
    public function index()
    {
        $debit = Auth::user()->cashes()
            ->whereBetween('when', [now()->firstOfMonth(), now()])
            ->where('amount', '>=', 0)
            ->get('amount')
            ->sum('amount');

        $credit = Auth::user()->cashes()
            ->whereBetween('when', [now()->firstOfMonth(), now()])
            ->where('amount', '<', 0)
            ->get('amount')
            ->sum('amount');

        $balances = Auth::user()->cashes()->get('amount')->sum('amount');

        $transactions = Auth::user()->cashes()
            ->whereBetween('when', [now()->firstOfMonth(), now()])
            ->latest()
            ->get();

        return response()->json([
            'debit' => formatPrice($debit),
            'credit' => formatPrice($credit),
            'balances' => formatPrice($balances),
            'transactions' => CashResource::collection($transactions),
        ]);
    }

    public function store()
    {
        request()->validate([
            'name' => 'required',
            'amount' => 'required|numeric',
        ]);

        $slug = request('name') . "-" . Str::random(6);
        $when = request('when') ?? now();
        Auth::user()->cashes()->create([
            'name' => request('name'),
            'slug' => Str::slug($slug),
            'when' => $when,
            'amount' => request('amount'),
            'description' => request('description'),
        ]);

        return response()->json([
            'message' => 'The transaction has been saved.',
        ]);
    }
}

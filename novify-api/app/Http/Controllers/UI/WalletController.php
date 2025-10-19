<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    /**
     * Display wallet overview
     */
    public function index()
    {
        $merchant = Auth::user();
        $wallet = $merchant->wallets()->first();
        
        if (!$wallet) {
            // Create wallet if it doesn't exist
            $wallet = Wallet::create([
                'merchant_id' => $merchant->id,
                'balance' => 0,
                'currency' => 'USD',
                'is_active' => true,
            ]);
        }
        
        $recentTransactions = $wallet->transactions()
            ->latest()
            ->take(10)
            ->get();
        
        return view('templates.wallet.index', compact('wallet', 'recentTransactions'));
    }

    /**
     * Show wallet details
     */
    public function show(Wallet $wallet)
    {
        // Ensure the wallet belongs to the authenticated merchant
        if ($wallet->merchant_id !== Auth::id()) {
            abort(403);
        }

        $transactions = $wallet->transactions()
            ->latest()
            ->paginate(20);
        
        return view('templates.wallet.show', compact('wallet', 'transactions'));
    }

    /**
     * Show topup form
     */
    public function topup(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string',
        ]);

        $merchant = Auth::user();
        $wallet = $merchant->wallets()->first();
        
        // Create topup transaction
        $transaction = WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'type' => 'topup',
            'amount' => $request->amount,
            'description' => 'Wallet topup via ' . $request->payment_method,
            'status' => 'pending',
        ]);

        // Update wallet balance
        $wallet->increment('balance', $request->amount);

        return redirect()->route('ui.wallet.index')
            ->with('success', 'Wallet topped up successfully');
    }

    /**
     * Process wallet transfer
     */
    public function transfer(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'recipient_id' => 'required|exists:merchants,id',
            'description' => 'nullable|string',
        ]);

        $merchant = Auth::user();
        $wallet = $merchant->wallets()->first();
        
        if ($wallet->balance < $request->amount) {
            return back()->with('error', 'Insufficient balance');
        }

        // Create transfer transaction
        $transaction = WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'type' => 'transfer',
            'amount' => -$request->amount,
            'description' => $request->description ?? 'Transfer to merchant',
            'status' => 'completed',
            'recipient_id' => $request->recipient_id,
        ]);

        // Update wallet balance
        $wallet->decrement('balance', $request->amount);

        return redirect()->route('ui.wallet.index')
            ->with('success', 'Transfer completed successfully');
    }

    /**
     * Show wallet transactions
     */
    public function transactions()
    {
        $merchant = Auth::user();
        $wallet = $merchant->wallets()->first();
        
        if (!$wallet) {
            return redirect()->route('ui.wallet.index');
        }

        $transactions = $wallet->transactions()
            ->latest()
            ->paginate(20);
        
        return view('templates.wallet.transactions', compact('transactions'));
    }
}

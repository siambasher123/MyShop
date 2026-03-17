<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction1;

class AdminTransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction1::orderBy('created_at', 'desc')->get();
        return view('admin.transactions', compact('transactions'));
    }

    public function updatePaymentNote(Request $request, $id)
    {
        $tx = Transaction1::findOrFail($id);
        $tx->payment_note = $request->input('note');
        $tx->save();

        return response()->json(['success' => true]);
    }

    public function updateStatus(Request $request, $id)
    {
        $tx = Transaction1::findOrFail($id);
        $tx->status = $request->input('status');
        $tx->save();

        return response()->json(['success' => true]);
    }
}

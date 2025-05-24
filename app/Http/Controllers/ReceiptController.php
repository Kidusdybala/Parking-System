<?php
namespace App\Http\Controllers;

use App\Models\Receipt;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
    public function generateReceipt($transaction_id)
    {
        $receipt = Receipt::where('transaction_id', $transaction_id)->with('user')->firstOrFail();
        $pdf = Pdf::loadView('receipt.template', compact('receipt'));

        return $pdf->download('receipt_' . $transaction_id . '.pdf');
    }
    public function uploadReceipt(Request $request)
{
    // Handle file upload logic
    $request->validate([
        'receipt_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $imagePath = $request->file('receipt_image')->store('receipts', 'public');



    return redirect()->back()->with('success', 'Receipt submitted successfully!');
}
}

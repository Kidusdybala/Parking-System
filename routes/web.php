    <?php

    use Illuminate\Support\Facades\Route;
    use Illuminate\Http\Request;
    use App\Http\Controllers\ReceiptController;
    use App\Http\Controllers\ChatController;
    use App\Http\Controllers\ChapaWebController;

    // Chapa payment routes - MUST BE BEFORE CATCH-ALL ROUTE
    Route::get('/payment-success', [ChapaWebController::class, 'showPaymentReceipt'])->name('chapa.success');
    Route::get('/payment-callback', [ChapaWebController::class, 'handleCallback'])->name('chapa.callback');
    Route::get('/payment-complete', function() {
        return view('payment-complete');
    })->name('payment.complete');
    
    Route::get('/after-payment', function() {
        return view('after-payment');
    })->name('after.payment');
    
    // Legacy wallet payment success route (for old return URLs)
    Route::get('/wallet', function(Request $request) {
        if ($request->get('payment') === 'success') {
            return redirect()->route('chapa.success', ['type' => 'wallet']);
        }
        return redirect('/');
    });

    // File upload routes (API functionality)
    Route::post('/upload-receipt', [ReceiptController::class, 'upload']);
    Route::get('/receipt/{transaction_id}', [ReceiptController::class, 'generateReceipt'])->name('receipt.download');

    // Chat route (API functionality)
    Route::post('/chat', [ChatController::class, 'handleChat'])->middleware('auth:api');

    // React SPA - Catch all routes and serve React app (MUST BE LAST)
    Route::get('/{any}', function () {
        return view('app');
    })->where('any', '.*');














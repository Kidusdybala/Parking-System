    <?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\ReceiptController;
    use App\Http\Controllers\ChatController;

    // File upload routes (API functionality)
    Route::post('/upload-receipt', [ReceiptController::class, 'upload']);
    Route::get('/receipt/{transaction_id}', [ReceiptController::class, 'generateReceipt'])->name('receipt.download');

    // Chat route (API functionality)
    Route::post('/chat', [ChatController::class, 'handleChat'])->middleware('auth:api');

    // React SPA - Catch all routes and serve React app
    Route::get('/{any}', function () {
        return view('app');
    })->where('any', '.*');














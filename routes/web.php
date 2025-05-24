    <?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\PaymentController;
    use App\Http\Controllers\Record\RecordMainController;
    use App\Http\Controllers\Client\ProfileController;
    use App\Http\Controllers\Client\ParkingController;
    use App\Http\Controllers\VerificationController;
    use App\Http\Controllers\Auth\RegisteredUserController;
    use Illuminate\Foundation\Auth\EmailVerificationRequest;
    use Illuminate\Http\Request;
    use App\Http\Controllers\ParkingRecommendationController;
    use App\Http\Controllers\Auth\AuthenticatedSessionController;
    use App\Http\Controllers\Client\ClientMainController;
    use App\Http\Controllers\WalletController;
    use App\Http\Controllers\ReceiptController;
    use App\Http\Controllers\MLController;
    use App\Http\Controllers\PredictController;
    use App\Http\Controllers\ChatController;
    Route::get('/', function () {
        return view('welcome');
    });

    // Admin (Role: 0)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['auth', 'verified', 'rolemanager:admin','prevent-back-history'])->name('dashboard');
    // Department (Role: 2)
    Route::get('/department/dashboard', function () {
        return view('department');
    })->middleware(['auth', 'verified', 'rolemanager:department','prevent-back-history'])->name('department');

    // Client (Role:1)
    Route::middleware(['auth', 'verified', 'rolemanager:client', 'prevent-back-history'])->group(function () {
    Route::prefix('client')->group(function () {
        Route::controller(ClientMainController::class)->group(function () {
            Route::get('/dashboard', 'index')->name('client');
            Route::get('/map/manage', 'manage_map')->name('client.map.manage');
            Route::get('/home', function () {
                return view('client.home'); // or use a controller
            })->name('client.home.manage');
            Route::get('/history/manage', 'manage_history')->name('client.history.manage');
            Route::get('/parking/manage', 'manage_parking')->name('client.parking.manage');
            Route::get('/profile/manage', 'manage_profile')->name('client.profile.manage');

        Route::get('/reservations', [ClientMainController::class, 'reservations'])->name('client.reservations');
        Route::get('/settings', [ClientMainController::class, 'setting'])->name('client.settings');
        Route::get('/payment-methods', [ClientMainController::class, 'payment_methods'])->name('client.payment.methods');
    });
        });
    });

    Route::middleware(['auth', 'verified', 'rolemanager:record', 'prevent-back-history'])->group(function () {
        Route::prefix('record')->group(function () {
            Route::controller(RecordMainController::class)->group(function () {
                Route::get('/dashboard', 'index')->name('record');
                Route::get('/user/manage', 'manage_user')->name('admin.user.manage');
                Route::get('/user/create', 'create_user')->name('admin.user.create');
                Route::post('/user/create', 'create_user')->name('admin.user.create');
                Route::post('/store/user', 'store_user')->name('admin.user.store');
                Route::get('/user/edit', 'edit_user')->name('admin.user.edit');
                Route::get('/user/{id}', 'showuser')->name('show.user');
                Route::put('/user/update{id}', 'updateuser')->name('update.user');
                Route::delete('/user/delete/{id}', 'deleteuser')->name('delete.user');
                Route::get('/history/manage', 'manage_history')->name('admin.history.manage');
                Route::get('/payment/manage', 'manage_payment')->name('admin.payment.manage');
                Route::get('/profile/manage', 'manage_profile')->name('admin.profile.manage');
            });
        });
    });

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    Route::middleware(['auth'])->group(function () {
        Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
        Route::post('/wallet/add', [WalletController::class, 'addBalance'])->name('wallet.add');
        Route::post('/wallet/pay', [WalletController::class, 'makePayment'])->name('wallet.pay');
    });

    Route::get('/receipt', function () {
        if (!session()->has('receiptData')) {
            return redirect('/')->with('error', 'No receipt available.');
        }
        return view('client.profile.receipt', ['receiptData' => session('receiptData')]);
    })->name('receipt');

    Route::get('/receipt/{transaction_id}', [ReceiptController::class, 'generateReceipt'])->name('receipt.download');

    Route::get('/verify-code', [VerificationController::class, 'show'])->name('verification.code');
    Route::post('/verify-code', [VerificationController::class, 'verify'])->name('custom.verification.verify');

    Route::middleware(['auth'])->group(function () {
        Route::get('/parking', [ParkingController::class, 'index'])->name('parking.index');
        Route::get('/client/parking/manage', [ParkingController::class, 'manage'])->name('client.parking.manage');
        Route::post('/parking/cancel/{reservationId}', [ParkingController::class, 'cancel'])->name('parking.cancel');
        Route::post('/parking/finish/{reservationId}', [ParkingController::class, 'finish'])->name('parking.finish');
        Route::post('/parking/finish/{reservationId}', [ParkingController::class, 'finishParking'])->name('parking.finish');
        Route::get('/parking/receipt/{reservationId}', [ParkingController::class, 'showReceipt'])->name('parking.receipt');
        Route::post('/parking/reserve/{spotId}', [ParkingController::class, 'reserve'])->name('parking.reserve');
        Route::post('/parking/park/{reservationId}', [ParkingController::class, 'park'])->name('parking.park');
        Route::post('/parking/leave/{reservationId}', [ParkingController::class, 'leave'])->name('parking.leave');
        Route::post('/parking/pay/{reservationId}', [ParkingController::class, 'pay'])->name('parking.pay');
        Route::get('/parking/pay/{reservationId}', [ParkingController::class, 'showPaymentPage'])->name('parking.pay');
        // routes/web.php
        Route::post('/chat', [ChatController::class, 'handleChat'])->middleware('auth');
         Route::delete('/parking/cancel/{reservationId}', [ParkingController::class, 'cancel'])->name('parking.cancel');

    });
    Route::post('/upload-receipt', [ReceiptController::class, 'upload']);
    Route::get('/upload-receipt', function () {
        return view('upload');
    });

    Route::middleware(['auth', 'rolemanager:client','prevent-back-history'])->group(function () {
        Route::get('/profile/topup', [PaymentController::class, 'showTopupPage'])->name('client.profile.topup');
        Route::get('/profile/payment-upload', [PaymentController::class, 'uploadPaymentProofPage'])->name('client.profile.payment_upload');
        Route::post('/profile/payment-store', [PaymentController::class, 'storePaymentProof'])->name('payment.store');
        Route::delete('/payment/delete/{id}', [PaymentController::class, 'deletePayment'])->name('admin.payments.delete');
        Route::middleware(['auth'])->group(function () {
        Route::get('/client/profile/edit', [ProfileController::class, 'edit'])->name('client.profile.edit');

        Route::post('/client/profile/update-password', [App\Http\Controllers\Client\ProfileController::class, 'updatePassword'])->name('client.profile.update-password');
    });
    // <-- ADD THIS LINE

        Route::post('/parking/pay/{reservationId}', [ParkingController::class, 'showPaymentPage'])->name('parking.pay');
    Route::post('/parking/confirm-payment/{reservationId}', [ParkingController::class, 'processPayment'])->name('parking.confirmPayment');
    Route::get('/history', [ParkingController::class, 'history'])->name('client.history.manage');
    Route::get('/parking/receipt/{reservationId}', [ParkingController::class, 'showReceipt'])->name('parking.receipt');
    });

    // Admin Routes
    Route::middleware(['auth', 'rolemanager:record'])->prefix('admin')->group(function () {
        Route::get('payment/manage', [PaymentController::class, 'viewPayments'])->name('admin.payment.manage');
        Route::patch('/payments/approve/{id}', [PaymentController::class, 'approvePayment'])->name('admin.payments.approve');
        Route::patch('/payments/reject/{id}', [PaymentController::class, 'rejectPayment'])->name('admin.payments.reject');
        Route::delete('/admin/payments/{id}/delete', [PaymentController::class, 'deletePayment'])->name('admin.payments.delete');
    });

    // Add this simple test route
    Route::get('/test-chat', function() {
        return view('test-chat');
    });

    // Add this new route to clear reservations
    Route::middleware(['auth'])->group(function () {
        Route::get('/parking/clear-reservations', [App\Http\Controllers\Client\ParkingController::class, 'clearReservations'])->name('parking.clearReservations');
    });

    require __DIR__.'/auth.php';














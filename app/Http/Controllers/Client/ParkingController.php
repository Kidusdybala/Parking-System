<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ParkingSpot;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;



    class ParkingController extends Controller
    {
        public function index()
    {
        $spots = ParkingSpot::paginate(20); // Show 20 spots per page
        return view('client.parking.manage', compact('spots'));
    }

        public function showReceipt($reservationId)
        {
            $reservation = Reservation::findOrFail($reservationId);
            return view('client.parking.receipt', compact('reservation'));
        }

       public function reserve(Request $request, $spotId)
{
    $userId = Auth::id();

    if (!$userId) {
        return redirect()->route('login')->with('error', 'Please login to reserve a spot.');
    }

    // Check if user already has an active reservation
    $existingReservation = Reservation::where('user_id', $userId)
        ->whereNull('left_at') // Means the user hasn't left yet
        ->first();

    if ($existingReservation) {
        return back();
    }

    $spot = ParkingSpot::findOrFail($spotId);

    if ($spot->is_reserved) {
        return back();
    }

    // Create reservation
    $reservation = Reservation::create([
        'user_id' => $userId,
        'parking_spot_id' => $spot->id,
        'reserved_at' => now(),
    ]);

    $spot->update(['is_reserved' => true]);

    return back();
}

        public function park($reservationId)
        {
            $reservation = Reservation::findOrFail($reservationId);

            // Check if reservation is expired
            if ($reservation->isExpired()) {
                $reservation->delete();
                return back();
            }

            $reservation->update(['parked_at' => now()]);

            return back();
        }

        public function leave($reservationId)
        {
            $reservation = Reservation::findOrFail($reservationId);
            $spot = ParkingSpot::find($reservation->parking_spot_id);

            if (!$reservation->parked_at) {
                return back();
            }

            $reservation->update([
                'left_at' => now(),
                'total_price' => $reservation->calculateTotalPrice(),
                'is_paid' => false,
            ]);

            return back();
        }

        public function pay($reservationId)
        {
            $reservation = Reservation::findOrFail($reservationId);

            if ($reservation->is_paid) {
                return back();
            }

            $reservation->update(['is_paid' => true]);

            // Free the spot
            $reservation->parkingSpot->update(['is_reserved' => false]);

            return back()->with('success', 'Payment complete. Show receipt at exit.');
        }
        // Make sure to import your model

        public function manage()
        {
            $spots = ParkingSpot::with('reservation')->get(); // Ensure reservations are loaded

            return view('client.parking.manage', compact('spots'));
        }

        public function cancel($reservationId)
    {
        $reservation = Reservation::findOrFail($reservationId);

        if (Auth::id() !== $reservation->user_id) {
            return back();
        }

        // Free the parking spot
        $reservation->parkingSpot->update(['is_reserved' => false]);

        // Delete reservation
        $reservation->delete();

        return back();
    }
    public function finishParking($reservationId)
    {
        $reservation = Reservation::findOrFail($reservationId);

        if (!$reservation->parked_at) {
            return back()->with('error', 'You must park before finishing.');
        }

        // Set the leaving time
        $reservation->update([
            'left_at' => now(),
            'total_price' => $reservation->calculateTotalPrice(),
            'is_paid' => false,
        ]);

        // Redirect to the payment confirmation page
        return redirect()->route('parking.pay', ['reservationId' => $reservation->id]);
    }
    public function showPaymentPage($reservationId)
    {
        $reservation = Reservation::findOrFail($reservationId);
        $user = Auth::user();

        return view('client.parking.payment_confirmation', compact('reservation', 'user'));
    }
    public function processPayment(Request $request, $reservationId)
    {
        $reservation = Reservation::findOrFail($reservationId);
        $user = Auth::user();

        // Check if user has enough balance
        if ($user->balance < $reservation->total_price) {
            return redirect()->route('client.profile.topup')->with('error', 'Insufficient balance. Please top up first.');
        }

        // Deduct amount from user's balance
        $user->update([
            'balance' => $user->balance - $reservation->total_price
        ]);

        // Mark reservation as paid
        $reservation->update([
            'is_paid' => true,
        ]);

        // Free the parking spot
        $reservation->parkingSpot->update([
            'is_reserved' => false
        ]);

        // Redirect to the receipt page
        return redirect()->route('parking.receipt', ['reservationId' => $reservation->id])
                        ->with('success', 'Payment successful! Your parking spot is now free.');
    }
    public function getRecommendedSpot($userId)
    {
        $data = [
            'user_id' => $userId,
            'time_spent' => 30 // Adjust as needed
        ];

        $ch = curl_init('http://127.0.0.1:5000/predict');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        return response()->json([
            'recommended_parking_spot' => $result['recommended_spot_id'] ?? null
        ]);
    }

    public function history()
    {
        $user = Auth::user();
        // Fetch all completed payments (where is_paid = true)
        $history = Reservation::where('user_id', $user->id)
                            ->where('is_paid', true)
                            ->orderBy('left_at', 'desc')
                            ->get();

        return view('client.history.manage', compact('history'));
    }
    }


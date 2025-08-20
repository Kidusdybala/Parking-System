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
        ->where('status', 'active')
        ->whereNull('left_at') // Means the user hasn't left yet
        ->first();

    if ($existingReservation) {
        $currentPage = $request->get('page', 1);
        return redirect()->route('client.parking.manage', ['page' => $currentPage])
                        ->with('error', 'You already have an active reservation.');
    }

    $spot = ParkingSpot::findOrFail($spotId);

    if ($spot->is_reserved) {
        $currentPage = $request->get('page', 1);
        return redirect()->route('client.parking.manage', ['page' => $currentPage])
                        ->with('error', 'This parking spot is already reserved.');
    }

    // Create reservation
    $reservation = Reservation::create([
        'user_id' => $userId,
        'parking_spot_id' => $spot->id,
        'reserved_at' => now(),
        'status' => 'active',
    ]);

    $spot->update(['is_reserved' => true]);

    // Preserve pagination and scroll position by redirecting to the same page with anchor
    $currentPage = $request->get('page', 1);
    $spotId = $request->get('spot_id', $spot->id);
    return redirect()->route('client.parking.manage', ['page' => $currentPage])
                    ->with('success', 'Parking spot reserved successfully!')
                    ->with('scroll_to_spot', $spotId);
}

        public function park(Request $request, $reservationId)
        {
            $reservation = Reservation::findOrFail($reservationId);

            // Check if reservation is expired
            if ($reservation->isExpired()) {
                $reservation->delete();
                $currentPage = $request->get('page', 1);
                return redirect()->route('client.parking.manage', ['page' => $currentPage])
                                ->with('error', 'Reservation has expired.');
            }

            $reservation->update(['parked_at' => now()]);

            $currentPage = $request->get('page', 1);
            $spotId = $request->get('spot_id', $reservation->parking_spot_id);
            return redirect()->route('client.parking.manage', ['page' => $currentPage])
                            ->with('success', 'You have successfully parked!')
                            ->with('scroll_to_spot', $spotId);
        }

        public function leave($reservationId)
        {
            $reservation = Reservation::findOrFail($reservationId);
            $spot = ParkingSpot::find($reservation->parking_spot_id);

            if (!$reservation->parked_at) {
                return back()->with('error', 'You must park first before leaving.');
            }

            $reservation->update([
                'left_at' => now(),
                'total_price' => $reservation->calculateTotalPrice(),
                'is_paid' => false,
                'status' => 'free',
            ]);

            return back()->with('success', 'You have left the parking spot. Please proceed to payment.');
        }

        public function pay($reservationId)
        {
            $reservation = Reservation::findOrFail($reservationId);

            if ($reservation->is_paid) {
                return back();
            }

            $reservation->update([
                'is_paid' => true,
                'status' => 'free'
            ]);

            // Free the spot
            $reservation->parkingSpot->update(['is_reserved' => false]);

            return back()->with('success', 'Payment complete. Show receipt at exit.');
        }
        // Make sure to import your model

        public function manage(Request $request)
        {
            $perPage = 20; // Show 20 spots per page
            $spots = ParkingSpot::with('currentReservation')
                               ->paginate($perPage);

            return view('client.parking.manage', compact('spots'));
        }

        public function cancel(Request $request, $reservationId)
    {
        $reservation = Reservation::findOrFail($reservationId);

        if (Auth::id() !== $reservation->user_id) {
            $currentPage = $request->get('page', 1);
            return redirect()->route('client.parking.manage', ['page' => $currentPage])
                            ->with('error', 'Unauthorized action.');
        }

        // Free the parking spot
        $reservation->parkingSpot->update(['is_reserved' => false]);

        // Update reservation status to free (cancelled)
        $reservation->update(['status' => 'free']);

        $currentPage = $request->get('page', 1);
        $spotId = $request->get('spot_id', $reservation->parking_spot_id);
        return redirect()->route('client.parking.manage', ['page' => $currentPage])
                        ->with('success', 'Reservation cancelled successfully.')
                        ->with('scroll_to_spot', $spotId);
    }
    public function finishParking(Request $request, $reservationId)
    {
        $reservation = Reservation::findOrFail($reservationId);

        if (!$reservation->parked_at) {
            $currentPage = $request->get('page', 1);
            return redirect()->route('client.parking.manage', ['page' => $currentPage])
                            ->with('error', 'You must park before finishing.');
        }

        // Set the leaving time
        $reservation->update([
            'left_at' => now(),
            'total_price' => $reservation->calculateTotalPrice(),
            'is_paid' => false,
            'status' => 'free',
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
            'status' => 'free',
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


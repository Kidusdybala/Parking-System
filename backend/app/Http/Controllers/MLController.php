<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
class MLController extends Controller
{
public function exportTrainingData()
{
    $reservations = DB::table('reservations')
        ->select('user_id', 'parking_spot_id')
        ->get();

    Storage::put('ml/training_data.csv', $reservations->implode("\n"));

    return 'Training data exported!';
}
}

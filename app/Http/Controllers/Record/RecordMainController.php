<?php
namespace App\Http\Controllers\Record;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Reservation;
use App\Models\ParkingSpot;

class RecordMainController extends Controller
{
    public function manage_user()
{
    $users = User::all();
    return view('admin.user.manage', compact('users'));
}

    public function index()
    {
        return view('record');
    }

    public function create_user()
    {
        return view('admin.user.create');
    }
    public function store_user(Request $request)
{
    // Validate the request data
    $request->validate([
        'name' => 'required|max:100',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8',
        'role' => 'required|in:0,1,2,3',
    ]);

    // Create a new user
    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'role' => $request->role,
    ]);
    return redirect()->route('admin.user.manage')->with('message', 'User created successfully');
}

    public function edit_user()
    {
        return view('admin.user.edit');
    }
    public function manage_history(Request $request)
    {

        $users = User::all();
        $parkingSpots = ParkingSpot::all();


        $query = Reservation::with('user', 'parkingSpot');


        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('parking_spot_id') && $request->parking_spot_id != '') {
            $query->where('parking_spot_id', $request->parking_spot_id);
        }


        $reservations = $query->orderBy('reserved_at', 'desc')->get();

        return view('admin.history.manage', compact('reservations', 'users', 'parkingSpots'));
    }
    public function manage_payment()
    {
        return view('admin.payment.manage');
    }
    public function manage_profile()
    {
        return view('admin.profile.manage');
    }

    public function showuser($id)
{
    $user = User::findOrFail($id);
    return view('admin.user.edit', compact('user'));
}
public function updateuser(Request $request, $id)
{
    $user = User::findOrFail($id);

    // Validate name and email only
    $request->validate([
        'name' => 'required|max:100',
        'email' => 'required|email|unique:users,email,' . $id,
    ]);

    // Update user
    $user->update([
        'name' => $request->name,
        'email' => $request->email,
    ]);

    return redirect()->route('admin.user.manage')->with('message', 'User updated successfully');
}
public function deleteuser($id)
{
    User::findOrFail($id)->delete();
    return redirect()->route('admin.user.manage')->with('message', 'User deleted successfully');
}

}

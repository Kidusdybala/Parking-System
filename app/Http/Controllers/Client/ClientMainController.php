<?php
namespace App\Http\Controllers\Client;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
class ClientMainController extends Controller
{
    
     public function reservations()
    {
        return view('client.reservations');
    }
     public function index()
    {
        return view('dashboard');
    }
    public function setting()
    {
        return view('client.settings');
    }
    public function payment_methode()
    {
        return view('client.payment.methods');
    }
    public function manage_home()
    {
        return view('client.home.manage');
    }
    public function manage_history()
    {
        return view('client.history.manage');
    }
    public function manage_map()
    {
        return view('client.map.manage');
    }

    public function manage_parking()
    {
        return view('client.parking.manage');
    }
    public function manage_profile()
    {
        return view('client.profile.manage');
    }


}


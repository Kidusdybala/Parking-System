@extends('admin.layouts.layout')

@section('admin_page_title')
   Manage History
@endsection
@section('admin_layout')
    <div class="container">
        <h2>Parking History</h2>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('admin.history.manage') }}" class="mb-3">
            <div class="row">
                <!-- User Filter -->
                <div class="col-md-4">
                    <label for="user_id">Filter by User:</label>
                    <select name="user_id" id="user_id" class="form-control">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Parking Spot Filter -->
                <div class="col-md-4">
                    <label for="parking_spot_id">Filter by Parking Spot:</label>
                    <select name="parking_spot_id" id="parking_spot_id" class="form-control">
                        <option value="">All Parking Spots</option>
                        @foreach($parkingSpots as $spot)
                            <option value="{{ $spot->id }}" {{ request('parking_spot_id') == $spot->id ? 'selected' : '' }}>
                                {{ $spot->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="col-md-4 mt-4">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.history.manage') }}" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>

        <!-- History Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Parking Spot</th>
                    <th>Reserved At</th>
                    <th>Parked At</th>
                    <th>Left At</th>
                    <th>Total Price</th>
                    <th>Paid</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reservations as $reservation)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $reservation->user->name }}</td>
                    <td>{{ $reservation->parkingSpot->name ?? 'N/A' }}</td>
                    <td>{{ $reservation->reserved_at }}</td>
                    <td>{{ $reservation->parked_at }}</td>
                    <td>{{ $reservation->left_at }}</td>
                    <td>${{ number_format($reservation->total_price, 2) }}</td>
                    <td>{{ $reservation->is_paid ? 'Yes' : 'No' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endsection

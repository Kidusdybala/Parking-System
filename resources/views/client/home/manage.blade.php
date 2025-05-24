@extends('client.layouts.layout')

@section('client_page_title')
Parking System
@endsection

@section('client_layout')
    <h3>Welcome</h3>
@endsection

@if($recommendedSpot)
    <div class="alert alert-info">
        Recommended Parking Spot: <strong>Spot #{{ $recommendedSpot }}</strong> based on your reservation history.
    </div>
@else
    <div class="alert alert-warning">
        No recommendation yet — you haven’t reserved any spots.
    </div>
@endif

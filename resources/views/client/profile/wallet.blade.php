@extends('client.layouts.layout')

@section('client_page_title', 'Wallet')

@section('client_layout')
    <div class="container">
        <h3>Wallet</h3>
        <p><strong>Balance:</strong> {{ number_format(Auth::user()->balance, 2) }} ETB</p>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('wallet.add') }}" method="POST">
            @csrf
            <label>Add Money:</label>
            <input type="number" name="amount" class="form-control" min="1" required>
            <button type="submit" class="btn btn-primary mt-2">top up Balance</button>
        </form>

        <hr>

        <form action="{{ route('wallet.pay') }}" method="POST">
            @csrf
            <label>Pay Amount:</label>
            <input type="number" name="amount" class="form-control" min="1" required>
            <button type="submit" class="btn btn-danger mt-2">Make Payment</button>
        </form>
    </div>
@endsection

@extends('admin.layouts.layout')

@section('admin_page_title', 'Manage Payments')

@section('admin_layout')
<div class="container">
    <h2>Pending Payment Proofs</h2>

    <table class="table">
        <thead>
            <tr>
                <th>User</th>
                <th>Proof</th>
                <th>Status</th>
                <th>Amount</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
                <tr>
                    <td>{{ $payment->user->name }}</td>
                    <td><img src="{{ asset('storage/' . $payment->image) }}" width="100"></td>
                    <td>{{ ucfirst($payment->status) }}</td>
                    <td>
                        <form action="{{ route('admin.payments.approve', $payment->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="number" name="amount" class="form-control" required>
                            <button type="submit" class="btn btn-success mt-2">Approve & Update Balance</button>
                        </form>
                        <form action="{{ route('admin.payments.reject', $payment->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-danger mt-2">Reject</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

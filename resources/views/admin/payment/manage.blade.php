@extends('admin.layouts.layout')

@section('admin_page_title', 'Manage Payments')

@section('admin_layout')
<div class="container">
    <h2>Pending Payment Proofs</h2>

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if(empty($payments) || count($payments) == 0)
        <div class="alert alert-info">No pending payment proofs found.</div>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Proof</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                    <tr>
                        <td>
                            User ID: {{ $payment->user_id ?? 'None' }}
                           @if($payment->user)
    <br><span class="text-success">{{ $payment->user->name }}</span>
@else
    <br><span class="text-danger">User not found</span>
@endif

                        </td>

                        {{-- Proof --}}
                        <td>
                            @if($payment->image)
                                <a href="{{ asset('storage/' . $payment->image) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $payment->image) }}" width="100" alt="Receipt Image">
                                </a>
                            @else
                                No image
                            @endif
                        </td>

                        {{-- Status --}}
                        <td>{{ ucfirst($payment->status ?? 'unknown') }}</td>

                        {{-- Actions --}}
                        <td>
                            @if(isset($payment->userExists) && $payment->userExists)
                                <form action="{{ route('admin.payments.approve', $payment->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="amount" class="form-control" required placeholder="ETB">
                                    <button type="submit" class="btn btn-success mt-2">Approve</button>
                                </form>

                                <form action="{{ route('admin.payments.reject', $payment->id) }}" method="POST" class="mt-2">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-danger">Reject</button>
                                </form>
                            @else
                                <div class="alert alert-warning">
                                    Cannot process - User not found
                                    <form action="{{ route('admin.payments.delete', $payment->id) }}" method="POST" class="mt-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection



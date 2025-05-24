@extends('client.layouts.layout')

@section('client_page_title', 'Edit Profile')

@section('client_layout')
    <h3>Edit Profile</h3>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('client.profile.update-password') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="current_password">Current Password:</label>
            <input type="password" name="current_password" class="form-control" required>
        </div>

        <div class="form-group mt-2">
            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" class="form-control" required>
            <small class="form-text text-muted">Minimum 8 characters, must include letters and numbers.</small>
        </div>

        <div class="form-group mt-2">
            <label for="new_password_confirmation">Confirm New Password:</label>
            <input type="password" name="new_password_confirmation" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success mt-3">Save Changes</button>
    </form>
@endsection

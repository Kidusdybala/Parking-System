@extends('admin.layouts.layout')

@section('admin_page_title')
Create User
@endsection

@section('admin_layout')
<h3>Create User</h3>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Create User</h5>
            </div>
            <div class="card-body">

@if ($errors->any())
    <div class="alert alert-warning alert-dismissible fade show">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@if(session('success'))
<div class="alert alert-success">
    {{session('success')}}
</div>
    @endif
    <form action="{{ route('admin.user.create') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="user_name" class="fw-bold mb-2">name of the user</label>
            <input type="text" name="attribute_value" id="attribute_value" class="form-control" placeholder="Enter user name" required>
        </div>
        <button type="submit" class="btn btn-primary">Add User</button>
    </form>

            </div>
        </div>
    </div>
</div>

@endsection

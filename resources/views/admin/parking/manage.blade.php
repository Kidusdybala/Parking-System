@extends('admin.layouts.layout')

@section('admin_page_title')
    Manage Parking Spots
@endsection

@section('admin_layout')
    <div class="container">
        <h2 class="my-4 text-center">Admin - Manage Parking Spots</h2>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price (ETB/Hour)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($spots as $spot)
                    <tr>
                        <td>{{ $spot->id }}</td>
                        <td>{{ $spot->name }}</td>
                        <td>{{ $spot->price_per_hour }}</td>
                        <td>
                            @if($spot->is_reserved)
                                <span class="badge bg-danger">Reserved</span>
                            @else
                                <span class="badge bg-success">Available</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

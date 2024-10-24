@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4 text-center text-primary font-weight-bold">Sites</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row justify-content-center">
        @foreach($sites as $site)
            <div class="col-md-8 mb-4 d-flex justify-content-center">
                <div class="card shadow-lg custom-card">
                    <img src="{{ $site->image_url }}" alt="{{ $site->name }}" class="card-img-top custom-card-img">
                    <div class="card-body text-center">
                        <h5 class="card-title">{{ $site->name }}</h5>
                        <div class="d-flex justify-content-between mt-3">
                            <a href="{{ route('sites.edit', $site->id) }}" class="btn btn-warning btn-lg">Edit</a>
                            <form action="{{ route('sites.destroy', $site->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-lg">Delete</button>
                            </form>
                        </div>
                        <a href="{{ route('sites.show', $site->id) }}" class="btn btn-info btn-block mt-3 btn-lg">View</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@push('styles')
<style>
    body {
        background-color: #f0f4f8; /* Light background color for the page */
    }

    .custom-card {
        width: 100%; /* Make card responsive */
        max-width: 500px; /* Adjust maximum width */
        border: none; /* Remove border */
        border-radius: 1.25rem; /* More rounded corners */
        overflow: hidden; /* Ensure content does not overflow */
        transition: transform 0.3s, box-shadow 0.3s; /* Smooth transition */
        margin: 20px; /* Add margin around the card */
    }

    .custom-card:hover {
        transform: translateY(-5px); /* Slight upward movement on hover */
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25); /* Enhanced shadow effect */
    }

    .custom-card-img {
        height: 300px; /* Set fixed height for images */
        width: 100%; /* Full width of the card */
        object-fit: cover; /* Maintain aspect ratio while filling */
    }

    .card-body {
        padding: 20px; /* Adjust padding for the card body */
        background-color: #ffffff; /* White background color for cards */
    }

    .card-title {
        font-size: 1.8rem; /* Adjust title font size */
        color: #343a40; /* Darker text for contrast */
        margin-bottom: 10px; /* Spacing below title */
    }

    .btn {
        font-weight: bold; /* Make button text bold */
        padding: 12px 15px; /* Add padding for larger buttons */
        border-radius: 0.5rem; /* Rounded button corners */
    }

    .btn-lg {
        font-size: 1.1rem; /* Larger font size for buttons */
    }

    .btn-warning {
        background-color: #ffc107; /* Bootstrap warning color */
        border-color: #ffc107; /* Same as background */
    }

    .btn-danger {
        background-color: #dc3545; /* Bootstrap danger color */
        border-color: #dc3545; /* Same as background */
    }

    .btn-info {
        background-color: #17a2b8; /* Bootstrap info color */
        border-color: #17a2b8; /* Same as background */
    }

    .btn-warning:hover,
    .btn-danger:hover,
    .btn-info:hover {
        opacity: 0.9; /* Slight opacity change on hover */
    }
</style>
@endpush

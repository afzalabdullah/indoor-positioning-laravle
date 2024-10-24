@extends('layouts.app')

@section('title', 'Indoor Tracking Dashboard')

@section('content')
    <div class="container mt-4">
        <h2 class="text-center mb-4">Indoor Tracking Dashboard</h2>
        <p class="text-center">Welcome, {{ auth()->user()->name }}! Here are the latest tracking insights.</p>

        <div class="row">
            <div class="col-md-6">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">Visitors Overview</div>
                    <div class="card-body">
                        <canvas id="visitorsChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Location Activity</div>
                    <div class="card-body">
                        <canvas id="locationActivityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card text-white bg-dark mb-3">
                    <div class="card-header">Recent Tracking Data</div>
                    <div class="card-body">
                        <table class="table table-dark table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Location</th>
                                    <th>Date & Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 1; $i <= 5; $i++)
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>User {{ $i }}</td>
                                        <td>Room {{ rand(1, 10) }}</td>
                                        <td>{{ now()->subHours($i)->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctxVisitors = document.getElementById('visitorsChart').getContext('2d');
        const visitorsChart = new Chart(ctxVisitors, {
            type: 'bar',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'Visitors Count',
                    data: [50, 75, 100, 120],
                    backgroundColor: 'rgba(255, 206, 86, 0.2)',
                    borderColor: 'rgba(255, 206, 86, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        const ctxLocationActivity = document.getElementById('locationActivityChart').getContext('2d');
        const locationActivityChart = new Chart(ctxLocationActivity, {
            type: 'line',
            data: {
                labels: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                datasets: [{
                    label: 'Active Locations',
                    data: [15, 20, 25, 18, 30, 28, 22],
                    fill: false,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endsection

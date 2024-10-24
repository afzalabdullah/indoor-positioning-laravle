@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #f8f9fa;
        font-family: 'Arial', sans-serif;
    }

    h1 {
        font-size: 2.5rem;
        color: #343a40;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
    }

    p {
        font-size: 1.1rem;
        color: #6c757d;
    }

    .position-relative {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
        background-color: #ffffff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        position: relative; /* Make sure this is relative for absolute children */
    }

    .point {
        position: absolute;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
        transition: transform 0.2s ease-in-out;
        cursor: pointer;
    }

    .anchor-point {
        background-color: rgba(255, 0, 0, 0.8);
    }

    .asset-point {
        background-color: rgba(0, 0, 255, 0.8);
    }

    .point:hover {
        transform: scale(1.2);
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.6);
    }

    .point i {
        font-size: 20px;
        color: white;
    }

    .unknown-device {
        background-color: red;
    }

    .table-responsive {
        margin-top: 2rem;
    }

    table {
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    th {
        background-color: #007bff;
        color: white;
        text-align: center;
    }

    th, td {
        padding: 15px;
        text-align: center;
    }

    tr:hover {
        background-color: #f1f1f1; /* Light gray on hover */
        transition: background-color 0.3s;
    }

    /* Modal styling */
    .modal-content {
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .modal-header {
        background-color: #007bff;
        color: white;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }

    .modal-footer {
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
    }

    .btn-secondary {
        background-color: #6c757d;
        border: none;
        border-radius: 5px;
    }

    .btn-secondary:hover {
        background-color: #5a6268; /* Darker gray on hover */
    }

    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin: 1rem 0;
    }
</style>

<div class="container">
    <h1 class="text-center my-4">{{ $site->name }}</h1>
    <p class="text-center mb-5">{{ $site->description }}</p>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="position-relative">
                <img src="{{ $site->image_url }}" alt="Site Image" id="site-image" style="height:500px" class="img-fluid rounded shadow">

                <!-- Anchor points -->
                @foreach ($site->anchors as $anchor)
                    <div class="point anchor-point" style="display:none" data-x="{{ $anchor->x }}" data-y="{{ $anchor->y }}" data-uid="{{ $anchor->uid }}">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                @endforeach

            </div>
        </div>

        <div class="col-md-4">
            <h3 class="my-4 text-center">Asset Details</h3>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Device UID</th>
                            <th>Device Icon</th>
                            <th>Device Name</th>
                            <th>Device Origins</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($site->assets as $asset)
                            <tr>
                                <td>{{ $asset->device_uid }}</td>
                                <td><i class="{{ $asset->device_icon }}"></i></td>
                                <td>{{ $asset->device_name }}</td>
                                <td>{{ $site->name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Point Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <div id="modalDetailContent">
                    <strong>ID:</strong> <span id="deviceId">Device ID</span><br>
                    <strong>Name:</strong> <span id="deviceName">Device Name</span><br>
                    <strong>Device Origin:</strong><span>{{ $site ? $site->name : 'Unknown' }}</span><br>
                </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const MAX_WIDTH = @json($maxWidth);
        const MAX_HEIGHT = @json($maxHeight);
        const POINT_SIZE = 40;
        const TRILATERATION_INTERVAL = 1000;

        function positionPoints(points, className) {
            const image = document.getElementById('site-image');
            const imageRect = image.getBoundingClientRect();

            points.forEach(point => {
                const pointElement = document.querySelector(`[data-uid="${point.uid}"]`) || createPointElement(point.uid, className, point.icon, point.name);

                const xRatio = point.x / MAX_WIDTH;
                const yRatio = point.y / MAX_HEIGHT;

                const x = xRatio * imageRect.width;
                const y = yRatio * imageRect.height;

                pointElement.style.left = `${x - POINT_SIZE / 2}px`;
                pointElement.style.top = `${y - POINT_SIZE / 2}px`;
                pointElement.style.display = 'flex';
            });
        }

        function createPointElement(uid, className, iconClass, deviceName) {
            const container = document.getElementById('site-image').parentElement;
            const pointElement = document.createElement('div');

            pointElement.classList.add('point', className);
            pointElement.setAttribute('data-uid', uid);

            const icon = document.createElement('i');

            if (!deviceName || deviceName.toLowerCase() === 'unknown') {
                pointElement.classList.add('unknown-device');
                icon.className = 'fas fa-question';
            } else {
                icon.className = iconClass;
            }

            pointElement.appendChild(icon);

            pointElement.addEventListener('click', () => {
                document.getElementById('deviceName').textContent = deviceName || 'Unknown Device';
                document.getElementById('deviceId').textContent = uid;
                $('#detailModal').modal('show');
            });

            container.appendChild(pointElement);
            return pointElement;
        }

        function fetchTrilateration() {
            const anchors = Array.from(document.querySelectorAll('.anchor-point')).map(anchor =>
                anchor.getAttribute('data-uid')
            );

            fetch('/trilateration/latest-position', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ anchors })
            })
            .then(response => response.json())
            .then(data => {
                const assetPoints = Object.entries(data).map(([uid, position]) => ({
                    uid,
                    x: position.x,
                    y: position.y,
                    icon: position.icon || 'default_icon.png',
                    name: position.name || 'Unknown'
                }));
                positionPoints(assetPoints, 'asset-point');
            })
            .catch(error => console.error('Error fetching trilateration data:', error));
        }

        const image = document.getElementById('site-image');
        image.addEventListener('load', function () {
            const anchors = @json($site->anchors).map(anchor => ({
                uid: anchor.uid,
                x: anchor.x,
                y: anchor.y
            }));

            positionPoints(anchors, 'anchor-point');
            fetchTrilateration();
            setInterval(fetchTrilateration, TRILATERATION_INTERVAL);
        });

        window.addEventListener('resize', function () {
            const anchors = @json($site->anchors).map(anchor => ({
                uid: anchor.uid,
                x: anchor.x,
                y: anchor.y
            }));

            positionPoints(anchors, 'anchor-point');
        });
    </script>
</div>
@endsection

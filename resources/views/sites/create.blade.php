@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="my-4 text-center text-uppercase">Create New Site</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-lg border-light">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Site Information</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('sites.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="name">Site Name</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Enter site name" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control" placeholder="Enter site description" required></textarea>
                </div>

                <div id="anchors-container">
                    <h4 class="my-3">Anchors</h4>
                    <div class="anchor mb-3 p-3 border rounded bg-light">
                        <div class="form-group">
                            <label for="anchors[0][uid]">UID</label>
                            <input type="text" name="anchors[0][uid]" class="form-control" placeholder="Enter UID" required>
                        </div>
                        <div class="form-group">
                            <label for="anchors[0][x]">X Value</label>
                            <input type="number" name="anchors[0][x]" class="form-control" step="0.0001" placeholder="Enter X value" required>
                        </div>
                        <div class="form-group">
                            <label for="anchors[0][y]">Y Value</label>
                            <input type="number" name="anchors[0][y]" class="form-control" step="0.0001" placeholder="Enter Y value" required>
                        </div>
                        <button type="button" class="btn btn-danger remove-anchor">Remove Anchor</button>
                    </div>
                </div>
                <button type="button" id="add-anchor" class="btn btn-outline-primary mb-3">Add Another Anchor</button>

                <div id="assets-container" class="my-4">
                    <h4>Devices (Assets)</h4>
                    <div class="asset mb-3 p-3 border rounded bg-light">
                        <div class="form-group">
                            <label for="assets[0][device_uid]">Device UID</label>
                            <input type="text" name="assets[0][device_uid]" class="form-control" placeholder="Enter Device UID" required>
                        </div>
                        <div class="form-group">
                            <label for="assets[0][device_icon]">Device Icon Class <small>(FontAwesome class, e.g., "fas fa-lightbulb")</small></label>
                            <input type="text" name="assets[0][device_icon]" class="form-control" placeholder="Enter Device Icon Class" required>
                        </div>
                        <div class="form-group">
                            <label for="assets[0][device_name]">Device Name</label>
                            <input type="text" name="assets[0][device_name]" class="form-control" placeholder="Enter Device Name" required>
                        </div>
                        <button type="button" class="btn btn-danger remove-asset">Remove Device</button>
                    </div>
                </div>
                <button type="button" id="add-asset" class="btn btn-outline-primary mb-3">Add Another Device</button>

                <div class="form-group">
                    <label for="image">Image</label>
                    <input type="file" name="image" id="image" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success btn-block">Create Site</button>
            </form>
        </div>
    </div>
</div>

<script>
    let anchorIndex = 1;
    let assetIndex = 1;

    document.getElementById('add-anchor').addEventListener('click', function() {
        const container = document.getElementById('anchors-container');
        const newAnchor = `
            <div class="anchor mb-3 p-3 border rounded bg-light" style="margin-top: 20px;">
                <div class="form-group">
                    <label for="anchors[${anchorIndex}][uid]">UID</label>
                    <input type="text" name="anchors[${anchorIndex}][uid]" class="form-control" placeholder="Enter UID" required>
                </div>
                <div class="form-group">
                    <label for="anchors[${anchorIndex}][x]">X Value</label>
                    <input type="number" name="anchors[${anchorIndex}][x]" class="form-control" step="0.0001" placeholder="Enter X value" required>
                </div>
                <div class="form-group">
                    <label for="anchors[${anchorIndex}][y]">Y Value</label>
                    <input type="number" name="anchors[${anchorIndex}][y]" class="form-control" step="0.0001" placeholder="Enter Y value" required>
                </div>
                <button type="button" class="btn btn-danger remove-anchor">Remove Anchor</button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newAnchor);
        anchorIndex++;
    });

    document.getElementById('add-asset').addEventListener('click', function() {
        const container = document.getElementById('assets-container');
        const newAsset = `
            <div class="asset mb-3 p-3 border rounded bg-light" style="margin-top: 20px;">
                <div class="form-group">
                    <label for="assets[${assetIndex}][device_uid]">Device UID</label>
                    <input type="text" name="assets[${assetIndex}][device_uid]" class="form-control" placeholder="Enter Device UID" required>
                </div>
                <div class="form-group">
                    <label for="assets[${assetIndex}][device_icon]">Device Icon Class <small>(FontAwesome class, e.g., "fas fa-lightbulb")</small></label>
                    <input type="text" name="assets[${assetIndex}][device_icon]" class="form-control" placeholder="Enter Device Icon Class" required>
                </div>
                <div class="form-group">
                    <label for="assets[${assetIndex}][device_name]">Device Name</label>
                    <input type="text" name="assets[${assetIndex}][device_name]" class="form-control" placeholder="Enter Device Name" required>
                </div>
                <button type="button" class="btn btn-danger remove-asset">Remove Device</button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newAsset);
        assetIndex++;
    });

    // Event delegation for removing anchors and assets
    document.getElementById('anchors-container').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-anchor')) {
            e.target.closest('.anchor').remove();
        }
    });

    document.getElementById('assets-container').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-asset')) {
            e.target.closest('.asset').remove();
        }
    });
</script>

<style>
    .card {
        border-radius: 10px;
    }
    .btn-outline-primary {
        width: 100%;
    }
</style>

@endsection

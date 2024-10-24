<!-- resources/views/partials/navbar.blade.php -->
<nav class="navbar navbar-expand-lg navbar-light fixed-top" style="background-color: black;">
    <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="https://play-lh.googleusercontent.com/xzITR21WRMhlMpMetgZppmY-3NMfxZ7jAQQ6jn-DpZPYrX98M3Jg9fpYtOpwGISswN0"
             alt="Indoor Tracking Logo" width="30" height="30">
        <span class="text-white">Indoor Tracking</span>
    </a>

    <!-- Sidebar Toggle Button for Small Screens -->
    <button class="navbar-toggler d-lg-none" type="button" id="sidebarToggle" aria-label="Toggle sidebar">
        <span class="navbar-toggler-icon"></span>
    </button>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <div class="dropdown">
            <button class="btn btn-light rounded-pill dropdown-toggle" type="button" id="profileDropdown"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-user-circle mr-2"></i> {{ auth()->user()->name }}
            </button>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profileDropdown">
                <a class="dropdown-item" href="#"><i class="fas fa-user mr-2"></i> Profile</a>
                <div class="dropdown-divider"></div>
                <form action="{{ route('logout') }}" method="POST" class="dropdown-item p-0">
                    @csrf
                    <button type="submit" class="btn btn-link dropdown-item">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>


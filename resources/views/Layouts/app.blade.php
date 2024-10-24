<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Indoor Tracking')</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">


    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa; /* Light background for content area */
            transition: background-color 0.3s;
        }

        /* Navbar Styles */
        .navbar {
            background-color: #007bff; /* Primary color for navbar */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
        }

        .navbar-brand img {
            height: 30px;
            margin-right: 10px;
        }

        /* Sidebar Styles */
        .sidebar {
            height: 100vh;
            width: 240px;
            position: fixed;
            top: 56px; /* Navbar height */
            left: 0;
            background: #343a40; /* Dark background for sidebar */
            color: #fff;
            padding-top: 20px;
            transition: all 0.3s ease;
            overflow-y: auto; /* Enable scrolling for sidebar content */
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1); /* Shadow for sidebar */
        }

        .sidebar a {
            color: #dee2e6; /* Light color for links */
            padding: 15px 20px;
            display: block;
            border-radius: 6px;
            margin: 5px 10px;
            transition: background 0.2s;
        }

        .sidebar a:hover {
            background-color: rgba(255, 255, 255, 0.1); /* Hover effect for links */
        }

        .content {
            margin-left: 240px; /* Space for sidebar */
            padding: 20px;
            padding-top: 76px; /* Adjusted for navbar height */
            height: calc(100vh - 76px);
            overflow-y: auto;
            transition: margin-left 0.3s ease;
        }

        /* Sidebar Toggle Button Styles */
        #sidebarToggle {
            position: absolute;
            top: 10px;
            left: 250px; /* Adjust to position outside the sidebar */
            z-index: 1000; /* Ensures it stays above other elements */
        }

        @media (max-width: 992px) {
            .sidebar {
                width: 0;
                overflow: hidden;
            }
            .content {
                margin-left: 0;
            }
            #sidebarToggle {
                display: block; /* Ensure toggle button is shown on small screens */
            }
        }
    </style>
</head>
<body>

    <!-- Include Navbar -->
    @include('partials.navbar')

    <!-- Sidebar Toggle Button (Visible on small screens) -->
    <button class="btn btn-outline-light d-lg-none" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Include Sidebar -->
    @include('partials.sidebar')

    <!-- Main Content -->
    <div class="content" id="content">
        @yield('content')
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <script>
    $(document).ready(function() {
        // Toggle Sidebar Visibility
        $('#sidebarToggle').click(function() {
            $('.sidebar').toggleClass('d-none'); // Toggle the sidebar
            if ($('.sidebar').hasClass('d-none')) {
                $('.content').css('margin-left', '0'); // Adjust content margin
            } else {
                $('.content').css('margin-left', '240px'); // Restore sidebar margin
            }
        });
    });
    </script>

</body>
</html>

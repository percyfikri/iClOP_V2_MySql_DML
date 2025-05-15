<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        /* Additional styles */
        .content {
        margin-left: 14rem; /* Sesuaikan dengan lebar sidebar */
        padding: 20px;
        }

        .sidebar {
            width: 100%; /* Sidebar akan menyesuaikan kolom */
            background-color: #ffffff; /* Warna latar belakang sidebar */
            padding: 20px;
        }

        .sidebar-right-shadow {
            box-shadow: 1px 0px 8px rgba(0, 0, 0, 0.1); /* Bayangan di sisi kanan sidebar */
        }

        /* NAV LINK */
        .nav-link {
            display: flex;
            align-items: center;
        }

        .nav-link:hover {
            color: blue !important;
        }

        .nav-link .icon {
            margin-right: 5px;
        }

        .custom-button {
            color: #A0A0A0;
            /* Warna teks saat tombol normal */
            transition: background-color 0.3s, color 0.3s;
            /* Efek transisi ketika hover */
            /* outline: none; */
        }

        .custom-button:hover {
            background-color: #007BFF;
            /* Warna latar belakang saat tombol dihover */
            color: white;
            /* Warna teks saat tombol dihover menjadi putih */
        }

        .custom-card {
            padding: 30px;
            width: 395px;
            height: 280px;
            background-color: #FFFFFF;
            border-radius: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .circle-image {
            width: 79px;
            height: 79px;
            border-radius: 50%;
        }

        .custom-title {
            font-weight: 600;
            font-size: 25px;
            color: #252525;
            font-family: 'Poppins', sans-serif;
            margin-top: 10px;
        }

        .custom-subtitle {
            font-weight: 400;
            font-size: 20px;
            color: #898989;
            font-family: 'Poppins', sans-serif;
            margin-top: 10px;
        }

        .custom-button {
            width: 335px;
            height: 43px;
            border-radius: 10px;
            background-color: #EAEAEA;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 15px;
            outline: none;
        }

        .custom-button-detail {
            width: 180px;
            height: 45px;
            border-radius: 10px;
            background-color: #EAEAEA;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 15px;
            margin-left: auto;
            color: #A0A0A0;
            /* Warna teks saat tombol normal */
            transition: background-color 0.3s, color 0.3s;
            /* Efek transisi ketika hover */
        }

        .custom-button-detail:hover {
            background-color: #007BFF;
            /* Warna latar belakang saat tombol dihover */
            color: white;
            /* Warna teks saat tombol dihover menjadi putih */
        }

        .button-text {
            font-weight: 500;
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            margin-left: 10px;
            margin-right: 10px;
            text-decoration: none;
            color: #A0A0A0;
        }

        .button-text:hover {
            text-decoration: none;
            color: #fff;
        }

        .text {
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
        }

        /* DROPDOWN */
        .dropdown {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }
    
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #fff;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 5px;
            overflow: hidden;
            transition: 0.3s;
            opacity: 0;
            transform: translateY(-10px);
        }
    
        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            transition: 0.3s;
        }
    
        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }
    
        .dropdown:hover .dropdown-content {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }
    
        .dropdown.active .dropdown-content {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }
        /* DROPDOWN END*/

        .btn-info {
            color: #fff !important; /* Warna teks putih */
            background-color: #0079FF !important; /* Warna latar belakang biru */
            border-color: #0079FF !important; /* Warna border biru */
        }

        .btn-info:hover {
            color: #fff !important; /* Warna teks tetap putih saat hover */
            background-color: #0056b3 !important; /* Warna latar belakang lebih gelap saat hover */
            border-color: #004a9c !important; /* Warna border lebih gelap saat hover */
        }

        .btn-info:focus, .btn-info.focus {
            color: #fff !important; /* Warna teks tetap putih saat fokus */
            background-color: #0056b3 !important; /* Warna latar belakang lebih gelap saat fokus */
            border-color: #004a9c !important; /* Warna border lebih gelap saat fokus */
            box-shadow: 0 0 0 0.2rem rgba(0, 121, 255, 0.5) !important; /* Efek fokus */
        }

        .btn-info.disabled, .btn-info:disabled {
            color: #fff !important; /* Warna teks tetap putih saat disabled */
            background-color: #0079FF !important; /* Warna latar belakang */
            border-color: #0079FF !important; /* Warna border */
        }

        .btn-info:not(:disabled):not(.disabled):active, .btn-info:not(:disabled):not(.disabled).active,
        .show > .btn-info.dropdown-toggle {
            color: #fff !important; /* Warna teks tetap putih saat aktif */
            background-color: #004a9c !important; /* Warna latar belakang lebih gelap saat aktif */
            border-color: #004080 !important; /* Warna border lebih gelap saat aktif */
        }
    </style>

    <title>Database Management System with MySQL</title>
    <link rel="icon" href={{asset("./images/logo.png")}} type="image/png">

   <!-- CSS Bootstrap -->
    {{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link href="style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
          rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <!-- JavaScript Bootstrap -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Place these in the <head> section -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.html5.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script>
        
        // Define the font files in base64
        var robotoRegularBase64 = 'BASE64_STRING_OF_ROBOTO_REGULAR';
        var robotoBoldBase64 = 'BASE64_STRING_OF_ROBOTO_BOLD';
        var robotoItalicBase64 = 'BASE64_STRING_OF_ROBOTO_ITALIC';
        var robotoBoldItalicBase64 = 'BASE64_STRING_OF_ROBOTO_BOLDITALIC';
        
        // Prepare the virtual file system object
        var vfs = {
          "Roboto-Regular.ttf": robotoRegularBase64,
          "Roboto-Bold.ttf": robotoBoldBase64,
          "Roboto-Italic.ttf": robotoItalicBase64,
          "Roboto-BoldItalic.ttf": robotoBoldItalicBase64
        };
    </script>

</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg" style="background-color: #FEFEFE;">
        <div class="container-fluid">
            <!-- <a class="navbar-brand" href="#">Navbar</a> -->
            <img src={{asset("./images/logo.png")}} alt="logo" width="104" height="65">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <div class="mx-auto">
                    <ul class="navbar-nav mb-2 mb-lg-0 justify-content-center">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="/dashboard_teacher">Dashboard Teacher</a>
                        </li>
                        <li class="nav-item d-flex align-items-center">
                            <span>/</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="/mysql-dml/teacher/topics">Database Management System with MySQL</a>
                        </li>
                    </ul>
                </div>
                <div class="dropdown">
                    <p style="margin-top: 10px; margin-right: 10px;">{{auth()->user()->name}}
                        <img src="{{ asset('./images/Group.png') }}" alt="Group" style="height: 50px; margin-right: 10px;">
                        <i class="fas fa-chevron-down" style="color: #0079FF;"></i>
                    <div class="dropdown-content" id="dropdownContent">
                        <form id="logout-form" action="{{ route('logoutt') }}" method="POST">
                            @csrf
                            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        </form>
                    </div>
                    </p>
                </div>
                <!-- <button class="btn btn-primary custom-button-sign-up" onclick="window.location.href='register.html'">Sign Up</button> -->
            </div>
        </div>
    </nav>
    <!-- ------------------------------------------------------------------------------------------ -->

    <div class="container-fluid">
        <div class="row">
            <!-- SIDEBAR -->
            <div class="col-md-2 sidebar sidebar-right-shadow" style="height: 100vh; position: fixed; overflow-y: auto;">
                <div class="sidebar-sticky" style="margin-top: 20px;">
                    <ul class="nav flex-column">
                        <li class="nav-item" style="margin-bottom: 40px;">
                            <div class="row align-items-start">
                                <div class="col">
                                    <p style="font-weight: 600; font-size: 14px; color: #34364A; margin-left: 15px;">STUDENT WEBAPPS</p>
                                </div>
                                <div class="col d-flex justify-content-center">
                                    <img src="{{asset('./images/mysql/mysql-logo.png')}}" alt="learning-logo" style="height: 45px;">
                                </div>
                            </div>
                        </li>
                        <li class="nav-item">
                            <div class="row align-items-start">
                                <div class="col-1">
                                    <i class="fas fa-folder-open" style="margin-top: 12px; margin-left: 10px; color: #676767;" id="topicIcon"></i>
                                </div>
                                <div class="col pl-3">
                                    <a class="nav-link" href="/teacher/materials" onclick="showContent('material')" style="color: #34364A;" id="materialLink">Materials Management</a>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- CONTENT -->
            <div class="col-md-10 offset-md-2 content">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Topics</th>
                            <th>Sub-Topics</th>
                            <th>Questions</th>
                            <th>Module</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $index => $item)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $item->topic_title }}</td>
                                <td>{{ $item->sub_topic_title }}</td>
                                <td>{{ $item->question }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center">
                                        @if ($item->module)
                                            <a href="{{ asset('public/mysql/DML/' . $item->module) }}" download class="btn btn-link">
                                                {{ $item->module }}
                                            </a>
                                        @else
                                            <span class="text-muted">No Module</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center">
                                        <a href="#" class="btn btn-info btn-sm mx-2 my-2" title="Details">
                                            <i class="fas fa-info-circle"></i>
                                            Details
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- FOOTER --}}
    <footer class="footer"style="background-color: #EAEAEA; color: #636363; text-align: center; padding: 10px 0; position: fixed; bottom: 0;  width: 100%; ">
        © 2023 Your Website. All rights reserved.
    </footer>   
</body>

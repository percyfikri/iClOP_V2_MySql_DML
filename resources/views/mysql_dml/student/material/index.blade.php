<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        /* Additional styles */
        .sidebar {
            min-width: 0;
            width: max-content;
            max-width: 100vw;
            background-color: #fff;
            padding: 20px 24px 20px 24px;
            z-index: 100;
            height: 100vh; /* Sidebar full height */
            position: fixed; /* Fixed di kiri */
            top: 0;
            left: 0;
        }

        .content {
            padding: 20px;
            padding-left: 32px; /* Tambahkan ini agar ada jarak dari sidebar */
            margin-bottom: 10rem;
            min-height: 200px; /* opsional, agar konten tetap proporsional */
            margin-top: 2rem;
            margin-left: 240px; /* Tambahkan/maksimalkan ini sesuai lebar sidebar */
        }

        .footer {
            background-color: #EAEAEA;
            color: #636363;
            text-align: center;
            font-size: 12px;
            padding: 5px 0;
            position: fixed;
            bottom: 0;
            left: 0;
            width: calc(100% - var(--sidebar-width, 240px));
            margin-left: var(--sidebar-width, 240px);
            z-index: 101;
        }

        /* Highlight menu aktif */
        .sidebar .nav-link.active-sidebar {
            background-color: #0077ff !important;
            color: #fff !important;
            transition: background-color 0.3s;
        }

        .sidebar .nav-link.active-sidebar i {
            color: #fff !important;
            transition: color 0.3s;
        }

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
            padding: 10px 30px;
            width: 100%;
            /* height: 280px; */ /* Hapus height tetap */
            background-color: #FFFFFF;
            border-radius: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center; /* Center horizontal */
            justify-content: center; /* Center vertical jika tinggi tetap, jika tidak bisa dihapus */
            margin: 0 auto; /* Center card di parent */
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

        .sidebar-right-shadow {
            box-shadow: 1px 0px 8px rgba(0, 0, 0, 0.1);
            /* Menambahkan bayangan ke sisi kanan */
        }
        
        .container {
            width: 100%;
            padding: 0;
            margin: 0;
        }

        #submission-detail-content .badge {
            font-size: 16px !important;
        }

        .sidebar .nav-link {
            color: #34364A !important;
            background: none !important;
        }
        .sidebar .nav-link i {
            color: #34364A !important;
            transition: color 0.3s;
        }
        .sidebar .nav-link.active-sidebar {
            background-color: #0077ff !important;
            color: #fff !important;
            /* border-radius: 8px; */
            transition: background-color 0.3s;
        }
        .sidebar .nav-link.active-sidebar i {
            color: #fff !important;
        }

        .custom-button-detail:hover,
        .custom-button-detail:focus {
            background-color: #007BFF;
            color: #fff;
        }

        .custom-button-detail:hover i,
        .custom-button-detail:focus i,
        .button-text:hover i,
        .button-text:focus i {
            color: #fff !important;
            transition: color 0.3s;
        }

        /* Pastikan icon pada tombol export selalu putih */
        #exportAllExcelBtn .fa-file-excel,
        #exportAllPdfBtn .fa-file-pdf {
            color: #fff !important;
            transition: color 0.3s;
        }

        /* Export Excel Button */
        #exportAllExcelBtn {
            background-color: #e6f4ea !important; /* hijau pudar */
            color: #198754 !important; /* hijau Bootstrap */
            border: 1.5px solid #198754 !important;
            border-radius: 10px !important;
            font-weight: 500;
            transition: background 0.3s, color 0.3s;
        }
        #exportAllExcelBtn:hover, #exportAllExcelBtn:focus {
            background-color: #198754 !important;
            color: #fff !important;
        }

        /* Export PDF Button */
        #exportAllPdfBtn {
            background-color: #fdeaea !important; /* merah pudar */
            color: #dc3545 !important; /* merah Bootstrap */
            border: 1.5px solid #dc3545 !important;
            border-radius: 10px !important;
            font-weight: 500;
            transition: background 0.3s, color 0.3s;
        }
        #exportAllPdfBtn:hover, #exportAllPdfBtn:focus {
            background-color: #dc3545 !important;
            color: #fff !important;
        }

        /* Icon warna hijau/merah saat normal (tidak hover) */
        #exportAllExcelBtn .fa-file-excel {
            color: #198754 !important; /* hijau Bootstrap */
            transition: color 0.3s;
        }
        #exportAllPdfBtn .fa-file-pdf {
            color: #dc3545 !important; /* merah Bootstrap */
            transition: color 0.3s;
        }

        /* Icon tetap putih saat hover/focus */
        #exportAllExcelBtn:hover .fa-file-excel,
        #exportAllExcelBtn:focus .fa-file-excel,
        #exportAllPdfBtn:hover .fa-file-pdf,
        #exportAllPdfBtn:focus .fa-file-pdf {
            color: #fff !important;
        }

    </style>

    <title>Database Management System with MySQL</title>
    <link rel="icon" href={{asset("./images/logo.png")}} type="image/png">

   <!-- CSS Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
    <script>
        function adjustContentMargin() {
            var sidebar = document.querySelector('.sidebar');
            var content = document.querySelector('.content');
            var footer = document.querySelector('.footer');
            var navbar = document.querySelector('.content-navbar');
            if (sidebar && content) {
                content.style.marginLeft = sidebar.offsetWidth + 'px';
            }
            if (sidebar && navbar) {
                navbar.style.marginLeft = sidebar.offsetWidth + 'px';
            }
            if (sidebar && footer) {
                document.documentElement.style.setProperty('--sidebar-width', sidebar.offsetWidth + 'px');
            }
        }
        window.addEventListener('DOMContentLoaded', adjustContentMargin);
        window.addEventListener('resize', adjustContentMargin);
    </script>

    <script>
        function showContent(contentId, sidebarId) {
            // Hide all content divs
            var contentDivs = document.getElementsByClassName('content');
            for (var i = 0; i < contentDivs.length; i++) {
                contentDivs[i].style.display = 'none';
            }

            // Show the selected content div
            var selectedContent = document.getElementById(contentId);
            if (selectedContent) {
                selectedContent.style.display = 'block';
            }

            // Remove active-sidebar from all sidebar links
            document.querySelectorAll('.sidebar .nav-link').forEach(function(link) {
                link.classList.remove('active-sidebar');
            });

            // Add active-sidebar to the clicked sidebar link
            if (sidebarId) {
                var sidebarLink = document.getElementById(sidebarId);
                if (sidebarLink) {
                    sidebarLink.classList.add('active-sidebar');
                }
            }
        }
    </script>
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            showContent('start-learning', 'learningLink');
        });
    </script>
    <script>
    $(document).ready(function () {
        // Data submissions dari backend ke JS
        var allStudentSubmissions = @json($studentSubmissions);

        // Export All to Excel
        $('#exportAllExcelBtn').on('click', function() {
            let csv = 'Name,Topic,Date,Wrong,Correct,Duration,Score\n';
            allStudentSubmissions.forEach(function(sub) {
                let durasiDetik = sub.Durasi ?? 0;
                let jam = Math.floor(durasiDetik / 3600);
                let menit = Math.floor((durasiDetik % 3600) / 60);
                let detik = durasiDetik % 60;
                let durasiFormat = sub.Durasi !== null ? 
                    (('0'+jam).slice(-2) + ':' + ('0'+menit).slice(-2) + ':' + ('0'+detik).slice(-2)) : '-';
                let nilai = (sub.TotalJawaban > 0) ? Math.round((sub.Benar / sub.TotalJawaban) * 100 * 100) / 100 : 0;
                csv += `"${sub.UserName}","${sub.SubmissionTopic}","${sub.Time}","${sub.Salah}","${sub.Benar}","${durasiFormat}","${nilai}"\n`;
            });
            var blob = new Blob([csv], { type: 'text/csv' });
            var url = window.URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'all_student_submissions.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        });

        // Export All to PDF (simple, pakai window.print)
        $('#exportAllPdfBtn').on('click', function() {
            let html = '<h2>All Student Submissions</h2><table border="1" cellpadding="5" cellspacing="0"><tr><th>Name</th><th>Topic</th><th>Date</th><th>Wrong</th><th>Correct</th><th>Duration</th><th>Score</th></tr>';
            allStudentSubmissions.forEach(function(sub) {
                let durasiDetik = sub.Durasi ?? 0;
                let jam = Math.floor(durasiDetik / 3600);
                let menit = Math.floor((durasiDetik % 3600) / 60);
                let detik = durasiDetik % 60;
                let durasiFormat = sub.Durasi !== null ? 
                    (('0'+jam).slice(-2) + ':' + ('0'+menit).slice(-2) + ':' + ('0'+detik).slice(-2)) : '-';
                let nilai = (sub.TotalJawaban > 0) ? Math.round((sub.Benar / sub.TotalJawaban) * 100 * 100) / 100 : 0;
                html += `<tr>
                    <td>${sub.UserName}</td>
                    <td>${sub.SubmissionTopic}</td>
                    <td>${sub.Time}</td>
                    <td>${sub.Salah}</td>
                    <td>${sub.Benar}</td>
                    <td>${durasiFormat}</td>
                    <td>${nilai}</td>
                </tr>`;
            });
            html += '</table>';
            var win = window.open('', '', 'width=1000,height=700');
            win.document.write(html);
            win.print();
            win.close();
        });
    });
</script>

</head>

<body>
<!-- SIDEBAR -->
<div class="sidebar sidebar-right-shadow" style="padding: 20px 0px;">
    <div class="d-flex justify-content-center mb-5" style="cursor: pointer;" onclick="window.location.href='/dashboard-student'">
        <img src={{asset("./images/logo.png")}} alt="logo" width="104" height="65">
    </div>
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
                <a class="nav-link d-flex align-items-center active-sidebar"
                    href="#"
                    id="learningLink"
                    style="white-space: nowrap; font-size: 16px;"
                    onclick="showContent('start-learning', 'learningLink')">
                        <i class="fas fa-book" style="margin-right: 12px;" id="learningIcon"></i>
                        Start Learning
                    </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center"
                   href="#"
                   id="validationLink"
                   style="white-space: nowrap; font-size: 16px;"
                   onclick="showContent('validation', 'validationLink')">
                    <i class="fas fa-check-circle" style="margin-right: 12px;" id="validationIcon"></i>
                    Student Submission
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg content-navbar" style="background-color: #FEFEFE;">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <div class="mx-auto">
                <ul class="navbar-nav mb-2 mb-lg-0 justify-content-center">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="/dashboard-student">Dashboard Student</a>
                    </li>
                    <li class="nav-item d-flex align-items-center">
                        <span>/</span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="/mysql/start">Database Management System with MySQL</a>
                    </li>
                </ul>
            </div>
            <div class="dropdown" id="dropdownContainer">
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
        </div>
    </div>
</nav>

<!-- CONTENT -->
<main id="main-content">
    <div class="content" id="start-learning">
        <h4 class="mb-4 mx-2 fw-bold">Start Learning</h4>
        <div class="custom-card">
            <div class="topic-list" style="width: 100%;">
                @foreach($topics as $topic)
                    @php
                        $limit_id = $topic->id;
                        $row = DB::table('mysql_topic_details')
                            ->where('topic_id', $limit_id)
                            ->orderBy('id', 'asc')
                            ->first();
                        $rows = $row ? $row->id : null;
                    @endphp
                    <div class="topic-row" style="display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f0f0f0;">
                        <div class="topic-title fw-semibold" style="font-size: 18px; color: #34364A;">
                            {{ $topic->title }}
                        </div>
                        <div>
                            <button type="button"
                                class="custom-button-detail button-text"
                                data-toggle="modal"
                                data-target="#exampleModal"
                                onclick="materialModal('{{ $topic->id }}','{{ $topic->title }}','{{ $rows }}')"
                                style="border: none; padding: 0; cursor: pointer;">
                                <i class="fas fa-key" style="margin-right: 5px;"></i>
                                Material Details
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div id="validation" class="content" style="display: none;">
        <div class="d-flex justify-content-between align-items-center mb-3" style="width: 100%;">
            <h4 class="fw-bold ml-2">Student Submission</h4>
            <div>
                <button id="exportAllExcelBtn" class="btn btn-success mr-2">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
                <button id="exportAllPdfBtn" class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </button>
            </div>
        </div>
        <div class="custom-card">
            
            <div class="topic-list" style="width: 100%;">
                @foreach($studentSubmissions as $submission)
                    <div class="topic-row" style="display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f0f0f0;">
                        <div>
                            <div class="fw-semibold" style="font-size: 18px; color: #34364A;">
                                {{ $submission->UserName }} - {{ $submission->SubmissionTopic }}
                            </div>
                            <div class="text-muted" style="font-size: 14px;">
                                Date: {{ date('Y-m-d H:i', strtotime($submission->Time)) }} |
                                Wrong: {{ $submission->Salah }} |
                                Correct: {{ $submission->Benar }} |
                                Duration: 
                                @php
                                    $durasiDetik = $submission->Durasi ?? 0;
                                    $jam = floor($durasiDetik / 3600);
                                    $menit = floor(($durasiDetik % 3600) / 60);
                                    $detik = $durasiDetik % 60;
                                    $durasiFormat = sprintf('%02d:%02d:%02d', $jam, $menit, $detik);
                                @endphp
                                {{ $submission->Durasi !== null ? $durasiFormat : '-' }}
                                <b>Score:</b> 
                                @php
                                    $nilai = ($submission->TotalJawaban > 0) ? round(($submission->Benar / $submission->TotalJawaban) * 100, 2) : 0;
                                @endphp
                                <b>{{ $nilai }}</b>
                            </div>
                        </div>
                        <div>
                            <button type="button"
                                class="custom-button-detail button-text"
                                style="border: none; padding: 0; cursor: pointer;"
                                data-toggle="modal"
                                data-target="#submissionDetailModal"
                                data-username="{{ $submission->UserName }}"
                                data-topic="{{ $submission->SubmissionTopic }}"
                                data-date="{{ date('Y-m-d H:i', strtotime($submission->Time)) }}"
                                data-wrong="{{ $submission->Salah }}"
                                data-correct="{{ $submission->Benar }}"
                                data-duration="{{ $submission->Durasi }}"
                                data-totaljawaban="{{ $submission->TotalJawaban }}"
                                onclick="showSubmissionDetail(this)">
                                <i class="fas fa-info-circle" style="margin-right: 5px;"></i>
                                Detail
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</main>

<!-- The Modal -->
<div class="modal fade" id="exampleModal"
     tabindex="-1"
     role="dialog"
     aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 80%;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><span id="span_title"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5">
                        <div class="text-center">
                            <img src={{asset("./images/online_virtual_machine.png")}} alt="logo" width="400"
                                 height="300">
                        </div>
                        <h5>Materi MySQL</h5>
                        
                        <input type="hidden" id="id"/>
                        <input type="hidden" id="title"/>
                        <input type="hidden" id="controller"/>
                        <span class="text-sm">Memiliki {{ $topicsCount }} materi yang akan dibahas secara detail</span>
                    </div>

                    <div class="col-md-7">
                        <b>Prerequisite knowledge : </b>
                        <div class="text-sm" style="margin-bottom: 20px">
                            <p style="margin-bottom: 5px !important">Sebelum memulai pembelajaran MySQL DML, Anda harus memiliki pengetahuan dasar tentang <b>Database Management Systems (DBMS)</b> dan <b>SQL</b>.</p>
                            1. SQL digunakan untuk mengelola dan mengakses data dalam database.<br>
                            2. DBMS membantu menyimpan, mengatur, dan mengambil data secara efisien.<br/>
                            3. Pemahaman dasar tentang konsep relasi dalam database.
                        </div>

                        <b>Requirement : </b>
                        <div class="text-sm mb-10" style="margin-bottom: 20px">
                            1. Prosesor Intel Core 2 duo atau Setara.<br>
                            2. Setidaknya memiliki RAM 4 GB atau lebih<br>
                            3. Hardisk 120 GB HDD dengan penyimpanan tersedia minimal 20 GB<br>
                            4. Koneksi Ethernet and Wi-Fi capabilities
                        </div>

                        <b>Tools : </b><br>
                        <div class="row">
                            <div class="col-md-6 text-center text-sm">
                                <a href="https://www.mysql.com/" target="_blank">
                                    <img style="width: 150px; height: 80px;" src="{{asset("./images/mysql/mysql-logo.png")}}"
                                         alt="">
                                </a>
                                <br>
                                MySQL
                            </div>
                            <div class="col-md-6 text-center text-sm">
                                <a href="https://www.apachefriends.org/" target="_blank">
                                    <img style="width: 90px; height: 80px; object-fit: cover"
                                         src="{{asset("./images/php/xampp.png")}}" alt="">
                                </a>
                                <br>
                                XAMPP
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" style="margin-left: 10px; width: 160px;"
                        onclick="materialDetailPage()">
                    <i class="fas fa-key" style="margin-right: 5px;"></i>Enroll Material
                </button>
            </div>
        </div>
    </div>
</div>

@include('mysql_dml.student.material.modal.detail_submission')
<!-- JavaScript untuk mengubah konten tab -->
<script>
    function materialModal(id, title, controller) {
        $("#id").val(id);
        $("#title").val(title);
        $("#controller").val(controller);
        $("#span_title").text(title);
        
  
    }

    function materialDetailPage() {
        var csrfToken = "{{ csrf_token() }}";
        let id = $("#id").val();
        let controller = $("#controller").val();

        // Enroll via AJAX
        $.ajax({
            type: "POST",
            url: "{{ route('student.enroll.topic') }}",
            data: {
                mysqlid: id,
                _token: csrfToken
            },
            success: function(res) {
                if(res.success){
                    // Setelah enroll, redirect ke detail materi
                    window.location.href = "{{ route('showTopicDetail') }}?mysqlid=" + id + "&start=" + controller;
                } else {
                    alert('Gagal enroll, silakan coba lagi.');
                }
            },
            error: function(xhr, status, error) {
                alert('Gagal enroll: ' + error);
            }
        });
    }

    // Fungsi untuk mengubah warna ikon, teks, dan link menjadi biru
    function changeColor(id) {
        var icon = document.getElementById(id + 'Icon');
        var link = document.getElementById(id + 'Link');
        var text = document.getElementById(id + 'Text');

        // Mengembalikan warna ikon, teks, dan link ke warna awal
        var icons = document.getElementsByClassName('fas');
        var links = document.getElementsByClassName('nav-link');
        var texts = document.getElementsByClassName('nav-link-text');
        for (var i = 0; i < icons.length; i++) {
            icons[i].style.color = '#676767';
        }
        for (var j = 0; j < links.length; j++) {
            links[j].style.color = '#34364A';
        }
        for (var k = 0; k < texts.length; k++) {
            texts[k].style.color = '#34364A';
        }

        // Mengubah warna ikon, teks, dan link menjadi biru
        icon.style.color = '#1A79E3';
        link.style.color = '#1A79E3';
        text.style.color = '#1A79E3';
    }

    // Menambahkan event listener pada setiap link
    var startLearningLink = document.getElementById('learningLink');
    startLearningLink.addEventListener('click', function () {
        changeColor('learning');
    });

    var validationLink = document.getElementById('validationLink');
    validationLink.addEventListener('click', function () {
        changeColor('validation');
    });

    var rankLink = document.getElementById('rankLink');
    rankLink.addEventListener('click', function () {
        changeColor('rank');
    });

    var settingsLink = document.getElementById('settingsLink');
    settingsLink.addEventListener('click', function () {
        changeColor('settings');
    });


    // Function to show the selected content based on sidebar link click
    function showContent(contentId, sidebarId) {
        // Hide all content divs
        var contentDivs = document.getElementsByClassName('content');
        for (var i = 0; i < contentDivs.length; i++) {
            contentDivs[i].style.display = 'none';
        }

        // Show the selected content div
        var selectedContent = document.getElementById(contentId);
        if (selectedContent) {
            selectedContent.style.display = 'block';
        }

        // Remove active-sidebar from all sidebar links
        document.querySelectorAll('.sidebar .nav-link').forEach(function(link) {
            link.classList.remove('active-sidebar');
        });

        // Add active-sidebar to the clicked sidebar link
        if (sidebarId) {
            var sidebarLink = document.getElementById(sidebarId);
            if (sidebarLink) {
                sidebarLink.classList.add('active-sidebar');
            }
        }
    }

    //  Change TAB
    $(document).ready(function () {
        $('#learning-tab').on('click', function (e) {
            e.preventDefault();
            $('#finished-tab').removeClass('active');
            $(this).tab('show');
        });

        $('#finished-tab').on('click', function (e) {
            e.preventDefault();
            $('#learning-tab').removeClass('active');
            $(this).tab('show');
        });
    });
</script>
<!-- Buttons extension -->
<!-- Buttons extension and its dependencies -->

<script>
    $(document).ready(function () {
        $("#dropdownContainer").click(function () {
            $("#dropdownContainer").toggleClass("active");
        });
        $("#dropdownContent").click(function (e) {
            e.stopPropagation();
        });
        $(document).click(function () {
            $("#dropdownContainer").removeClass("active");
        });
        $('#progressTable').DataTable({
            // Configuration options
            "paging": true,
            "ordering": true,
            "info": true,
            dom: 'Bfrtip', // Needs to include 'B' for buttons
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export to Excel',
                    title: 'Data Export REACT',
                    filename: 'react_data_export_topic_finished_student_' + new Date().toLocaleDateString() + '_' + new Date().toLocaleTimeString(),
                    customize: function (xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        // Customizations go here
                    }
                },
                'pdf',
            ]
        });
        $('#studentSubmissionTable').DataTable({
            // Configuration options
            "paging": true,
            "ordering": true,
            "info": true,
            dom: 'Bfrtip', // Needs to include 'B' for buttons
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export to Excel',
                    title: 'Data Export MySQL',
                    filename: 'mysql_data_export_student_submission_student_' + new Date().toLocaleDateString() + '_' + new Date().toLocaleTimeString(),
                    customize: function (xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        // Customizations go here
                    }
                },
               {
                extend: 'pdfHtml5',
                text: 'Export to PDF',
                title: 'Data Export MySQL',
                filename: 'mysql_data_export_student_submission_student_' + new Date().toLocaleDateString().replace(/\//g, '-') + '_' + new Date().toLocaleTimeString().replace(/:/g, '-'),
                orientation: 'portrait', // 'portrait' or 'landscape'
                pageSize: 'A4', // 'A3', 'A4', 'A5', 'LEGAL', 'LETTER' or 'TABLOID'
                exportOptions: {
                    columns: ':visible' // Export visible columns only
                },
                customize: function (doc) {
                    doc.styles.title = {
                        color: '#4c4c4c',
                        fontSize: '20',
                        alignment: 'center'
                    }
                    doc.styles.tableHeader = {
                        fillColor: '#2d4154',
                        color: 'white',
                        alignment: 'center'
                    }
                    // Customize the PDF header, footer, etc. here
                }
            },
            ]
        });
        $('#finnishedProgressTable').DataTable({
            // Configuration options
            "paging": true,
            "ordering": true,
            "info": true,
            dom: 'Bfrtip', // Needs to include 'B' for buttons
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export to Excel',
                    title: 'Data Export REACT',
                    filename: 'react_data_export_progress_student_' + new Date().toLocaleDateString() + '_' + new Date().toLocaleTimeString(),
                    customize: function (xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        // Customizations go here
                    }
                },
                {
                extend: 'pdfHtml5',
                text: 'Export to PDF',
                title: 'Data Export REACT',
                filename: 'react_data_export_progress_student_' + new Date().toLocaleDateString().replace(/\//g, '-') + '_' + new Date().toLocaleTimeString().replace(/:/g, '-'),
                orientation: 'portrait', // 'portrait' or 'landscape'
                pageSize: 'A4', // 'A3', 'A4', 'A5', 'LEGAL', 'LETTER' or 'TABLOID'
                exportOptions: {
                    columns: ':visible' // Export visible columns only
                },
                customize: function (doc) {
                    doc.styles.title = {
                        color: '#4c4c4c',
                        fontSize: '20',
                        alignment: 'center'
                    }
                    doc.styles.tableHeader = {
                        fillColor: '#2d4154',
                        color: 'white',
                        alignment: 'center'
                    }
                    // Customize the PDF header, footer, etc. here
                }
            },
            ]
        });
        $('#tableStudentReport').DataTable({
            // Configuration options
            "paging": true,
            "ordering": true,
            "info": true,
            dom: 'Bfrtip', // Needs to include 'B' for buttons
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export to Excel',
                    title: 'Data Export REACT',
                    filename: 'react_data_export_progress_student_' + new Date().toLocaleDateString() + '_' + new Date().toLocaleTimeString(),
                    customize: function (xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        // Customizations go here
                    }
                },
               {
                extend: 'pdfHtml5',
                text: 'Export to PDF',
                title: 'Data Export REACT',
                filename: 'react_data_export_progress_student_' + new Date().toLocaleDateString().replace(/\//g, '-') + '_' + new Date().toLocaleTimeString().replace(/:/g, '-'),
                orientation: 'portrait', // 'portrait' or 'landscape'
                pageSize: 'A4', // 'A3', 'A4', 'A5', 'LEGAL', 'LETTER' or 'TABLOID'
                exportOptions: {
                    columns: ':visible' // Export visible columns only
                },
                customize: function (doc) {
                    doc.styles.title = {
                        color: '#4c4c4c',
                        fontSize: '20',
                        alignment: 'center'
                    }
                    doc.styles.tableHeader = {
                        fillColor: '#2d4154',
                        color: 'white',
                        alignment: 'center'
                    }
                    // Customize the PDF header, footer, etc. here
                }
            },
            ]
        });
    });
</script>

{{-- button detail di student submission --}}
<script>
    function showSubmissionDetail(btn) {
        // Ambil data dari attribute
        var username = btn.getAttribute('data-username');
        var topic = btn.getAttribute('data-topic');
        var date = btn.getAttribute('data-date');
        var wrong = btn.getAttribute('data-wrong');
        var correct = btn.getAttribute('data-correct');
        var duration = btn.getAttribute('data-duration');
        var totalJawaban = btn.getAttribute('data-totaljawaban');
        var menit = Math.floor(duration / 60);
        var detik = duration % 60;
        var durasiFormat = duration ? (('0'+menit).slice(-2) + ':' + ('0'+detik).slice(-2)) : '-';
        var nilai = (totalJawaban > 0) ? Math.round((correct / totalJawaban) * 100 * 100) / 100 : 0;

        // Isi konten modal dengan card dan table
        document.getElementById('submission-detail-content').innerHTML = `
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-user"></i> ${username}</h5>
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <th style="width: 40%"><i class="fas fa-book"></i> Topic</th>
                                <td>${topic}</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-calendar-alt"></i> Date</th>
                                <td>${date}</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-times-circle text-danger"></i> Wrong</th>
                                <td><span class="badge badge-danger">${wrong}</span></td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-check-circle text-success"></i> Correct</th>
                                <td><span class="badge badge-success">${correct}</span></td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-clock"></i> Duration (minutes)</th>
                                <td>${durasiFormat}</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-percentage"></i> Score</th>
                                <td><span class="badge badge-primary" style="font-size:1rem">${nilai}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        `;

        window.currentSubmissionDetail = {
            username, topic, date, wrong, correct, durasiFormat, nilai
        };
    }
</script>


<style>
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

</style>
<footer class="footer"
        style="background-color: #EAEAEA; color: #636363; text-align: center; padding: 5px 0; position: fixed; bottom: 0;  width: 100%; ">
    © 2025 Your Website. All rights reserved.
</footer>

</body>


</html>

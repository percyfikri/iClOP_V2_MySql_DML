<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link href="style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <title>iCLOP</title>
    <link rel="icon" href={{asset("./images/logo.png")}} type="image/png">
    <style>
        .text {
            font-family: 'Poppins', sans-serif;
            color: #3F3F46;
            text-decoration: none
        }

        .text-list {
            font-family: 'Poppins', sans-serif;
            color: #3F3F46;
        }

        .footer {
            background-color: #EAEAEA;
            color: #636363;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        /* CSS untuk mengatur sidebar */
        .sidebar {
            width: 250px;
            background-color: #ffffff;
            height: 100%;
            position: fixed;
            top: 0;
            right: 0;
            overflow-x: hidden;
            padding-top: 20px;
        }

        /* Gaya dropdown */
        .dropdown {
            padding: 6px 8px;
            display: inline-block;
            cursor: pointer;
        }

        /* Gaya dropdown content */
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .list-item {
            display: flex;
            align-items: center;
            /* justify-content: space-between; */
            padding: 10px;
            border: 1px solid #E4E4E7;
            cursor: pointer;
            margin-bottom: 10px;
            border: none;
        }

        .list-item:hover {
            background-color: #F5F5F8;
        }

        .list-item-title {
            font-size: 18px;
            margin-left: 10px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
            color: #3F3F46;
        }

        .list-item-icon {
            font-size: 20px;
        }

        .expandable-content {
            margin-top: 0px;
            display: none;
            padding: 10px;
            border-top: 1px solid #E4E4E7;
            border: none;
            margin-left: 32px;
        }

        .radio-label {
            font-weight: bold;
            color: #333;
            font-size: 18px;
        }

        .progress-container {
            width: 100%;
            background-color: #f1f1f1;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        #progressbar {
            height: 20px;
            background-color: #4caf50;
            border-radius: 10px;
            transition: width 0.5s;
        }

        .progress-text {
            /* margin-top: 10px; */
            font-size: 18px;
            text-align: end;
        }

        .text:hover {
        color: black; /* Change text color to blue on hover */
        text-decoration: underline; /* Add underline on hover */
    }
    </style>
    
    <style>
        @media only screen and (max-width: 600px) {
            #sidebar {
                display: none; /* Hide sidebar on small screens */
            }

            div[style*="max-width: 800px"] {
                max-width: 90%; /* Adjust max-width of container */
            }
        }
    </style>
</head>
<!-- This is body test -->

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-light bg-light" style="padding: 15px 20px; border-bottom: 1px solid #E4E4E7; font-family: 'Poppins', sans-serif;">
        <a class="navbar-brand" href="{{ route('mysql_welcome') }}">
            <img src="{{ asset('images/left-arrow.png') }}" style="height: 24px; margin-right: 10px;">
            {{ $topicsNavbar->title; }}
        </a>
    </nav>

        <!-- Sidebar -->
        <div id="sidebar" class="sidebar" style="border-left: 1px solid #E4E4E7; padding: 20px; width: 100%; max-width: 400px;">
            <p class="text-list" style="font-size: 18px; font-weight: 600; font-size: 20px">
                <img src="{{ asset('images/right.png') }}" style="height: 24px; margin-right: 10px; border:1px solid; border-radius:50%"> Task List
            </p>
            <div class="progress-text">{{ $progressPercent }}%</div>
            <div class="progress-container">
                <div id="progressbar" style="width: {{ $progressPercent }}%;"></div>
            </div>
            <ul class="list pt-3">
                
                    @php
                    // Ambil mysqlid dari query string
                    $mysqlid = request()->get('mysqlid');

                    // Ambil data mysql_topic_details berdasarkan mysqlid
                    $rows = DB::table('mysql_topic_details')
                        ->where('topic_id', $mysqlid)
                        ->get();
                        $no = 1;
                    @endphp
                    @foreach($rows as $row)
                        @php
                            $no++;
                            $count_ = ($no / $detailCount) * 10;
                            $mysqldid = isset($_GET['start']) ? $_GET['start'] : '';
                            $active = ($row->id == $mysqldid) ? 'color:#000; font-weight:bold; text-decoration: underline;' : '';
                        @endphp
                        {{-- Title topic detail --}}
                        <div class="row px-4 py-2">
                            <div class="col" style="padding-bottom: 1rem;">
                                <img src="{{ asset('images/book.png') }}" style="height: 24px; margin-right: 10px;">
                                <a class="text" style="{{ $active }};" href="{{ route('showTopicDetail') }}?mysqlid={{ $mysqlid }}&start={{ $row->id }}" id="requirement">
                                    {{ $row->title }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                
            </ul>
        </div>

    <div style="padding: 20px; max-width: 68%; margin-left:5px;  ">
        <div style="border: 1px solid #ccc; padding: 20px 10px 10px 30px; border-radius: 5px;margin-bottom:10px">
            @php
                if($pdf_reader == 0):
                echo $html_start;
            @endphp
                    
                    
            @php
                else:
            @endphp
            
            <iframe src="{{ asset('mysql/DML/'. $html_start ) }}" style="width: 100%; height: 510px"></iframe></iframe>
            @php
                endif;
            @endphp

        </div>
    </div>

    {{-- Submit Query from user input --}}
    <div style="padding-top: 20px; padding-bottom: 2rem; max-width: 65%; margin-left:25px; margin-bottom: 5rem;">
        <div style="border: 1px solid #ccc; padding: 20px 10px 10px 30px; border-radius: 5px; margin-bottom: 40px;">
            <div style="padding-top: 15px; padding-bottom: 15px;">
                <form action="{{ route('submitUserInput') }}?page={{ $page }}" method="POST" style="display: flex; align-items: center; margin-bottom: 1rem;">
                    @csrf
                    <input type="hidden" name="mysqlid" value="{{ $mysqlid }}">
                    <input type="hidden" name="start" value="{{ $detail->id }}">
                    <input type="hidden" name="topic_detail_id" value="{{ $detail->id }}">
                    <input type="hidden" name="answer_number" value="{{ $page }}">
                    <div class="form-group" style="flex: 1; margin-right: 10px;">
                        <label class="mb-2" for="userInput">
                            <h4>Your Answer (SQL Query) #{{ $page }}</h4>
                        </label>
                        @if($lastAnswer)
                            @if($lastStatus == 'true')
                                <input type="text" name="userInput" id="userInput" class="form-control" value="{{ $lastAnswer }}" disabled>
                            @else
                                <input type="text" name="userInput" id="userInput" class="form-control" value="{{ $lastAnswer }}" required>
                            @endif
                        @else
                            <input type="text" name="userInput" id="userInput" class="form-control" placeholder="Masukkan jawaban Anda di sini" required>
                        @endif
                    </div>
                    @if($lastAnswer)
                        @if($lastStatus == 'true')
                            <input type="submit" value="Submit" class="btn btn-primary" style="height: 38px; margin-top: 2.7rem;" disabled>
                        @else
                            <input type="submit" value="Submit" class="btn btn-primary" style="height: 38px; margin-top: 2.7rem;">
                        @endif
                    @else
                        <input type="submit" value="Submit" class="btn btn-primary" style="height: 38px; margin-top: 2.7rem;">
                    @endif
                </form>

                {{-- Pesan Benar/Salah --}}
                @php
                    $feedback = null;
                    if(isset($lastSubmission) && $lastSubmission->feedback_id) {
                        $feedback = \App\Models\MySQL\MySqlFeedbacks::find($lastSubmission->feedback_id);
                    }
                @endphp

                @if($lastStatus == 'true')
                    <div class="alert alert-success text-start p-3">
                        <div class="fw-bold mb-2">Your Query Is Correct!</div>
                        @if($feedback)
                            @php
                                $lines = preg_split('/\r\n|\r|\n/', $feedback->feedback);
                                $feedbackText = implode('<br>', array_map('trim', $lines));
                            @endphp
                            <div style="margin-bottom:0;">{!! $feedbackText !!}</div>
                        @endif
                    </div>
                @elseif($lastStatus == 'false')
                    <div class="alert alert-danger text-start p-3">
                        <div class="fw-bold mb-2">Your Query Is Wrong!</div>
                        @if($feedback)
                            @php
                                // Gabungkan semua baris feedback jadi satu string dengan <br>
                                $lines = preg_split('/\r\n|\r|\n/', $feedback->feedback);
                                $feedbackText = implode('<br><br>', array_map('trim', $lines));
                            @endphp
                            <div style="margin-bottom:0;">{!! $feedbackText !!}</div>
                        @endif
                    </div>
                @endif

                {{-- Pagination --}}
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ $page > 1 ? route('showTopicDetail', ['mysqlid' => $mysqlid, 'start' => $detail->id, 'page' => $page-1]) : '#' }}"
                       class="btn btn-outline-secondary {{ $page == 1 ? 'disabled' : '' }}">Previous</a>
                    <span>Page {{ $page }} of {{ $totalAnswer }}</span>
                    <a href="{{ $page < $totalAnswer ? route('showTopicDetail', ['mysqlid' => $mysqlid, 'start' => $detail->id, 'page' => $page+1]) : '#' }}"
                       class="btn btn-outline-secondary {{ $page == $totalAnswer ? 'disabled' : '' }}">Next</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        © 2023 Your Website. All rights reserved.
    </footer>
    
    <script src="https://cdn.ckeditor.com/ckeditor5/34.2.0/classic/ckeditor.js"></script>
    <script type="text/javascript">
        ClassicEditor
            .create(document.querySelector('#editor'), {
                ckfinder: {
                    
                    uploadUrl: '{{route('uploadimage').'?_token='.csrf_token()}}',
                
                }
            });
    </script>
    <script>
        
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("active");
        }

        function toggleItem(item) {
            const content = item.nextElementSibling;
            const icon = item.querySelector('.list-item-icon');
            content.style.display = content.style.display === 'block' ? 'none' : 'block';
            icon.style.transform = content.style.display === 'block' ? 'rotate(180deg)' : 'none';
        }

        const radioButtons = document.querySelectorAll('input[name="itemSelection"]');
        const textElements = document.querySelectorAll('.text');

        radioButtons.forEach((button, index) => {
            button.addEventListener('change', () => {
                textElements.forEach((textElement, i) => {
                    if (i === index) {
                        textElement.style.fontWeight = 'bold';
                    } else {
                        textElement.style.fontWeight = 'normal';
                    }
                });
            });
        });
    </script>
</body>

</html>

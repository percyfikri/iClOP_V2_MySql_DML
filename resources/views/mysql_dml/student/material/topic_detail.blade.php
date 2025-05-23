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
    
    
    {{-- Tampilkan Soal --}}
    @if(isset($questions) && count($questions) == 0)
        <div class="alert alert-warning" style="max-width: 65%; margin: 2rem 0 2rem 25px;">
            There are no related questions on this subtopic.
        </div>
    @elseif(isset($currentQuestion))
        <div style="padding-top: 20px; max-width: 65%; margin-left:25px;">
            <div style="border: 1px solid #ccc; padding: 20px 10px 10px 30px; border-radius: 5px;">
                <p class="text-list" style="font-size: 22px; font-weight: 600;">
                    Question {{ $questionIndex + 1 }} of {{ count($questions) }}
                </p>
                <ol>
                    <li style="margin-bottom: 10px;">
                        {{ $currentQuestion->question }}
                    </li>
                </ol>
            </div>
        </div>
    @endif

    {{-- Submit Query from user input --}}
    @if(isset($currentQuestion))
    <div style="padding-top: 20px; padding-bottom: 2rem; max-width: 65%; margin-left:25px; margin-bottom: 5rem;">
        <div style="border: 1px solid #ccc; padding: 20px 10px 10px 30px; border-radius: 5px; margin-bottom: 40px;">
            <div style="padding-top: 15px; padding-bottom: 15px;">
                <form action="{{ route('submitUserInput') }}" method="POST" style="display: flex; align-items: center; margin-bottom: 1rem;">
                    @csrf
                    <input type="hidden" name="mysqlid" value="{{ $mysqlid }}">
                    <input type="hidden" name="start" value="{{ $currentQuestion->topic_detail_id }}">
                    <input type="hidden" name="topic_detail_id" value="{{ $currentQuestion->topic_detail_id }}">
                    <input type="hidden" name="question_id" value="{{ $currentQuestion->id }}">
                    <input type="hidden" name="question_index" value="{{ $questionIndex }}">
                    <div class="form-group" style="flex: 1; margin-right: 10px;">
                        <label class="mb-2" for="userInput">Your Answer</label>
                        @if($lastAnswer)
                            @if($lastStatus == 'benar')
                                <input type="text" name="userInput" id="userInput" class="form-control" value="{{ $lastAnswer }}" disabled>
                            @else
                                <input type="text" name="userInput" id="userInput" class="form-control" value="{{ $lastAnswer }}" required>
                            @endif
                        @else
                            <input type="text" name="userInput" id="userInput" class="form-control" placeholder="Masukkan jawaban Anda di sini" required>
                        @endif
                    </div>
                    @if($lastAnswer)
                        @if($lastStatus == 'benar')
                            {{-- Tidak tampilkan tombol submit --}}
                        @else
                            <input type="submit" value="Submit" class="btn btn-primary" style="height: 38px; margin-top: 30px;">
                        @endif
                    @else
                        <input type="submit" value="Submit" class="btn btn-primary" style="height: 38px; margin-top: 30px;">
                    @endif
                </form>

                {{-- Pesan Benar/Salah --}}
                @if($lastStatus == 'true')
                    <div class="alert alert-success">
                        Jawaban Anda BENAR!
                    </div>
                @elseif($lastStatus == 'false')
                    <div class="alert alert-danger">
                        Jawaban Anda SALAH!
                    </div>
                @endif

                {{-- Kolom 2: Run Query --}}
                {{-- <form action="{{ route('mysql_run_user_query') }}" method="POST" style="display: flex; align-items: center;">
                    @csrf
                    <input type="hidden" name="topic_detail_id" value="{{ $row->id }}">
                    <div class="form-group" style="flex: 1; margin-right: 10px;">
                        <label class="mb-2" for="runInput">Run Query</label>
                        <input type="text" name="runInput" id="runInput" class="form-control" placeholder="Enter query to run" required>
                    </div>
                    <input type="submit" value="Run" class="btn btn-success" style="height: 38px; margin-top: 24px;">
                </form> --}}

                {{-- Tampilan sementar tanpa fungsi --}}
                <form style="display: flex; align-items: center;">
                    <input type="hidden" name="topic_detail_id" value="{{ $row->id }}">
                    <div class="form-group" style="flex: 1; margin-right: 10px;">
                        <label class="mb-2" for="runInput">Check Result Query</label>
                        <input type="text" name="runInput" id="runInput" class="form-control" placeholder="Enter query to run" disabled>
                    </div>
                    <button type="button" class="btn btn-success" style="height: 38px; width: 75px; margin-top: 30px;" disabled>Run</button>
                </form>

                {{-- Tombol Previous & Next dengan Bootstrap Pagination --}}
                <nav aria-label="Soal navigation" style="margin-top: 2rem;">
                    <ul class="pagination justify-content-end">
                        {{-- Previous --}}
                        <li class="page-item {{ $questionIndex == 0 ? 'disabled' : '' }}">
                            <a class="page-link" 
                                href="{{ $questionIndex == 0 ? '#' : route('showTopicDetail', ['mysqlid' => $mysqlid, 'start' => $detail->id, 'q' => max(0, $questionIndex - 1)]) }}"
                                tabindex="-1"
                                @if($questionIndex == 0) aria-disabled="true" @endif
                            >Previous</a>
                        </li>
                        {{-- Nomor Soal --}}
                        @for($i = 0; $i < count($questions); $i++)
                            <li class="page-item {{ $i == $questionIndex ? 'active' : '' }}">
                                <a class="page-link" 
                                    href="{{ route('showTopicDetail', ['mysqlid' => $mysqlid, 'start' => $detail->id, 'q' => $i]) }}"
                                    @if($i == $questionIndex) aria-current="page" @endif
                                >{{ $i + 1 }}</a>
                            </li>
                        @endfor
                        {{-- Next --}}
                        <li class="page-item {{ $questionIndex >= count($questions) - 1 ? 'disabled' : '' }}">
                            <a class="page-link" 
                                href="{{ $questionIndex >= count($questions) - 1 ? '#' : route('showTopicDetail', ['mysqlid' => $mysqlid, 'start' => $detail->id, 'q' => min(count($questions) - 1, $questionIndex + 1)]) }}"
                                @if($questionIndex >= count($questions) - 1) aria-disabled="true" @endif
                            >Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    @endif

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

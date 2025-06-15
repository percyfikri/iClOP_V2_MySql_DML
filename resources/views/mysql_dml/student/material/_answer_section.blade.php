@php
    // Hitung jumlah subtopik pada topik ini
    $totalSubtopics = \App\Models\MySQL\MySqlTopicDetails::where('topic_id', $mysqlid)->count();
    // Hitung jumlah subtopik yang sudah completed (semua jawaban benar)
    $completedSubtopics = \App\Models\MySQL\MySqlTopicDetails::where('topic_id', $mysqlid)
        ->get()
        ->filter(function($subtopic) {
            return \DB::table('mysql_student_submissions')
                ->where('user_id', Auth::user()->id)
                ->where('topic_detail_id', $subtopic->id)
                ->where('status', 'true')
                ->count() >= $subtopic->total_question;
        })->count();
    $allSubtopicsCompleted = ($totalSubtopics > 0 && $completedSubtopics == $totalSubtopics);

    $isReset = \DB::table('mysql_user_reset')
        ->where('user_id', Auth::id())
        ->where('topic_id', $mysqlid)
        ->value('is_reset');
@endphp

{{-- Submit Query from user input --}}
    <div id="answer-section">
        {{-- <div style="border: 1px solid #ccc; padding: 20px 20px; border-radius: 10px; margin-bottom: 40px;"> --}}
            <div style="padding-top: 15px; padding-bottom: 15px;">
                <div style="border: 1px solid #ccc; padding: 20px 10px 10px 30px; border-radius: 10px; margin-bottom: 20px;">
                    <form action="{{ route('submitUserInput') }}?page={{ $page }}" method="POST" style="display: flex; align-items: flex-start; margin-bottom: 1rem;">
                        @csrf
                        <input type="hidden" name="mysqlid" value="{{ $mysqlid }}">
                        <input type="hidden" name="start" value="{{ $detail->id }}">
                        <input type="hidden" name="topic_detail_id" value="{{ $detail->id }}">
                        <input type="hidden" name="answer_number" value="{{ $page }}">
                        <div class="form-group" style="flex: 1; margin-right: 10px;">
                            <label class="mb-2" for="userInput">
                                <h4>{{ $page }}. Your Answer (SQL Query)</h4>
                            </label>
                            @if($lastAnswer)
                                @if($lastStatus == 'true')
                                    <textarea name="userInput" id="userInput" class="form-control" rows="4" disabled style="background-color: #f1f1f1; color: #525252;">{{ $lastAnswer }}</textarea>
                                @else
                                    <textarea name="userInput" id="userInput" class="form-control" rows="4" required>{{ $lastAnswer }}</textarea>
                                @endif
                            @else
                                <textarea name="userInput" id="userInput" class="form-control" rows="4" placeholder="Input your query in here" required></textarea>
                            @endif
                        </div>
                        <div style="margin-top: 2.8rem; margin-left: 10px;">
                            <button type="submit"
                                class="btn btn-primary d-flex align-items-center justify-content-center"
                                id="submit-btn"
                                style="width: max-content; min-width: 90px; min-height: 40px; padding: 0 22px; position: relative;"
                                @if($lastStatus == 'true' || $isReset) disabled @endif>
                                <span id="submit-btn-text" style="width:100%; text-align:center; font-weight: 600;">Submit</span>
                                <span id="submit-spinner"
                                    class="spinner-border spinner-border-sm"
                                    style="display:none; margin-left: 1.1rem; margin-right: 1.1rem;"
                                    role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </form>
                    
                    {{-- Pesan Feedback Benar/Salah --}}
                    @php
                        $feedback = null;
                        if(isset($lastSubmission) && $lastSubmission->feedback_id) {
                            $feedback = \App\Models\MySQL\MySqlFeedbacks::find($lastSubmission->feedback_id);
                        }
                    @endphp

                    @if($lastStatus == 'true')
                        <div class="mb-4">
                                <div class="fw-bold mb-2" style="background-color: #25923e; border-radius: 0.5rem; max-width: fit-content; padding: 0.25rem 0.5rem">
                                    <div class="text-white">
                                        Your Query Is Correct!
                                    </div>
                                </div>
                            @if($feedback)
                                @php
                                    $lines = preg_split('/\r\n|\r|\n/', $feedback->feedback);
                                    $feedbackText = implode('<br>', array_map('trim', $lines));
                                @endphp
                                <div class="fw-semibold" style="color: #25923e; border-radius: 0.5rem; max-width: fit-content; padding: 0.25rem">{!! $feedbackText !!}</div>
                            @endif
                        </div>
                    @elseif($lastStatus == 'false')
                            <div class="fw-bold mb-2" style="background-color: #ff0000; border-radius: 0.5rem; max-width: fit-content; padding: 0.25rem 0.5rem">
                                <div class="text-white">
                                    Your Query Is Wrong!
                                </div>
                            </div>
                            @if($feedback)
                                @php
                                    // Gabungkan semua baris feedback jadi satu string dengan <br>
                                    $lines = preg_split('/\r\n|\r|\n/', $feedback->feedback);
                                    $feedbackText = implode('<br>', array_map('trim', $lines));
                                @endphp
                                <div class="fw-semibold" style="color: red; border-radius: 0.5rem; max-width: fit-content; padding: 0.25rem">{!! $feedbackText !!}</div>
                            @endif
                        </div>
                    @endif
                </div>

                <div style="border: 1px solid #ccc; padding: 20px 10px 20px 30px; border-radius: 10px;">
                    {{-- Query Data Section --}}
                    <form id="run-query-form" method="POST" action="{{ route('runUserSelectQuery') }}">
                        @csrf
                        <input type="hidden" name="mysqlid" value="{{ $mysqlid }}">
                        <label for="userSelectQuery" class="mb-2 fw-semibold">Try Query Data (SELECT only):</label>
                        <div class="d-flex align-items-start mb-2">
                            <textarea name="userSelectQuery" id="userSelectQuery" class="form-control me-3" rows="4" placeholder="e.g. SELECT * FROM mk" style="resize: vertical;"></textarea>
                            <button type="submit" class="btn btn-success fw-semibold" style="height: 40px; white-space: nowrap;" @if($isReset) disabled @endif>Run Query</button>
                        </div>
                    </form>
                    <div id="query-result" class="mt-3">
                        @if(session('query_result'))
                            {!! session('query_result') !!}
                        @endif
                    </div>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-between mt-5">
                        <button type="button"
                            class="btn btn-outline-secondary answer-pagination"
                            data-page="{{ $page - 1 }}"
                            {{ $page == 1 ? 'disabled' : '' }}>
                            Previous
                        </button>
                        <span class="fw-semibold">Answer {{ $page }} of {{ $totalAnswer }}</span>
                        @if($page == $totalAnswer)
                            @php
                                // Cari subtopik berikutnya
                                $nextDetail = \App\Models\MySQL\MySqlTopicDetails::where('topic_id', $mysqlid)
                                    ->where('id', '>', $detail->id)
                                    ->orderBy('id')
                                    ->first();
                                // Cek semua jawaban benar
                                $allCorrect = \DB::table('mysql_student_submissions')
                                    ->where('user_id', Auth::user()->id)
                                    ->where('topic_detail_id', $detail->id)
                                    ->where('status', 'true')
                                    ->count() >= $detail->total_question;
                            @endphp
                            @if($nextDetail)
                                {{-- @if($allCorrect)
                                    <a href="{{ route('showTopicDetail', ['mysqlid' => $mysqlid, 'start' => $nextDetail->id]) }}"
                                       id="import-next-btn"
                                       class="btn btn-primary fw-semibold">
                                        Next Sub-Topics &rarr;
                                    </a>
                                @else
                                    <button class="btn btn-primary fw-semibold" disabled>
                                        Next Sub-Topics &rarr;
                                    </button>
                                @endif --}}
                                <a href="{{ route('showTopicDetail', ['mysqlid' => $mysqlid, 'start' => $nextDetail->id]) }}"
                                    id="import-next-btn"
                                    class="btn btn-primary fw-semibold">
                                     Next Sub-Topics &rarr;
                                 </a>
                            @else
                                @if($page == $totalAnswer && !$nextDetail)
                                    @if($allSubtopicsCompleted)
                                        @if($isReset)
                                            <button id="reset-testing-db-btn" class="btn btn-primary fw-semibold" disabled>
                                                Submit All Answer
                                            </button>
                                        @else
                                            <button id="reset-testing-db-btn" class="btn btn-primary fw-semibold" data-mysqlid="{{ $mysqlid }}">
                                                Submit All Answer
                                            </button>
                                        @endif
                                    @else
                                        @if($isReset)
                                            <button id="reset-testing-db-btn" class="btn btn-primary fw-semibold" disabled>
                                                Submit All Answer
                                            </button>
                                        @else
                                            <button id="reset-testing-db-btn" class="btn btn-primary fw-semibold" data-mysqlid="{{ $mysqlid }}">
                                                Submit All Answer
                                            </button>
                                        @endif
                                    @endif
                                @endif
                            @endif
                        @else
                            <button type="button"
                                class="btn btn-outline-secondary answer-pagination"
                                data-page="{{ $page + 1 }}">
                                Next
                            </button>
                        @endif
                    </div>
                    
                    {{-- Next Sub-Topics Button --}}
                </div>
            </div>
        {{-- </div> --}}
    </div>

<script>


document.addEventListener('click', function(e) {
    if (e.target && e.target.id === 'reset-testing-db-btn' && !e.target.disabled) {
        e.preventDefault();
        const btn = e.target;
        btn.disabled = true;

        // 1. Simpan durasi pengerjaan
        fetch('{{ route('student.finish.topic') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                mysqlid: btn.getAttribute('data-mysqlid')
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                // 2. Jika sukses, lanjut reset database
                fetch('{{ route('student.reset.testing.db') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        mysqlid: btn.getAttribute('data-mysqlid')
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success){
                        window.location.href = '{{ url("/mysql/start") }}';
                    } else {
                        alert('Gagal reset: ' + data.message);
                        btn.disabled = false;
                        btn.textContent = 'Submit All Answer';
                    }
                });
            } else {
                alert('Gagal menyimpan durasi pengerjaan.');
                btn.disabled = false;
                btn.textContent = 'Submit All Answer';
            }
        });
    }
});
</script>

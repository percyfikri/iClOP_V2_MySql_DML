{{-- Submit Query from user input --}}
    <div id="answer-section">
        <div style="border: 1px solid #ccc; padding: 20px 10px 10px 30px; border-radius: 5px; margin-bottom: 40px;">
            <div style="padding-top: 15px; padding-bottom: 15px;">
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
                                <textarea name="userInput" id="userInput" class="form-control" rows="4" disabled>{{ $lastAnswer }}</textarea>
                            @else
                                <textarea name="userInput" id="userInput" class="form-control" rows="4" required>{{ $lastAnswer }}</textarea>
                            @endif
                        @else
                            <textarea name="userInput" id="userInput" class="form-control" rows="4" placeholder="Input your answer in here" required></textarea>
                        @endif
                    </div>
                    <div style="margin-top: 3rem; margin-left: 10px;">
                        <button type="submit"
                            class="btn btn-primary d-flex align-items-center justify-content-center"
                            id="submit-btn"
                            style="width: max-content; min-width: 90px; min-height: 40px; padding: 0 22px; position: relative;"
                            @if($lastStatus == 'true') disabled @endif>
                            <span id="submit-btn-text" style="width:100%; text-align:center;">Submit</span>
                            <span id="submit-spinner"
                                class="spinner-border spinner-border-sm"
                                style="display:none; margin-left: 1.1rem; margin-right: 1.1rem;"
                                role="status" aria-hidden="true"></span>
                        </button>
                    </div>
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
                    <button type="button"
                        class="btn btn-outline-secondary answer-pagination"
                        data-page="{{ $page - 1 }}"
                        {{ $page == 1 ? 'disabled' : '' }}>
                        Previous
                    </button>
                    <span class="fw-semibold">Answer {{ $page }} of {{ $totalAnswer }}</span>
                    <button type="button"
                        class="btn btn-outline-secondary answer-pagination"
                        data-page="{{ $page + 1 }}"
                        {{ $page == $totalAnswer ? 'disabled' : '' }}>
                        Next
                    </button>
                </div>

                {{-- Next Sub-Topics Button --}}
                @if($page == $totalAnswer)
                    @php
                        // Cari subtopik berikutnya
                        $nextDetail = \App\Models\MySQL\MySqlTopicDetails::where('topic_id', $mysqlid)
                            ->where('id', '>', $detail->id)
                            ->orderBy('id')
                            ->first();
                    @endphp
                    @if($nextDetail)
                        <div class="mt-4 text-end">
                            <a href="{{ route('showTopicDetail', ['mysqlid' => $mysqlid, 'start' => $nextDetail->id]) }}"
                            class="btn btn-primary fw-semibold">
                                Next Sub-Topics &rarr;
                            </a>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
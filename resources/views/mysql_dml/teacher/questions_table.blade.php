<div>
    {{-- Judul Topics List --}}
    <div class="mb-0 d-flex justify-content-between align-items-center">
        <h4 class="mb-3 fw-bold">Questions Management</h4>
    </div>
    {{-- Tombol Add Topic di bawah judul, tetap di kanan --}}
    <div class="mb-3 d-flex justify-content-end align-items-center">
        <button class="btn btn-primary" id="add-question-btn" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
            <i class="fas fa-plus"></i> Add Question
        </button>
    </div>
    <div class="card shadow-sm p-4 mb-4" style="border-radius: 18px;">
        <table class="table table-bordered table-hover mb-0 align-middle">
            <thead class="table-primary">
                <tr class="text-center align-middle">
                    <th style="width: 45px;">No</th>
                    <th>Question</th>
                    <th>Answer Key</th>
                    <th>Sub-Topic</th>
                    <th style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($questions as $index => $q)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $q->question }}</td>
                        <td>{{ $q->answer_key }}</td>
                        <td>{{ $q->topicDetail->title ?? '-' }}</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <button class="btn btn-sm btn-info" title="View Details" onclick="viewQuestionDetails({{ $q->id }})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning" title="Edit" onclick="editQuestion({{ $q->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-question-btn" data-id="{{ $q->id }}" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No questions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Add Question -->
<div class="modal fade" id="addQuestionModal" tabindex="-1" aria-labelledby="addQuestionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="add-question-form" class="needs-validation" novalidate>
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="addQuestionModalLabel">Add Question</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Question</label>
                        <input type="text" class="form-control" name="question" required autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Answer Key</label>
                        <input type="text" class="form-control" name="answer_key" required autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Sub-Topic</label>
                        <select class="form-control" name="topic_detail_id" required>
                            <option value="">-- Select Sub-Topic --</option>
                            @foreach($subtopics as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal View Question Details -->
<div class="modal fade" id="viewQuestionDetailsModal" tabindex="-1" aria-labelledby="viewQuestionDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 1rem;">
            <div class="modal-header text-white" style="background-color: #258eff; border-radius: 0.9rem 0.9rem 0 0;">
                <h5 class="modal-title fw-bold" id="viewQuestionDetailsModalLabel">Question Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(100%);"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Sub-Topic</label>
                    <input type="text" class="form-control" id="detail_subtopic" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Question</label>
                    <input type="text" class="form-control" id="detail_question" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Answer Key</label>
                    <input type="text" class="form-control" id="detail_answer_key" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Modul</label>
                    <input type="text" class="form-control" id="detail_modul" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Created By</label>
                    <input type="text" class="form-control" id="detail_created_by" readonly>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Question -->
<div class="modal fade" id="editQuestionModal" tabindex="-1" aria-labelledby="editQuestionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="edit-question-form" class="needs-validation" novalidate>
            @csrf
            <input type="hidden" id="edit_question_id" name="question_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="editQuestionModalLabel">Edit Question</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Question</label>
                        <input type="text" class="form-control" id="edit_question" name="question" required autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Answer Key</label>
                        <input type="text" class="form-control" id="edit_answer_key" name="answer_key" required autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Sub-Topic</label>
                        <select class="form-control" id="edit_topic_detail_id" name="topic_detail_id" required>
                            @foreach($subtopics as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Delete Question Script --}}
<script>
    $(document).on('click', '.delete-question-btn', function() {
        var id = $(this).data('id');
        if (confirm('Delete this question?')) {
            $.ajax({
                url: '/mysql/teacher/questions/' + id + '/delete',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE'
                },
                success: function() {
                    $.get("{{ route('teacher.questions.table') }}", function(data) {
                        $('#main-table-content').html(data);
                    });
                },
                error: function() {
                    alert('Failed to delete question.');
                }
            });
        }
    });

    function viewQuestionDetails(id) {
        $.get('/mysql/teacher/questions/' + id, function(data) {
            console.log(data); // Debug: lihat data yang diterima
            $('#detail_subtopic').val(data.topic_detail?.title ?? '-');
            $('#detail_question').val(data.question ?? '-');
            $('#detail_answer_key').val(data.answer_key ?? '-');
            $('#detail_modul').val(data.file_name ?? '-');
            $('#detail_created_by').val(data.created_by_user?.name ?? '-');
            $('#viewQuestionDetailsModal').modal('show');
        }).fail(function(xhr) {
            console.log(xhr.responseText); // Debug: lihat error response
            alert('Failed to fetch question details.');
        });
    }
</script>
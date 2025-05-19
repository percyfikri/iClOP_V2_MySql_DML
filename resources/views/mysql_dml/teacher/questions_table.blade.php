<div>
    {{-- Judul Topics List --}}
    <div class="mb-0 d-flex justify-content-between align-items-center">
        <h4 class="mb-3 fw-bold">Questions Management</h4>
    </div>
    {{-- Tombol Add Topic di bawah judul, tetap di kanan --}}
    <div class="mb-3 d-flex justify-content-end align-items-center">
        <button class="btn btn-primary fw-bold" id="add-question-btn" data-bs-toggle="modal" data-bs-target="#addQuestionModal" style="border-radius: 0.5rem;">
            <i class="fas fa-plus"></i> Add Question
        </button>
    </div>
    <div class="card shadow-sm p-4 mb-4" style="border-radius: 18px;">
        <table class="table table-bordered table-hover mb-0 align-middle">
            <thead class="table-primary">
                <tr class="text-center align-middle">
                    <th style="width: 45px;">No</th>
                    <th>Sub-Topic</th>
                    <th>Question</th>
                    <th>Answer Key</th>
                    <th style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($questions as $index => $q)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $q->topicDetail->title ?? '-' }}</td>
                        <td>{{ $q->question }}</td>
                        <td>{{ $q->answer_key }}</td>
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
    <div class="modal-dialog modal-xl">
        <form id="add-question-form" class="needs-validation" novalidate enctype="multipart/form-data">
            @csrf
            <div class="modal-content" style="border-radius: 1rem;">
                <div class="modal-header text-white" style="background-color: #258eff; border-radius: 0.9rem 0.9rem 0 0;">
                    <h5 class="modal-title fw-bold" id="addQuestionModalLabel">Add Question</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(100%);"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Kolom kiri: Preview Modul -->
                        <div class="col-md-7 d-flex align-items-stretch" id="add-modul-preview-container">
                            <div class="w-100">
                                <div class="d-flex justify-content-between mb-2">
                                    <label class="form-label fw-semibold">Preview Modul</label>
                                    <a id="add-open-pdf-btn" href="#" class="btn btn-sm btn-add-subtopic-hover disabled"
                                       tabindex="-1" aria-disabled="true"
                                       style="border: 1px solid #258eff; color: #258eff; background-color: white; border-radius: 0.5rem; pointer-events: none;">
                                        <i class="fas fa-file-pdf"></i> Open PDF
                                    </a>
                                </div>
                                <div id="add-modul-preview-message" class="text-muted text-center">Please select a sub-topic to preview module.</div>
                                <div id="add-modul-preview-file"></div>
                            </div>
                        </div>
                        <!-- Kolom kanan: Form (pakai card) -->
                        <div class="col-md-5">
                            <div class="card p-3 shadow-sm" style="border-radius: 1rem;">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Sub-Topic</label>
                                    <select class="form-control" id="add_topic_detail_id" name="topic_detail_id" required>
                                        <option value="" disabled selected>-- Select a Sub-Topic --</option>
                                        @foreach($subtopics as $sub)
                                            <option value="{{ $sub->id }}">{{ $sub->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Question</label>
                                    <input type="text" class="form-control" id="add_question" name="question" required autocomplete="off">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Answer Key</label>
                                    <input type="text" class="form-control" id="add_answer_key" name="answer_key" required autocomplete="off">
                                </div>
                            </div>
                        </div>
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

<!-- Modal Edit Question -->
<div class="modal fade" id="editQuestionModal" tabindex="-1" aria-labelledby="editQuestionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <!-- Ubah ke modal-xl agar lebih lebar -->
        <form id="edit-question-form" class="needs-validation" novalidate enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="edit_question_id" name="question_id">
            <div class="modal-content" style="border-radius: 1rem;">
                <div class="modal-header text-white" style="background-color: #258eff; border-radius: 0.9rem 0.9rem 0 0;">
                    <h5 class="modal-title fw-bold" id="editQuestionModalLabel">Edit Question</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Kolom kiri: Preview Modul -->
                        <div class="col-md-7 d-flex align-items-stretch" id="edit-modul-preview-container">
                            <div class="w-100">
                                <div class="d-flex justify-content-between mb-2">
                                    <label class="form-label fw-semibold">Preview Modul</label>
                                    <a id="edit-open-pdf-btn" href="#" class="btn btn-sm btn-add-subtopic-hover disabled"
                                       tabindex="-1" aria-disabled="true"
                                       style="border: 1px solid #258eff; color: #258eff; background-color: white; border-radius: 0.5rem; pointer-events: none;">
                                        <i class="fas fa-file-pdf"></i> Open PDF
                                    </a>
                                </div>
                                <div id="edit-modul-preview-message" class="text-muted text-center">No module uploaded.</div>
                                <div id="edit-modul-preview-file"></div>
                            </div>
                        </div>
                        <!-- Kolom kanan: Form (pakai card) -->
                        <div class="col-md-5">
                            <div class="card p-3 shadow-sm" style="border-radius: 1rem;">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Sub-Topic</label>
                                    <select class="form-control" id="edit_topic_detail_id" name="topic_detail_id" required>
                                        @foreach($subtopics as $sub)
                                            <option value="{{ $sub->id }}">{{ $sub->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Question</label>
                                    <input type="text" class="form-control" id="edit_question" name="question" required autocomplete="off">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Answer Key</label>
                                    <input type="text" class="form-control" id="edit_answer_key" name="answer_key" required autocomplete="off">
                                </div>
                            </div>
                        </div>
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
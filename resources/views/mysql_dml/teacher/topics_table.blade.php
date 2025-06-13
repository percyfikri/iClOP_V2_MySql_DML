<div>
    {{-- Judul Topics List --}}
    <div class="mb-0 d-flex justify-content-between align-items-center">
        <h4 class="mb-3 fw-bold">Topics Management</h4>
    </div>
    {{-- Tombol Add Topic di bawah judul, tetap di kanan --}}
    <div class="mb-3 d-flex justify-content-end align-items-center">
        <button class="btn btn-primary fw-bold" id="add-topic-btn" data-bs-toggle="modal" data-bs-target="#addTopicModal" style="border-radius: 0.5rem;">
            <i class="fas fa-plus"></i> Add Topic
        </button>
    </div>
    <div class="card shadow-sm p-4 mb-4" style="border-radius: 18px;">
        <table class="table table-bordered table-hover mb-5">
            <thead class="table-primary">
                <tr class="text-center">
                    <th style="width: 45px;">No</th>
                    <th>Topic</th>
                    <th>Sub-Topics</th>
                    <th>Created By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data1 as $index => $data)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $data->title }}</td>
                        <td>
                            @if($data->topicDetails && count($data->topicDetails) > 0)
                                <ul class="mb-0 pl-3" style="list-style-type: disc;">
                                    @foreach($data->topicDetails as $subtopic)
                                        <li>{{ $subtopic->title }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $data->createdBy->name ?? '-' }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-warning" title="Edit" onclick="editTopic({{ $data->id }})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger ms-1 delete-topic-btn" data-id="{{ $data->id }}" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Add Topic & Sub-Topic -->
<div class="modal fade" id="addTopicModal" tabindex="-1" aria-labelledby="addTopicModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('teacher.topics.addTopicSubtopic') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-content" style="border-radius: 1rem;">
                <div class="modal-header text-white" style="background-color: #258eff; border-radius: 0.9rem 0.9rem 0 0;">
                    <h5 class="modal-title" id="addTopicModalLabel">Add Topic & Sub-Topic</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(100%);"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4 row">
                        <div class="col-md-6">
                            <label for="topicTitle" class="form-label fw-bold">Topic</label>
                            <input type="text" class="form-control" id="topicTitle" name="topic_title" autocomplete="off" required>
                        </div>
                        <div class="col-md-6" style="max-width: 180px;">
                            <label for="countdown_minutes" class="form-label fw-bold">Timer (minutes)</label>
                            <input style="border: 1px solid #fa6767; width: 100px; background-color: #fce3e3;" type="number" class="form-control fw-semibold" id="countdown_minutes" name="countdown_minutes" value="{{ old('countdown_minutes', isset($topic) ? ($topic->countdown_seconds ?? 3600) / 60 : 60) }}" min="1" step="1">
                            <small class="form-text text-muted">Ex : 60 minutes</small>
                        </div>
                    </div>
                    <div id="subtopics-container">
                        <div class="row mb-3 subtopic-group align-items-center" style="border-top: 1px solid #ccc">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Sub-Topic</label>
                                <input type="text" class="form-control" name="sub_topic_title[]" autocomplete="off" required>
                            </div>
                            <div class="col-md-5 mt-3">
                                <label class="form-label fw-semibold">Upload Module</label>
                                <div class="custom-file-group">
                                    <input type="text" class="form-control custom-file-label" placeholder="No file chosen" readonly>
                                    <label class="custom-file-btn mb-0">
                                        Choose File
                                        <input type="file" class="custom-file-input" name="sub_topic_file[]" accept=".pdf">
                                    </label>
                                </div>
                                <div class="text-danger" style="font-size: 12px">*Please upload a file with .pdf extension.</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Number of Questions</label>
                            <input type="number" class="form-control" name="sub_topic_jumlah_jawaban[]" min="1" required placeholder="0">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-sm fw-bold btn-add-subtopic-hover"
                            style="border: 1px solid #258eff; color: #258eff; background-color: white; border-radius: 0.5rem;"
                            id="add-subtopic-btn">
                            <i class="fas fa-plus"></i> Add Sub-Topics
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Topic & Sub-Topic -->
<div class="modal fade" id="editTopicModal" tabindex="-1" aria-labelledby="editTopicModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="edit-topic-form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="edit_topic_id" name="topic_id">
            <div class="modal-content" style="border-radius: 1rem;">
                <div class="modal-header text-white" style="background-color: #258eff; border-radius: 0.9rem 0.9rem 0 0;">
                    <h5 class="modal-title" id="editTopicModalLabel">Edit Topic & Sub-Topic</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(100%);"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label for="edit_topic_title" class="form-label fw-bold">Topic</label>
                            <input type="text" class="form-control" id="edit_topic_title" name="topic_title" required>
                        </div>
                        <div class="col-md-6" style="max-width: 180px;">
                            <label for="edit_countdown_minutes" class="form-label fw-bold">Timer (minutes)</label>
                            <input style="border: 1px solid #fa6767; width: 100px; background-color: #fce3e3;" type="number" class="form-control fw-semibold" id="edit_countdown_minutes" name="countdown_minutes" min="1" step="1" style="width: 100px;" placeholder="-">
                            <small class="form-text text-muted">Ex : 60 minutes.</small>
                        </div>
                    </div>
                    <div id="edit-subtopics-container">
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-sm fw-bold btn-add-subtopic-hover"
                            style="border: 1px solid #258eff; color: #258eff; background-color: white; border-radius: 0.5rem;"
                            id="add-edit-subtopic-btn">
                            <i class="fas fa-plus"></i> Add Sub-Topics
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>


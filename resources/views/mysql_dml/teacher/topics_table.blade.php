<div>
    {{-- Judul Topics List --}}
    <div class="mb-0 d-flex justify-content-between align-items-center">
        <h4 class="mb-3 fw-bold">Topics Management</h4>
    </div>
    {{-- Tombol Add Topic di bawah judul, tetap di kanan --}}
    <div class="mb-3 d-flex justify-content-end align-items-center">
        <button class="btn btn-primary" id="add-topic-btn" data-bs-toggle="modal" data-bs-target="#addTopicModal">
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
    <div class="modal-dialog">
        <form method="POST" action="{{ route('teacher.topics.addTopicSubtopic') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTopicModalLabel">Add Topic & Sub-Topic</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="topicTitle" class="form-label fw-bold">Topic</label>
                        <input type="text" class="form-control" id="topicTitle" name="topic_title" autocomplete="off" required>
                    </div>
                    <div id="subtopics-container">
                        <div class="mb-3 subtopic-group">
                            <label class="form-label fw-bold">Sub-Topic</label>
                            <input type="text" class="form-control" name="sub_topic_title[]" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-success btn-sm" id="add-subtopic-btn">
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
    <div class="modal-dialog">
        <form id="edit-topic-form">
            @csrf
            <input type="hidden" id="edit_topic_id" name="topic_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTopicModalLabel">Edit Topic & Sub-Topic</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_topic_title" class="form-label fw-bold">Topic</label>
                        <input type="text" class="form-control" id="edit_topic_title" name="topic_title" required>
                    </div>
                    <div id="edit-subtopics-container"></div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-success btn-sm" id="add-edit-subtopic-btn">
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


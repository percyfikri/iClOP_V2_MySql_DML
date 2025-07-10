{{-- filepath: /d:/Semester 8 (Skripsi)/Skripsi/Project/iClOP_V2_MySql_DML/resources/views/mysql_dml/teacher/answer_key.blade.php --}}
<div>
    <div class="mb-0 d-flex justify-content-between align-items-center">
        <h4 class="mb-5 fw-bold">Answer Key Management</h4>
    </div>
    <div class="mb-3 d-flex gap-3 align-items-center">
        <div class="d-flex align-items-center">
            <button id="filterTopicBtn" class="btn btn-outline-primary filter-btn" data-bs-toggle="modal" data-bs-target="#filterTopicModal" type="button">
                <span class="filter-label" id="filterTopicLabel">Filter by Topic</span>
            </button>
            {{-- <span class="filter-clear d-none ms-2" id="clearTopic">&times;</span> --}}
        </div>
        <div class="d-flex align-items-center">
            <button id="filterSubtopicBtn" class="btn btn-outline-primary filter-btn" data-bs-toggle="modal" data-bs-target="#filterSubtopicModal" type="button">
                <span class="filter-label" id="filterSubtopicLabel">Filter by Subtopic</span>
            </button>
            {{-- <span class="filter-clear d-none ms-2" id="clearSubtopic">&times;</span> --}}
        </div>
        <button id="resetFilterBtn" class="btn btn-outline-secondary" style="border-radius: 18px; font-weight: 500;">
            Reset Filter
        </button>
    </div>
    <div class="card shadow-sm p-4 mb-4" style="border-radius: 18px;">
        <table class="table table-bordered table-hover mb-0">
            <thead class="table-primary">
                <tr class="text-center">
                    <th style="width: 200px;">Question</th>
                    <th>Answer Key</th>
                    <th>Expected Table</th>
                    <th style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody id="answerKeyTbody">
                {{-- Data akan di-render oleh JS --}}
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Filter Topic -->
<div class="modal fade" id="filterTopicModal" tabindex="-1" aria-labelledby="filterTopicModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="filterTopicForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="filterTopicModalLabel">Filter by Topic</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <select class="form-select" name="topic" id="filterTopicSelect">
          @foreach($topics as $topic)
            <option value="{{ $topic->id }}">{{ $topic->title }}</option>
          @endforeach
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Apply</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Filter Subtopic -->
<div class="modal fade" id="filterSubtopicModal" tabindex="-1" aria-labelledby="filterSubtopicModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="filterSubtopicForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="filterSubtopicModalLabel">Filter by Subtopic</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <select class="form-select" name="subtopic" id="filterSubtopicSelect">
          {{-- Akan diisi dinamis oleh JS --}}
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Apply</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Add/Edit Answer Key -->
<div class="modal fade" id="answerKeyModal" tabindex="-1" aria-labelledby="answerKeyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <form id="answerKeyForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="answerKeyModalLabel">Add/Edit Answer Key</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body row">
        <!-- Preview Modul Praktikum -->
        <div class="col-md-8 mb-3">
            <div id="modulePreview" style="border:1px solid #eee; border-radius:8px; padding:10px; min-height:120px; background:#f9f9f9;">
                <span class="text-muted">Loading module...</span>
            </div>
        </div>
        <!-- Form Input Answer Key -->
        <div class="col-md-4 mb-3">
            <div class="mb-3">
                <label class="form-label fw-bold">Subtopic</label>
                <input type="text" class="form-control" id="modalSubtopicTitle" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Question</label>
                <input type="text" class="form-control" id="modalQuestionNumber" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Answer Key (SQL Query)</label>
                <textarea class="form-control" name="expected_query" id="expectedQueryInput" rows="6" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Expected Table</label>
                <input type="text" class="form-control" name="expected_table" id="expectedTableInput">
            </div>
            {{-- <input type="hidden" name="answer_id" id="answerIdInput"> --}}
            <input type="hidden" name="topic_detail_id" id="topicDetailIdInput">
            <input type="hidden" name="answer_number" id="answerNumberInput">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Answer Key</button>
      </div>
    </form>
  </div>
</div>

<script>
window.allAnswerKeys = @json($answerKeys);
window.topics = @json($topics);
window.subtopics = @json($subtopics);

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function initAnswerKeyPage() {
    // Data dari backend
    const allAnswerKeys = window.allAnswerKeys;
    const topics = window.topics;
    const subtopics = window.subtopics;

    // State filter
    let filterState = {
        topic: topics.length > 0 ? topics[0].id : null,
        subtopic: null
    };

    // Inisialisasi subtopic default
    if (filterState.topic) {
        const relatedSubtopics = subtopics.filter(st => st.topic_id == filterState.topic);
        filterState.subtopic = relatedSubtopics.length > 0 ? relatedSubtopics[0].id : null;
    }

    // Render subtopic select options
    function renderSubtopicOptions(topicId) {
        const related = subtopics.filter(st => st.topic_id == topicId);
        let html = '';
        related.forEach(st => {
            html += `<option value="${st.id}">${st.title}</option>`;
        });
        $('#filterSubtopicSelect').html(html);
    }

    // Render table
    function renderTable() {
        // Temukan subtopic yang aktif
        let subtopic = subtopics.find(st => st.id == filterState.subtopic);
        let totalQuestion = subtopic ? subtopic.total_question : 0;
        let tbody = '';

        if (!subtopic || totalQuestion == 0) {
            tbody = `<tr><td colspan="4" class="text-center text-muted">No questions found for this subtopic.</td></tr>`;
        } else {
            for (let i = 1; i <= totalQuestion; i++) {
                let answer = allAnswerKeys.find(a =>
                    a.topic_detail_id == subtopic.id && a.answer_number == i
                );
                tbody += `<tr>
                    <td class="text-center">Question ${i}</td>
                    <td>${answer ? answer.expected_query : '<span class="text-muted fst-italic">No answer key</span>'}</td>
                    <td>${answer ? (answer.expected_table ?? '<span class="text-muted fst-italic">-</span>') : '<span class="text-muted fst-italic">No expected table</span>'}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-warning" title="${answer ? 'Edit' : 'Add'}"
                            onclick="editAnswerKey(${answer ? answer.id : 'null'}, ${subtopic.id}, ${i})">
                            <i class="fas fa-edit"></i>
                        </button>
                        ${answer ? `<button class="btn btn-sm btn-danger ms-1 delete-answer-key-btn" data-id="${answer.id}" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>` : ''}
                    </td>
                </tr>`;
            }
        }
        $('#answerKeyTbody').html(tbody);
    }

    // Update label filter
    function updateFilterLabels() {
        let topic = topics.find(t => t.id == filterState.topic);
        let subtopic = subtopics.find(st => st.id == filterState.subtopic);
        $('#filterTopicLabel').text(topic ? topic.title : 'Filter by Topic');
        $('#filterSubtopicLabel').text(subtopic ? subtopic.title : 'Filter by Subtopic');
    }

    renderSubtopicOptions(filterState.topic);
    $('#filterSubtopicSelect').val(filterState.subtopic);

    renderTable();
    updateFilterLabels();

        // Event handler
    $('#filterTopicForm').off('submit').on('submit', function(e) {
        e.preventDefault();
        filterState.topic = $('#filterTopicSelect').val();
        const related = subtopics.filter(st => st.topic_id == filterState.topic);
        filterState.subtopic = related.length > 0 ? related[0].id : null;
        renderSubtopicOptions(filterState.topic);
        $('#filterSubtopicSelect').val(filterState.subtopic);
        renderTable();
        updateFilterLabels();
        $('#filterTopicModal').modal('hide');
    });

    $('#filterSubtopicForm').off('submit').on('submit', function(e) {
        e.preventDefault();
        filterState.subtopic = $('#filterSubtopicSelect').val();
        renderTable();
        updateFilterLabels();
        $('#filterSubtopicModal').modal('hide');
    });

    $('#resetFilterBtn').off('click').on('click', function() {
        filterState.topic = topics.length > 0 ? topics[0].id : null;
        const related = subtopics.filter(st => st.topic_id == filterState.topic);
        filterState.subtopic = related.length > 0 ? related[0].id : null;
        renderSubtopicOptions(filterState.topic);
        $('#filterSubtopicSelect').val(filterState.subtopic);
        renderTable();
        updateFilterLabels();
    });

    $('#filterTopicSelect').off('change').on('change', function() {
        renderSubtopicOptions($(this).val());
    });
}

// Fungsi untuk membuka modal Add/Edit Answer Key
window.editAnswerKey = function(answerId, topicDetailId, answerNumber) {
    // Reset form
    $('#answerKeyForm')[0].reset();
    // $('#answerIdInput').val(answerId || '');
    $('#topicDetailIdInput').val(topicDetailId);
    $('#answerNumberInput').val(answerNumber);

    // Set judul modal
    $('#answerKeyModalLabel').text(answerId ? 'Edit Answer Key' : 'Add Answer Key');

    // Isi textarea jika edit
    if (answerNumber && topicDetailId) {
        const answer = window.allAnswerKeys.find(a =>
            a.topic_detail_id == topicDetailId && a.answer_number == answerNumber
        );
        $('#expectedQueryInput').val(answer ? answer.expected_query : '');
        $('#expectedTableInput').val(answer ? answer.expected_table ?? '' : '');
    } else {
        $('#expectedQueryInput').val('');
        $('#expectedTableInput').val('');
    }

    // Tampilkan preview modul (ambil dari subtopic terkait)
    const subtopic = window.subtopics.find(st => st.id == topicDetailId);
    $('#modalSubtopicTitle').val(subtopic ? subtopic.title : '-');
    $('#modalQuestionNumber').val(answerNumber ? 'Question ' + answerNumber : '-');
    if (subtopic && subtopic.file_path && subtopic.file_name) {
        $('#modulePreview').html(
            `<iframe src="/${subtopic.file_path}${subtopic.file_name}" style="width:100%;height:350px;border:none;border-radius:6px;"></iframe>`
        );
    } else if (subtopic && subtopic.title) {
        $('#modulePreview').html(`<div class="fw-bold">${subtopic.title}</div>`);
    } else {
        $('#modulePreview').html('<span class="text-muted">No module available.</span>');
    }

    // Tampilkan modal
    $('#answerKeyModal').modal('show');
};

// Handler submit form (AJAX, sesuaikan endpoint sesuai kebutuhan)
$('#answerKeyForm').off('submit').on('submit', function(e) {
    e.preventDefault();
    const formData = $(this).serialize();
    $.ajax({
        url: '/mysql/teacher/answer-key/save', // Ganti dengan route baru
        method: 'POST',
        data: formData,
        success: function(res) {
            $('#answerKeyModal').modal('hide');
            location.reload(); // Atau renderTable() jika ingin tanpa reload
        },
        error: function() {
            alert('Failed to save answer key.');
        }
    });
});

$(function() {
    initAnswerKeyPage();
});
</script>

<style>
    .filter-btn {
        border-radius: 18px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        position: relative;
    }
    .filter-label {
        display: inline-block;
        vertical-align: middle;
    }
    .filter-clear {
        pointer-events: auto;
        color: #000000;
        font-weight: bold;
        font-size: 1.1em;
        background: transparent;
        border: none;
        padding: 0 4px;
        line-height: 1;
        transition: color 0.2s, background 0.2s;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 22px;
    }
    .filter-btn.active {
        background: #2563eb !important;
        color: #fff !important;
        border-color: #2563eb !important;
    }
    .filter-btn.active .filter-clear {
        color: #fff;
    }
    .filter-clear:hover {
        color: #333333;
        background: #d6d6d686;
        border-radius: 50%;
        align-items: center;
    }
</style>
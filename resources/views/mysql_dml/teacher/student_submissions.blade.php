{{-- filepath: /d:/Semester 8 (Skripsi)/Skripsi/Project/iClOP_V2_MySql_DML/resources/views/mysql_dml/teacher/student_submissions.blade.php --}}
<div>
    {{-- Judul dan tombol export (jika ingin, bisa dihapus jika tidak perlu) --}}
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <h4 class="mb-3 fw-bold">Student Submissions</h4>
    </div>
    <div class="mb-3 d-flex justify-content-end align-items-center">
        <button id="exportAllExcelBtn" class="btn btn-success me-2">
            <i class="fas fa-file-excel"></i> Export Excel
        </button>
        <button id="exportAllPdfBtn" class="btn btn-danger">
            <i class="fas fa-file-pdf"></i> Export PDF
        </button>
    </div>
    <div class="mb-3 d-flex gap-2">
        <button id="filterTopicBtn" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filterTopicModal" style="border-radius: 18px; font-weight: 500;">
            Filter by Topic
        </button>
        <button id="filterUserBtn" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filterUserModal" style="border-radius: 18px; font-weight: 500;">
            Filter by Username
        </button>
        <button id="filterDateBtn" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filterDateModal" style="border-radius: 18px; font-weight: 500;">
            Filter by Date
        </button>
        <button id="resetFilterBtn" class="btn btn-outline-secondary" style="border-radius: 18px; font-weight: 500;">
            Reset Filter
        </button>
    </div>
    <div class="card shadow-sm p-4 mb-4" style="border-radius: 18px;">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0 align-middle">
                <thead class="table-primary">
                    <tr class="text-center align-middle">
                        <th style="width: 45px;">No</th>
                        <th>Username</th>
                        <th>Topic</th>
                        <th>Date</th>
                        <th>Wrong</th>
                        <th>Correct</th>
                        <th>Duration</th>
                        <th>Score</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($studentSubmissions as $index => $submission)
                        @php
                            $durasiDetik = $submission->Durasi ?? 0;
                            $jam = floor($durasiDetik / 3600);
                            $menit = floor(($durasiDetik % 3600) / 60);
                            $detik = $durasiDetik % 60;
                            $durasiFormat = sprintf('%02d:%02d:%02d', $jam, $menit, $detik);
                            $nilai = ($submission->TotalJawaban > 0) ? round(($submission->Benar / $submission->TotalJawaban) * 100, 2) : 0;
                        @endphp
                        <tr class="text-center">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $submission->UserName }}</td>
                            <td>{{ $submission->SubmissionTopic }}</td>
                            <td>{{ date('Y-m-d H:i', strtotime($submission->Time)) }}</td>
                            <td>{{ $submission->Salah }}</td>
                            <td>{{ $submission->Benar }}</td>
                            <td>{{ $submission->Durasi !== null ? $durasiFormat : '-' }}</td>
                            <td>{{ $nilai }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No submissions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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
          <option value="">-- Select Topic --</option>
          @foreach($topics as $topic)
            <option value="{{ $topic->title }}">{{ $topic->title }}</option>
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

<!-- Modal Filter Username -->
<div class="modal fade" id="filterUserModal" tabindex="-1" aria-labelledby="filterUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="filterUserForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="filterUserModalLabel">Filter by Username</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <select class="form-select" name="username" id="filterUserSelect">
          <option value="">-- Select Username --</option>
          @foreach(collect($studentSubmissions)->pluck('UserName')->unique() as $user)
            <option value="{{ $user }}">{{ $user }}</option>
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

<!-- Modal Filter Date -->
<div class="modal fade" id="filterDateModal" tabindex="-1" aria-labelledby="filterDateModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="filterDateForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="filterDateModalLabel">Filter by Date</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="date" class="form-control" name="date" id="filterDateInput">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Apply</button>
      </div>
    </form>
  </div>
</div>

<script>
    $(document).ready(function () {
        // Ambil data dari blade ke JS
        var allStudentSubmissions = @json($studentSubmissions);
        var filteredSubmissions = allStudentSubmissions.slice();

        // Render Table
        function renderTable(data) {
            let tbody = '';
            if (data.length === 0) {
                tbody = `<tr><td colspan="8" class="text-center text-muted">No submissions found.</td></tr>`;
            } else {
                data.forEach(function(sub, idx) {
                    let durasiDetik = sub.Durasi ?? 0;
                    let jam = Math.floor(durasiDetik / 3600);
                    let menit = Math.floor((durasiDetik % 3600) / 60);
                    let detik = durasiDetik % 60;
                    let durasiFormat = sub.Durasi !== null ? 
                        (('0'+jam).slice(-2) + ':' + ('0'+menit).slice(-2) + ':' + ('0'+detik).slice(-2)) : '-';
                    let nilai = (sub.TotalJawaban > 0) ? Math.round((sub.Benar / sub.TotalJawaban) * 100 * 100) / 100 : 0;
                    tbody += `<tr class="text-center">
                        <td>${idx + 1}</td>
                        <td>${sub.UserName}</td>
                        <td>${sub.SubmissionTopic}</td>
                        <td>${sub.Time ? sub.Time.substring(0,16).replace('T',' ') : '-'}</td>
                        <td>${sub.Salah}</td>
                        <td>${sub.Benar}</td>
                        <td>${durasiFormat}</td>
                        <td>${nilai}</td>
                    </tr>`;
                });
            }
            $('.table tbody').html(tbody);
        }

        // Helper untuk reset semua tombol filter ke non-active
        function resetFilterButtons() {
            $('#filterTopicBtn').removeClass('active');
            $('#filterUserBtn').removeClass('active');
            $('#filterDateBtn').removeClass('active');
        }

        // Initial render
        renderTable(filteredSubmissions);

        // Export Filtered to Excel
        $('#exportAllExcelBtn').on('click', function() {
            let csv = 'Name,Topic,Date,Wrong,Correct,Duration,Score\n';
            filteredSubmissions.forEach(function(sub) {
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
            a.download = 'filtered_student_submissions.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        });

        // Export Filtered to PDF
        $('#exportAllPdfBtn').on('click', function() {
            let html = '<h2>Student Submissions</h2><table border="1" cellpadding="5" cellspacing="0"><tr><th>Name</th><th>Topic</th><th>Date</th><th>Wrong</th><th>Correct</th><th>Duration</th><th>Score</th></tr>';
            filteredSubmissions.forEach(function(sub) {
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

        // Filter by Topic
        $('#filterTopicForm').on('submit', function(e) {
            e.preventDefault();
            let topic = $('#filterTopicSelect').val();
            filteredSubmissions = allStudentSubmissions.filter(function(sub) {
                return topic === '' || sub.SubmissionTopic === topic;
            });
            renderTable(filteredSubmissions);
            $('#filterTopicModal').modal('hide');
            resetFilterButtons();
            if (topic !== '') $('#filterTopicBtn').addClass('active');
        });

        // Filter by Username
        $('#filterUserForm').on('submit', function(e) {
            e.preventDefault();
            let username = $('#filterUserSelect').val();
            filteredSubmissions = allStudentSubmissions.filter(function(sub) {
                return username === '' || sub.UserName === username;
            });
            renderTable(filteredSubmissions);
            $('#filterUserModal').modal('hide');
            resetFilterButtons();
            if (username !== '') $('#filterUserBtn').addClass('active');
        });

        // Filter by Date
        $('#filterDateForm').on('submit', function(e) {
            e.preventDefault();
            let date = $('#filterDateInput').val();
            filteredSubmissions = allStudentSubmissions.filter(function(sub) {
                if (!date) return true;
                let subDate = sub.Time ? sub.Time.substring(0,10) : '';
                return subDate === date;
            });
            renderTable(filteredSubmissions);
            $('#filterDateModal').modal('hide');
            resetFilterButtons();
            if (date !== '') $('#filterDateBtn').addClass('active');
        });

        // Reset Filter
        $('#resetFilterBtn').on('click', function() {
            $('#filterTopicSelect').val('');
            $('#filterUserSelect').val('');
            $('#filterDateInput').val('');
            filteredSubmissions = allStudentSubmissions.slice();
            renderTable(allStudentSubmissions);
            resetFilterButtons();
        });
    });
</script>

<style>
    #exportAllExcelBtn {
        background-color: #e6f4ea !important;
        color: #198754 !important;
        border: 1.5px solid #198754 !important;
        border-radius: 10px !important;
        font-weight: 500;
    }
    #exportAllExcelBtn:hover, #exportAllExcelBtn:focus {
        background-color: #198754 !important;
        color: #fff !important;
    }
    #exportAllPdfBtn {
        background-color: #fdeaea !important;
        color: #dc3545 !important;
        border: 1.5px solid #dc3545 !important;
        border-radius: 10px !important;
        font-weight: 500;
    }
    #exportAllPdfBtn:hover, #exportAllPdfBtn:focus {
        background-color: #dc3545 !important;
        color: #fff !important;
    }
    #exportAllExcelBtn .fa-file-excel {
        color: #198754 !important;
    }
    #exportAllPdfBtn .fa-file-pdf {
        color: #dc3545 !important;
    }
    #exportAllExcelBtn:hover .fa-file-excel,
    #exportAllExcelBtn:focus .fa-file-excel,
    #exportAllPdfBtn:hover .fa-file-pdf,
    #exportAllPdfBtn:focus .fa-file-pdf {
        color: #fff !important;
    }
</style>
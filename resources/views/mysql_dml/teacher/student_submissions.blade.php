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

<script>
    $(document).ready(function () {
        // Ambil data dari blade ke JS
        var allStudentSubmissions = @json($studentSubmissions);

        // Export All to Excel
        $('#exportAllExcelBtn').on('click', function() {
            let csv = 'Name,Topic,Date,Wrong,Correct,Duration,Score\n';
            allStudentSubmissions.forEach(function(sub) {
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
            a.download = 'all_student_submissions.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        });

        // Export All to PDF (simple, pakai window.print)
        $('#exportAllPdfBtn').on('click', function() {
            let html = '<h2>All Student Submissions</h2><table border="1" cellpadding="5" cellspacing="0"><tr><th>Name</th><th>Topic</th><th>Date</th><th>Wrong</th><th>Correct</th><th>Duration</th><th>Score</th></tr>';
            allStudentSubmissions.forEach(function(sub) {
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
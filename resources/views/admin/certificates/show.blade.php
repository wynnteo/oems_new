@extends('layouts.master')

@section('title')
Certificate Details | Admin Panel
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header actions">
                    <h5 class="text-capitalize">Certificate Details</h5>
                    <div class="actions_item">
                        <a class="btn btn-darken" href="{{ route('certificates.index') }}" title="Back">
                            <i class="material-icons">arrow_back</i> Back
                        </a>
                        <a class="btn btn-info" href="{{ route('certificates.edit', $certificate) }}" title="Edit">
                            <i class="material-icons">edit</i> Edit
                        </a>
                        @if($certificate->status === 'generated')
                            <a class="btn btn-success" href="{{ route('certificates.download', $certificate) }}" title="Download Certificate">
                                <i class="material-icons">download</i> Download PDF
                            </a>
                            <button class="btn btn-warning" onclick="regenerateCertificate()" title="Regenerate PDF">
                                <i class="material-icons">refresh</i> Regenerate
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Certificate Status Alert -->
                    <div class="alert alert-{{ $certificate->status === 'generated' ? 'success' : ($certificate->status === 'revoked' ? 'danger' : 'warning') }} mb-4">
                        <div class="d-flex align-items-center">
                            <i class="material-icons me-2">
                                {{ $certificate->status === 'generated' ? 'check_circle' : ($certificate->status === 'revoked' ? 'cancel' : 'schedule') }}
                            </i>
                            <div>
                                <strong>Certificate Status: {{ ucfirst($certificate->status) }}</strong>
                                @if($certificate->status === 'generated')
                                    <br><small>Certificate is ready for download and verification</small>
                                @elseif($certificate->status === 'revoked')
                                    <br><small>This certificate has been revoked and is no longer valid</small>
                                @else
                                    <br><small>Certificate is pending generation</small>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Certificate Information -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="material-icons me-2">assignment</i>Certificate Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Certificate Number:</strong></td>
                                            <td>{{ $certificate->certificate_number }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Verification Code:</strong></td>
                                            <td>
                                                <code class="bg-light p-1 rounded">{{ $certificate->verification_code }}</code>
                                                <button class="btn btn-sm btn-outline-secondary ms-2" onclick="copyToClipboard('{{ $certificate->verification_code }}')">
                                                    <i class="material-icons" style="font-size: 16px;">content_copy</i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Issue Date:</strong></td>
                                            <td>{{ $certificate->issued_at->format('d M Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Completion Type:</strong></td>
                                            <td>
                                                <span class="badge bg-info">{{ ucwords(str_replace('_', ' ', $certificate->completion_type)) }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Score:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $certificate->score >= 85 ? 'success' : ($certificate->score >= 65 ? 'warning' : 'secondary') }}">
                                                    {{ $certificate->score }}%
                                                </span>
                                            </td>
                                        </tr>
                                        @if($certificate->distinction)
                                        <tr>
                                            <td><strong>Distinction:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $certificate->distinction === 'high_distinction' ? 'success' : ($certificate->distinction === 'distinction' ? 'warning' : 'info') }}">
                                                    {{ ucwords(str_replace('_', ' ', $certificate->distinction)) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td><strong>Created:</strong></td>
                                            <td>{{ $certificate->created_at->format('d M Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Last Updated:</strong></td>
                                            <td>{{ $certificate->updated_at->format('d M Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Student Information -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="material-icons me-2">person</i>Student Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Name:</strong></td>
                                            <td>{{ $certificate->student->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $certificate->student->email }}</td>
                                        </tr>
                                        @if($certificate->student->phone)
                                        <tr>
                                            <td><strong>Phone:</strong></td>
                                            <td>{{ $certificate->student->phone }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td><strong>Student ID:</strong></td>
                                            <td>{{ $certificate->student->student_id ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $certificate->student->status === 'active' ? 'success' : 'secondary' }}">
                                                    {{ ucfirst($certificate->student->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Enrolled:</strong></td>
                                            <td>{{ $certificate->student->created_at->format('d M Y') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Course Information -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="material-icons me-2">book</i>Course Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Course Title:</strong></td>
                                            <td>{{ $certificate->course->title }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Course Code:</strong></td>
                                            <td><code>{{ $certificate->course->course_code }}</code></td>
                                        </tr>
                                        @if($certificate->course->description)
                                        <tr>
                                            <td><strong>Description:</strong></td>
                                            <td>{{ Str::limit($certificate->course->description, 100) }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td><strong>Duration:</strong></td>
                                            <td>{{ $certificate->course->duration ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $certificate->course->is_active === 'active' ? 'success' : 'secondary' }}">
                                                    {{ ucfirst($certificate->course->is_active) }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Exam Information (if applicable) -->
                        @if($certificate->exam)
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="material-icons me-2">quiz</i>Exam Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Exam Title:</strong></td>
                                            <td>{{ $certificate->exam->title }}</td>
                                        </tr>
                                        @if($certificate->exam->description)
                                        <tr>
                                            <td><strong>Description:</strong></td>
                                            <td>{{ Str::limit($certificate->exam->description, 100) }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td><strong>Duration:</strong></td>
                                            <td>{{ $certificate->exam->duration ?? 'N/A' }} minutes</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Passing Score:</strong></td>
                                            <td>{{ $certificate->exam->passing_score ?? 50 }}%</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Questions:</strong></td>
                                            <td>{{ $certificate->exam->total_questions ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Exam Date:</strong></td>
                                            <td>{{ $certificate->exam->start_time ? $certificate->exam->start_time->format('d M Y H:i') : 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Additional Notes -->
                        @if($certificate->notes)
                        <div class="col-12">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="material-icons me-2">note</i>Additional Notes</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $certificate->notes }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Certificate Preview -->
                        @if($certificate->status === 'generated' && $certificate->file_path)
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="material-icons me-2">picture_as_pdf</i>Certificate Preview</h6>
                                </div>
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="material-icons text-danger" style="font-size: 48px;">picture_as_pdf</i>
                                        <h5>{{ $certificate->certificate_number }}.pdf</h5>
                                        <p class="text-muted">Certificate file is ready for download</p>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('certificates.download', $certificate) }}" class="btn btn-success">
                                            <i class="material-icons me-2">download</i>Download Certificate
                                        </a>
                                        <button class="btn btn-info" onclick="previewCertificate()">
                                            <i class="material-icons me-2">visibility</i>Preview
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Status Actions -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="material-icons me-2">settings</i>Certificate Actions</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label><strong>Change Status:</strong></label>
                                                <select id="status-select" class="form-control">
                                                    <option value="pending" {{ $certificate->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="generated" {{ $certificate->status === 'generated' ? 'selected' : '' }}>Generated</option>
                                                    <option value="revoked" {{ $certificate->status === 'revoked' ? 'selected' : '' }}>Revoked</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 d-flex align-items-end">
                                            <button class="btn btn-primary me-2" onclick="updateStatus()">
                                                <i class="material-icons me-2">update</i>Update Status
                                            </button>
                                            <button class="btn btn-danger" onclick="deleteCertificate()">
                                                <i class="material-icons me-2">delete</i>Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Certificate Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <iframe id="pdfPreview" width="100%" height="500px" frameborder="0"></iframe>
            </div>
            <div class="modal-footer">
                <a href="{{ route('certificates.download', $certificate) }}" class="btn btn-success">
                    <i class="material-icons me-2">download</i>Download
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this certificate? This action cannot be undone.</p>
                <div class="alert alert-warning">
                    <strong>Warning:</strong> This will permanently delete the certificate and its associated PDF file.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('certificates.destroy', $certificate) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Certificate</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Copy verification code to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success message
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
        Toast.fire({
            icon: 'success',
            title: 'Verification code copied to clipboard!'
        });
    }).catch(function() {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('Verification code copied to clipboard!');
    });
}

// Preview certificate
function previewCertificate() {
    @if($certificate->status === 'generated' && $certificate->file_path)
        const pdfUrl = '{{ asset("storage/" . $certificate->file_path) }}';
        document.getElementById('pdfPreview').src = pdfUrl;
        $('#previewModal').modal('show');
    @else
        alert('Certificate PDF is not available for preview.');
    @endif
}

// Update certificate status
function updateStatus() {
    const newStatus = document.getElementById('status-select').value;
    const currentStatus = '{{ $certificate->status }}';
    
    if (newStatus === currentStatus) {
        alert('Status is already set to ' + newStatus);
        return;
    }
    
    if (confirm('Are you sure you want to change the certificate status to ' + newStatus + '?')) {
        $.ajax({
            url: '{{ route("certificates.toggle-status", $certificate) }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: newStatus
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error updating status: ' + response.message);
                }
            },
            error: function() {
                alert('Error updating certificate status');
            }
        });
    }
}

// Delete certificate
function deleteCertificate() {
    $('#deleteModal').modal('show');
}

// Regenerate certificate
function regenerateCertificate() {
    if (confirm('Are you sure you want to regenerate the certificate PDF? This will replace the existing file.')) {
        window.location.href = '{{ route("certificates.regenerate", $certificate) }}';
    }
}

// Initialize tooltips
$(document).ready(function() {
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
@endsect
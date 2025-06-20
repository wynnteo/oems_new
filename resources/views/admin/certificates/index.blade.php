@extends('layouts.master')

@section('title')
Certificates | Admin Panel
@endsection

@section('content')
<!-- Delete Modal -->
<div class="modal fade" id="deletemodal" tabindex="-1" aria-labelledby="deleteModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Delete Certificate</h5>
                <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="certificate_delete_modal" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="text-center">
                        <i class="material-icons text-warning mb-3" style="font-size: 48px;">warning</i>
                        <h6 class="mb-3">Are you sure you want to delete this certificate?</h6>
                        <p class="text-muted small">This action cannot be undone. The certificate file will also be deleted from storage.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Certificate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Actions Modal -->
<div class="modal fade" id="bulkActionModal" tabindex="-1" aria-labelledby="bulkActionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkActionModalLabel">Bulk Actions</h5>
                <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="bulkAction">Select Action:</label>
                    <select id="bulkAction" class="form-control">
                        <option value="">Choose an action...</option>
                        <option value="generate">Generate Selected Certificates</option>
                        <option value="revoke">Revoke Selected Certificates</option>
                        <option value="regenerate">Regenerate Selected Certificates</option>
                        <option value="delete">Delete Selected Certificates</option>
                    </select>
                </div>
                <div id="selectedCount" class="text-muted small mt-2"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="executeBulkAction" class="btn btn-primary">Execute</button>
            </div>
        </div>
    </div>
</div>

<!-- Verification Modal -->
<div class="modal fade" id="verificationModal" tabindex="-1" aria-labelledby="verificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verificationModalLabel">Verify Certificate</h5>
                <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('certificates.verify') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="verification_code">Verification Code:</label>
                        <input type="text" class="form-control" id="verification_code" name="verification_code" 
                               placeholder="Enter 8-character verification code" maxlength="8" required>
                        <small class="text-muted">Enter the verification code to validate a certificate</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Verify Certificate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Certificates</p>
                                <h5 class="font-weight-bolder mb-0">{{ $certificates->count() }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                <i class="material-icons opacity-10">card_membership</i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Generated</p>
                                <h5 class="font-weight-bolder mb-0">{{ $certificates->where('status', 'generated')->count() }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                <i class="material-icons opacity-10">check_circle</i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Pending</p>
                                <h5 class="font-weight-bolder mb-0">{{ $certificates->where('status', 'pending')->count() }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="material-icons opacity-10">pending</i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Revoked</p>
                                <h5 class="font-weight-bolder mb-0">{{ $certificates->where('status', 'revoked')->count() }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-danger shadow text-center border-radius-md">
                                <i class="material-icons opacity-10">block</i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header actions">
                    <h5 class="text-capitalize"><i class="material-icons opacity-10">card_membership</i> Certificates Management</h5>
                    <div class="actions_item">
                        <a class="btn btn-darken" href="{{ route('certificates.create') }}" title="Issue New Certificate">
                            <i class="material-icons">add</i> New Certificate
                        </a>
                        <button type="button" class="btn btn-info" id="verifyBtn">
                            <i class="material-icons">verified</i> Verify Certificate
                        </button>
                        <button type="button" class="btn btn-info" id="bulkActionsBtn" disabled>
                            <i class="material-icons">checklist</i> Bulk Actions
                        </button>
                        <div class="btn-group">
                            <button type="button" class="btn btn-light dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="material-icons">download</i> Export
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <li><a class="dropdown-item dropdown-item-tools border-radius-md" href="#" data-action="csv">
                                    <i class="material-icons">description</i> CSV</a></li>
                                <li><a class="dropdown-item dropdown-item-tools" href="#" data-action="excel">
                                    <i class="material-icons">table_chart</i> Excel</a></li>
                                <li><a class="dropdown-item dropdown-item-tools" href="#" data-action="pdf">
                                    <i class="material-icons">picture_as_pdf</i> PDF</a></li>
                                <li><a class="dropdown-item dropdown-item-tools" href="#" data-action="print">
                                    <i class="material-icons">print</i> Print</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible text-white mx-4">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <span><i class="material-icons">check_circle</i> {{ $message }}</span>
                    </div>
                    @endif

                    @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible text-white mx-4">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <span><i class="material-icons">error</i> {{ $errors->first() }}</span>
                    </div>
                    @endif

                    <!-- Filters -->
                    <div class="row mx-4 mb-3">
                        <div class="col-md-3">
                            <select id="statusFilter" class="form-control form-control-sm">
                                <option value="">All Status</option>
                                <option value="generated">Generated</option>
                                <option value="pending">Pending</option>
                                <option value="revoked">Revoked</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" id="dateFromFilter" class="form-control form-control-sm" placeholder="From Date">
                        </div>
                        <div class="col-md-3">
                            <input type="date" id="dateToFilter" class="form-control form-control-sm" placeholder="To Date">
                        </div>
                        <div class="col-md-3">
                            <button type="button" id="clearFilters" class="btn btn-outline-secondary btn-sm">
                                <i class="material-icons">clear</i> Clear Filters
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table" id="certificatetable">
                            <thead>
                                <tr>
                                    <th class="not-export-col">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                        </div>
                                    </th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Certificate #</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Student Details</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Course</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Exam</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Score</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Status</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Issued Date</th>
                                    <th class="not-export-col text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($certificates as $row)
                                <tr id="certificate-{{ $row->id }}" data-status="{{ $row->status }}" data-issued="{{ $row->issued_at }}">
                                    <td class="not-export-col">
                                        <div class="form-check">
                                            <input class="form-check-input row-checkbox" type="checkbox" value="{{ $row->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="text-sm font-weight-bold mb-0">{{ $row->certificate_number }}</h6>
                                            <p class="text-xs text-secondary mb-0">
                                                <span class="badge badge-sm bg-gradient-info">{{ $row->verification_code }}</span>
                                            </p>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="text-sm font-weight-bold mb-0">{{ $row->student->name ?? 'N/A' }}</h6>
                                            <p class="text-xs text-secondary mb-0">{{ $row->student->email ?? 'N/A' }}</p>
                                            @if($row->student->student_id)
                                                <small class="text-muted">ID: {{ $row->student->student_id }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="text-sm font-weight-bold mb-0">{{ $row->course->title ?? 'N/A' }}</h6>
                                            @if($row->course->course_code)
                                                <p class="text-xs text-secondary mb-0">{{ $row->course->course_code }}</p>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-xs">{{ $row->exam->title ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="text-sm font-weight-bold">
                                            @if($row->score >= 80)
                                                <span class="text-success">{{ $row->score }}%</span>
                                            @elseif($row->score >= 60)
                                                <span class="text-warning">{{ $row->score }}%</span>
                                            @else
                                                <span class="text-danger">{{ $row->score }}%</span>
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        @if($row->status == 'generated')
                                            <span class="status-badge badge badge-sm bg-gradient-success">Generated</span>
                                        @elseif($row->status == 'pending')
                                            <span class="status-badge badge badge-sm bg-gradient-warning">Pending</span>
                                        @else
                                            <span class="status-badge badge badge-sm bg-gradient-danger">Revoked</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-sm">{{ \Carbon\Carbon::parse($row->issued_at)->format('M d, Y') }}</span>
                                        <br><small class="text-muted">{{ \Carbon\Carbon::parse($row->issued_at)->format('h:i A') }}</small>
                                    </td>
                                    <td class="not-export-col">
                                        <div class="dropdown float-lg-end pe-4" id="certificate-{{ $row->id }}-dropdown">
                                            <a class="cursor-pointer" id="dropdownTable" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v text-secondary"></i>
                                            </a>
                                            <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5" aria-labelledby="dropdownTable">
                                                <li><a class="dropdown-item border-radius-md" href="{{ route('certificates.show', $row->id) }}">
                                                    <i class="material-icons">remove_red_eye</i> View Details</a></li>
                                                @if($row->status == 'generated' && $row->file_path)
                                                    <li><a class="dropdown-item border-radius-md" href="{{ route('certificates.download', $row->id) }}">
                                                        <i class="material-icons">download</i> Download PDF</a></li>
                                                @endif
                                                <li><a class="dropdown-item border-radius-md" href="{{ route('certificates.edit', $row->id) }}">
                                                    <i class="material-icons">edit</i> Edit Certificate</a></li>
                                                @if($row->status == 'pending')
                                                    <li><a class="dropdown-item border-radius-md" href="#" onclick="toggleStatus({{ $row->id }}, 'generated')">
                                                        <i class="material-icons">check_circle</i> Generate</a></li>
                                                @elseif($row->status == 'generated')
                                                    <li><a class="dropdown-item border-radius-md" href="#" onclick="toggleStatus({{ $row->id }}, 'revoked')">
                                                        <i class="material-icons">block</i> Revoke</a></li>
                                                    <li><a class="dropdown-item border-radius-md" href="{{ route('certificates.regenerate', $row->id) }}">
                                                        <i class="material-icons">refresh</i> Regenerate PDF</a></li>
                                                @elseif($row->status == 'revoked')
                                                    <li><a class="dropdown-item border-radius-md" href="#" onclick="toggleStatus({{ $row->id }}, 'generated')">
                                                        <i class="material-icons">restore</i> Restore</a></li>
                                                @endif
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="deletebtn dropdown-item border-radius-md text-danger" href="#" 
                                                    data-action="{{ route('certificates.destroy', $row->id) }}">
                                                    <i class="material-icons">delete</i> Delete Certificate</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    var table = $('#certificatetable').DataTable({
        columnDefs: [{
            orderable: false,
            targets: [0, -1] // First and last columns not orderable
        }, {
            targets: 0, width: '50px'
        }, {
            targets: 1, width: '150px'
        }, {
            targets: 2, width: '200px'
        }],
        order: [[7, 'desc']], // Order by issued date descending
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        pageLength: 25,
        layout: {
            top1Start: {
                buttons: [{
                    text: 'CSV', extend: 'csvHtml5',
                    exportOptions: { columns: ':visible:not(.not-export-col)' }
                }, {
                    text: 'Excel', extend: 'excelHtml5',
                    exportOptions: { columns: ':visible:not(.not-export-col)' }
                }, {
                    text: 'PDF', extend: 'pdfHtml5',
                    pageSize: 'A4',
                    exportOptions: { columns: ':visible:not(.not-export-col)' },
                    customize: function(doc) {
                        doc.title = 'Certificates Report | OEMS';
                        doc.styles.title = { fontSize: 14, bold: true, color: 'black', alignment: 'center' };
                        doc.content.forEach(function (item) {
                            if (item.table) {
                                item.table.widths = Array(item.table.body[0].length).fill('*');
                            }
                        });
                    }
                }, {
                    text: 'Print', extend: 'print',
                    exportOptions: { columns: ':visible:not(.not-export-col)' }
                }]
            }
        }
    });

    // Filters
    $('#statusFilter, #dateFromFilter, #dateToFilter').on('change', function() {
        var status = $('#statusFilter').val();
        var dateFrom = $('#dateFromFilter').val();
        var dateTo = $('#dateToFilter').val();
        
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var row = $(table.row(dataIndex).node());
            var rowStatus = row.data('status');
            var rowDate = row.data('issued');
            
            var statusMatch = !status || rowStatus === status;
            
            var dateMatch = true;
            if (dateFrom || dateTo) {
                var issuedDate = new Date(rowDate);
                if (dateFrom) {
                    dateMatch = dateMatch && issuedDate >= new Date(dateFrom);
                }
                if (dateTo) {
                    dateMatch = dateMatch && issuedDate <= new Date(dateTo + ' 23:59:59');
                }
            }
            
            return statusMatch && dateMatch;
        });
        
        table.draw();
        $.fn.dataTable.ext.search.pop();
    });

    $('#clearFilters').on('click', function() {
        $('#statusFilter, #dateFromFilter, #dateToFilter').val('');
        table.draw();
    });

    // Select all functionality
    $('#selectAll').on('change', function() {
        $('.row-checkbox').prop('checked', this.checked);
        updateBulkActionsButton();
    });

    $('.row-checkbox').on('change', function() {
        updateBulkActionsButton();
        $('#selectAll').prop('checked', $('.row-checkbox:checked').length === $('.row-checkbox').length);
    });

    function updateBulkActionsButton() {
        var selectedCount = $('.row-checkbox:checked').length;
        $('#bulkActionsBtn').prop('disabled', selectedCount === 0);
        if (selectedCount > 0) {
            $('#bulkActionsBtn').text('Bulk Actions (' + selectedCount + ')');
        } else {
            $('#bulkActionsBtn').text('Bulk Actions');
        }
    }

    // Bulk actions
    $('#bulkActionsBtn').on('click', function() {
        var selectedCount = $('.row-checkbox:checked').length;
        $('#selectedCount').text(selectedCount + ' certificates selected');
        $('#bulkActionModal').modal('show');
    });

    // Verify certificate button
    $('#verifyBtn').on('click', function() {
        $('#verificationModal').modal('show');
    });

    // Delete modal
    $('#certificatetable').on('click', '.deletebtn', function () {
        var action = $(this).attr("data-action");
        $('#certificate_delete_modal').attr('action', action);
        $('#deletemodal').modal('show');
    });

    // Export buttons
    $('.dropdown-item-tools').on('click', function (e) {
        e.preventDefault();
        var action = $(this).data('action');
        var buttonClass = '.buttons-' + action;
        table.button(buttonClass).trigger();
    });
});

function updateStatusUI(certificateId, newStatus) {
    const dropdown = $(`#certificate-${certificateId}-dropdown`);
    const statusBadge = $(`#certificate-${certificateId}`).find('.status-badge');
    
    // Update status badge
    statusBadge.removeClass('bg-gradient-success bg-gradient-warning bg-gradient-danger');
    
    if (newStatus === 'generated') {
        statusBadge.addClass('bg-gradient-success').text('Generated');
    } else if (newStatus === 'pending') {
        statusBadge.addClass('bg-gradient-warning').text('Pending');
    } else if (newStatus === 'revoked') {
        statusBadge.addClass('bg-gradient-danger').text('Revoked');
    }
    
    // Update dropdown actions based on new status
    const actions = dropdown.find('ul');
    actions.find('[onclick*="toggleStatus"]').remove();
    actions.find('[href*="regenerate"]').remove();
    
    const editItem = actions.find('[href*="edit"]').parent();
    
    if (newStatus === 'pending') {
        editItem.after(`
            <li><a class="dropdown-item border-radius-md" href="#" onclick="toggleStatus(${certificateId}, 'generated')">
                <i class="material-icons">check_circle</i> Generate</a></li>
        `);
    } else if (newStatus === 'generated') {
        editItem.after(`
            <li><a class="dropdown-item border-radius-md" href="#" onclick="toggleStatus(${certificateId}, 'revoked')">
                <i class="material-icons">block</i> Revoke</a></li>
            <li><a class="dropdown-item border-radius-md" href="/admin/certificates/${certificateId}/regenerate">
                <i class="material-icons">refresh</i> Regenerate PDF</a></li>
        `);
    } else if (newStatus === 'revoked') {
        editItem.after(`
            <li><a class="dropdown-item border-radius-md" href="#" onclick="toggleStatus(${certificateId}, 'generated')">
                <i class="material-icons">restore</i> Restore</a></li>
        `);
    }
}

function toggleStatus(certificateId, newStatus) {
    const token = $('meta[name="csrf-token"]').attr('content');
    
    if (!token) {
        console.error('CSRF token not found');
        return;
    }
    
    $.ajax({
        url: `/admin/certificates/${certificateId}/toggle-status`,
        type: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': token
        },
        data: {
            status: newStatus,
        },
        beforeSend: function() {
            $(`#certificate-${certificateId}`).addClass('opacity-50');
        },
        success: function(response) {
            if (response.success) {
                updateStatusUI(certificateId, newStatus);
                // Show success message
                if (newStatus === 'generated') {
                    alert('Certificate generated successfully!');
                } else if (newStatus === 'revoked') {
                    alert('Certificate revoked successfully!');
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('Error toggling status:', error);
            alert('Failed to update certificate status. Please try again.');
        },
        complete: function() {
            $(`#certificate-${certificateId}`).removeClass('opacity-50');
        }
    });
}
</script>
@endsection
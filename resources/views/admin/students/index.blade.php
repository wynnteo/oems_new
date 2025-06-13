@extends('layouts.master')

@section('title')
Students | Admin Panel
@endsection

@section('content')
<!-- Delete Modal -->
<div class="modal fade" id="deletemodal" tabindex="-1" aria-labelledby="deleteModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Delete Student</h5>
                <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="student_delete_modal" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="text-center">
                        <i class="material-icons text-warning mb-3" style="font-size: 48px;">warning</i>
                        <h6 class="mb-3">Are you sure you want to delete this student?</h6>
                        <p class="text-muted small">This action cannot be undone. All related enrollments and exam results will also be affected.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Student</button>
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
                        <option value="activate">Activate Selected Students</option>
                        <option value="deactivate">Deactivate Selected Students</option>
                        <option value="delete">Delete Selected Students</option>
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

<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Students</p>
                                <h5 class="font-weight-bolder mb-0">{{ $students->count() }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                <i class="material-icons opacity-10">people</i>
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Active Students</p>
                                <h5 class="font-weight-bolder mb-0">{{ $students->where('status', 'active')->count() }}</h5>
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Enrollments</p>
                                <h5 class="font-weight-bolder mb-0">{{ $students->sum(function($student) { return $student->enrollments->count(); }) }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="material-icons opacity-10">school</i>
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">New This Month</p>
                                <h5 class="font-weight-bolder mb-0">{{ $students->where('created_at', '>=', now()->startOfMonth())->count() }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="material-icons opacity-10">trending_up</i>
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
                    <h5 class="text-capitalize"><i class="material-icons opacity-10">people</i> Students Management</h5>
                    <div class="actions_item">
                        <a class="btn btn-darken" href="{{ route('students.create') }}" title="Add New Student">
                            <i class="material-icons">add</i> New Student
                        </a>
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

                    <!-- Filters -->
                    <div class="row mx-4 mb-3">
                        <div class="col-md-3">
                            <select id="statusFilter" class="form-control form-control-sm">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="genderFilter" class="form-control form-control-sm">
                                <option value="">All Genders</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" id="searchFilter" class="form-control form-control-sm" placeholder="Search by name or email...">
                        </div>
                        <div class="col-md-3">
                            <button type="button" id="clearFilters" class="btn btn-outline-secondary btn-sm">
                                <i class="material-icons">clear</i> Clear Filters
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table" id="studenttable">
                            <thead>
                                <tr>
                                    <th class="not-export-col">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                        </div>
                                    </th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Student ID</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Student Details</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Contact Info</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Gender</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Status</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Enrollments</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Joined Date</th>
                                    <th class="not-export-col text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($students as $row)
                                <tr id="student-{{ $row->id }}" data-status="{{ $row->status }}" data-gender="{{ $row->gender }}">
                                    <td class="not-export-col">
                                        <div class="form-check">
                                            <input class="form-check-input row-checkbox" type="checkbox" value="{{ $row->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-sm bg-gradient-info">{{ $row->student_code }}</span>
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="text-sm font-weight-bold mb-0">{{ $row->name }}</h6>
                                            @if($row->date_of_birth)
                                                <p class="text-xs text-secondary mb-0">
                                                    Age: {{ \Carbon\Carbon::parse($row->date_of_birth)->age }} years
                                                </p>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <p class="text-xs mb-0">{{ $row->email }}</p>
                                            @if($row->phone_number)
                                                <p class="text-xs text-secondary mb-0">{{ $row->phone_number }}</p>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-sm bg-gradient-secondary">{{ ucfirst($row->gender) }}</span>
                                    </td>
                                    <td>
                                        @if($row->status == 'active')
                                            <span class="status-badge badge badge-sm bg-gradient-success">Active</span>
                                        @elseif($row->status == 'inactive')
                                            <span class="status-badge badge badge-sm bg-gradient-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-sm">{{ $row->enrollments->count() ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <span class="text-xs">{{ $row->created_at->format('M d, Y') }}</span>
                                    </td>
                                    <td class="not-export-col">
                                        <div class="dropdown float-lg-end pe-4" id="student-{{ $row->id }}-dropdown">
                                            <a class="cursor-pointer" id="dropdownTable" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v text-secondary"></i>
                                            </a>
                                            <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5" aria-labelledby="dropdownTable">
                                                <li><a class="dropdown-item border-radius-md" href="{{ route('students.show', $row->id) }}">
                                                    <i class="material-icons">remove_red_eye</i> View Details</a></li>
                                                <li><a class="dropdown-item border-radius-md" href="{{ route('students.edit', $row->id) }}">
                                                    <i class="material-icons">edit</i> Edit Student</a></li>
                                                @if($row->status != 'active')
                                                    <li><a class="dropdown-item border-radius-md" href="#" onclick="toggleStatus({{ $row->id }}, 'active')">
                                                        <i class="material-icons">check_circle</i> Activate</a></li>
                                                @else
                                                    <li><a class="dropdown-item border-radius-md" href="#" onclick="toggleStatus({{ $row->id }}, 'inactive')">
                                                        <i class="material-icons">pause_circle</i> Deactivate</a></li>
                                                @endif
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="deletebtn dropdown-item border-radius-md text-danger" href="#" 
                                                    data-action="{{ route('students.destroy', $row->id) }}">
                                                    <i class="material-icons">delete</i> Delete Student</a></li>
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
    var table = $('#studenttable').DataTable({
        columnDefs: [{
            orderable: false,
            targets: [0, -1] // First and last columns not orderable
        }, {
            targets: 0, width: '50px'
        }, {
            targets: 1, width: '120px'
        }, {
            targets: 2, width: '200px'
        }],
        order: [[1, 'asc']], // Order by student ID
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
                        doc.title = 'Students Report | OEMS';
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
    $('#statusFilter, #genderFilter').on('change', function() {
        var status = $('#statusFilter').val();
        var gender = $('#genderFilter').val();
        
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var row = $(table.row(dataIndex).node());
            var rowStatus = row.data('status');
            var rowGender = row.data('gender');
            
            var statusMatch = !status || rowStatus === status;
            var genderMatch = !gender || rowGender === gender;
            
            return statusMatch && genderMatch;
        });
        
        table.draw();
        $.fn.dataTable.ext.search.pop();
    });

    // Search filter
    $('#searchFilter').on('keyup', function() {
        table.search(this.value).draw();
    });

    $('#clearFilters').on('click', function() {
        $('#statusFilter, #genderFilter').val('');
        $('#searchFilter').val('');
        table.search('').draw();
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
        $('#selectedCount').text(selectedCount + ' students selected');
        $('#bulkActionModal').modal('show');
    });

    // Delete modal
    $('#studenttable').on('click', '.deletebtn', function () {
        var action = $(this).attr("data-action");
        $('#student_delete_modal').attr('action', action);
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

function updateStatusUI(studentId, newStatus) {
    const dropdown = $(`#student-${studentId}-dropdown`);
    const statusBadge = $(`#student-${studentId}`).find('.status-badge');
    
    // Update status badge
    statusBadge.removeClass('bg-gradient-success bg-gradient-danger bg-gradient-info bg-gradient-warning bg-gradient-secondary');
    
    switch(newStatus) {
        case 'active':
            statusBadge.addClass('bg-gradient-success').text('Active');
            break;
        case 'inactive':
            statusBadge.addClass('bg-gradient-danger').text('Inactive');
            break;
        default:
            statusBadge.addClass('bg-gradient-secondary').text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1));
    }
    
    // Update data attribute
    $(`#student-${studentId}`).attr('data-status', newStatus);
}

function toggleStatus(studentId, newStatus) {
    const token = $('meta[name="csrf-token"]').attr('content');
    
    if (!token) {
        console.error('CSRF token not found');
        return;
    }
    
    $.ajax({
        url: `/admin/students/${studentId}/toggle-status`,
        type: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': token
        },
        data: {
            status: newStatus,
        },
        beforeSend: function() {
            $(`#student-${studentId}`).addClass('opacity-50');
        },
        success: function(response) {
            if (response.success) {
                updateStatusUI(studentId, newStatus);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error toggling status:', error);
            alert('Failed to update student status. Please try again.');
        },
        complete: function() {
            $(`#student-${studentId}`).removeClass('opacity-50');
        }
    });
}
</script>
@endsection
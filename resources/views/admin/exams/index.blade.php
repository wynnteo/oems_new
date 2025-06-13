@extends('layouts.master')

@section('title')
Exams | Admin Panel
@endsection

@section('content')
<!-- Delete Modal -->
<div class="modal fade" id="deletemodal" tabindex="-1" aria-labelledby="deleteModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Delete Exam</h5>
                <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="exam_delete_modal" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="text-center">
                        <i class="material-icons text-warning mb-3" style="font-size: 48px;">warning</i>
                        <h6 class="mb-3">Are you sure you want to delete this exam?</h6>
                        <p class="text-muted small">This action cannot be undone. All related questions and submissions will also be affected.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Exam</button>
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
                        <option value="activate">Make Available</option>
                        <option value="deactivate">Make Unavailable</option>
                        <option value="delete">Delete Selected Exams</option>
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Exams</p>
                                <h5 class="font-weight-bolder mb-0">{{ $exams->count() }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                <i class="material-icons opacity-10">assignment</i>
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Available Exams</p>
                                <h5 class="font-weight-bolder mb-0">{{ $exams->where('status', 'available')->count() }}</h5>
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Upcoming Exams</p>
                                <h5 class="font-weight-bolder mb-0">{{ $exams->where('status', 'available')->where('start_time', '>', now())->count() }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="material-icons opacity-10">schedule</i>
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Completed Exams</p>
                                <h5 class="font-weight-bolder mb-0">{{ $exams->where('start_time', '<', now())->count() }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="material-icons opacity-10">done_all</i>
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
                    <h5 class="text-capitalize"><i class="material-icons opacity-10">assignment</i> Exams Management</h5>
                    <div class="actions_item">
                        <a class="btn btn-darken" href="{{ route('exams.create') }}" title="Add New Exam">
                            <i class="material-icons">add</i> New Exam
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
                                <option value="available">Available</option>
                                <option value="unavailable">Unavailable</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="courseFilter" class="form-control form-control-sm">
                                <option value="">All Courses</option>
                                @foreach($exams->unique('course.course_code')->pluck('course') as $course)
                                    <option value="{{ $course->course_code }}">{{ $course->course_code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="timeFilter" class="form-control form-control-sm">
                                <option value="">All Times</option>
                                <option value="upcoming">Upcoming</option>
                                <option value="ongoing">Ongoing</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="button" id="clearFilters" class="btn btn-outline-secondary btn-sm">
                                <i class="material-icons">clear</i> Clear Filters
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table" id="examtable">
                            <thead>
                                <tr>
                                    <th class="not-export-col">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                        </div>
                                    </th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Course Code</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Exam Code</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Exam Details</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Schedule</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Duration</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Status</th>
                                    <!-- <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Submissions</th> -->
                                    <th class="not-export-col text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($exams as $row)
                                <tr id="exam-{{ $row->id }}" data-status="{{ $row->status }}" data-course="{{ $row->course->course_code }}" data-time="{{ $row->start_time }}">
                                    <td class="not-export-col">
                                        <div class="form-check">
                                            <input class="form-check-input row-checkbox" type="checkbox" value="{{ $row->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-sm bg-gradient-info">{{ $row->course->course_code }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-sm bg-gradient-dark">{{ $row->exam_code }}</span>
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="text-sm font-weight-bold mb-0">{{ $row->title }}</h6>
                                            @if($row->description)
                                                <p class="text-xs text-secondary mb-0">{{ Str::limit($row->description, 50) }}</p>
                                            @endif
                                            @if($row->course->title)
                                                <small class="text-muted">{{ $row->course->title }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <span class="text-xs font-weight-bold">{{ \Carbon\Carbon::parse($row->start_time)->format('M d, Y') }}</span><br>
                                            <span class="text-xs text-secondary">{{ \Carbon\Carbon::parse($row->start_time)->format('h:i A') }}</span>
                                            @if($row->end_time)
                                                <br><small class="text-muted">to {{ \Carbon\Carbon::parse($row->end_time)->format('h:i A') }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-sm">{{ $row->formatDuration() }}</span>
                                    </td>
                                    <td>
                                        @if($row->status == 'available')
                                            <span class="status-badge badge badge-sm bg-gradient-success">Available</span>
                                        @else
                                            <span class="status-badge badge badge-sm bg-gradient-danger">Unavailable</span>
                                        @endif
                                    </td>
    
                                    <td class="not-export-col">
                                        <div class="dropdown float-lg-end pe-4" id="exam-{{ $row->id }}-dropdown">
                                            <a class="cursor-pointer" id="dropdownTable" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v text-secondary"></i>
                                            </a>
                                            <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5" aria-labelledby="dropdownTable">
                                                <li><a class="dropdown-item border-radius-md" href="{{ route('exams.show', $row->id) }}">
                                                    <i class="material-icons">remove_red_eye</i> View Details</a></li>
                                                <li><a class="dropdown-item border-radius-md" href="{{ route('exams.edit', $row->id) }}">
                                                    <i class="material-icons">edit</i> Edit Exam</a></li>
                                                <li><a class="dropdown-item border-radius-md" href="{{ route('questions.create', $row->id) }}">
                                                    <i class="material-icons">question_answer</i> Add Question</a></li>
                                                @if($row->status != 'available')
                                                    <li><a class="dropdown-item border-radius-md" href="#" onclick="toggleStatus({{ $row->id }}, 'available')">
                                                        <i class="material-icons">check_circle</i> Make Available</a></li>
                                                @else
                                                    <li><a class="dropdown-item border-radius-md" href="#" onclick="toggleStatus({{ $row->id }}, 'not_available')">
                                                        <i class="material-icons">pause_circle</i> Make Unavailable</a></li>
                                                @endif
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="deletebtn dropdown-item border-radius-md text-danger" href="#" 
                                                    data-action="{{ route('exams.destroy', $row->id) }}">
                                                    <i class="material-icons">delete</i> Delete Exam</a></li>
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
    var table = $('#examtable').DataTable({
        columnDefs: [{
            orderable: false,
            targets: [0, -1] // First and last columns not orderable
        }, {
            targets: 0, width: '50px'
        }, {
            targets: 1, width: '120px'
        }, {
            targets: 2, width: '120px'
        }],
        order: [[2, 'asc']], // Order by exam code
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
                        doc.title = 'Exams Report | OEMS';
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
    $('#statusFilter, #courseFilter, #timeFilter').on('change', function() {
        var status = $('#statusFilter').val();
        var course = $('#courseFilter').val();
        var timeFilter = $('#timeFilter').val();
        
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var row = $(table.row(dataIndex).node());
            var rowStatus = row.data('status');
            var rowCourse = row.data('course');
            var rowTime = new Date(row.data('time'));
            var now = new Date();
            
            var statusMatch = !status || rowStatus === status;
            var courseMatch = !course || rowCourse === course;
            
            var timeMatch = true;
            if (timeFilter === 'upcoming') {
                timeMatch = rowTime > now;
            } else if (timeFilter === 'completed') {
                timeMatch = rowTime < now;
            } else if (timeFilter === 'ongoing') {
                // Assuming ongoing means within the exam duration
                timeMatch = Math.abs(rowTime - now) < (2 * 60 * 60 * 1000); // Within 2 hours
            }
            
            return statusMatch && courseMatch && timeMatch;
        });
        
        table.draw();
        $.fn.dataTable.ext.search.pop();
    });

    $('#clearFilters').on('click', function() {
        $('#statusFilter, #courseFilter, #timeFilter').val('');
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
        $('#selectedCount').text(selectedCount + ' exams selected');
        $('#bulkActionModal').modal('show');
    });

    // Delete modal
    $('#examtable').on('click', '.deletebtn', function () {
        var action = $(this).attr("data-action");
        $('#exam_delete_modal').attr('action', action);
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

function updateStatusUI(examId, newStatus) {
    const dropdown = $(`#exam-${examId}-dropdown`);
    const statusToggle = dropdown.find('[onclick*="toggleStatus"]');
    const statusBadge = $(`#exam-${examId}`).find('.status-badge');
    
    if (newStatus === 'available') {
        statusToggle.attr('onclick', `toggleStatus(${examId}, 'not_available')`);
        statusToggle.html('<i class="material-icons">pause_circle</i> Make Unavailable');

        statusBadge.removeClass('bg-gradient-danger')
                  .addClass('bg-gradient-success')
                  .text('Available');
    } else {
        statusToggle.attr('onclick', `toggleStatus(${examId}, 'available')`);
        statusToggle.html('<i class="material-icons">check_circle</i> Make Available');

        statusBadge.removeClass('bg-gradient-success')
                  .addClass('bg-gradient-danger')
                  .text('Unavailable');
    }
}

function toggleStatus(examId, newStatus) {
    const token = $('meta[name="csrf-token"]').attr('content');
    
    if (!token) {
        console.error('CSRF token not found');
        return;
    }
    
    $.ajax({
        url: `/admin/exams/${examId}/toggle-status`,
        type: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            status: newStatus,
        },
        beforeSend: function() {
            $(`#exam-${examId}`).addClass('opacity-50');
        },
        success: function(response) {
            if (response.success) {
                updateStatusUI(examId, newStatus);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error toggling status:', error);
            alert('Failed to update exam status. Please try again.');
        },
        complete: function() {
            $(`#exam-${examId}`).removeClass('opacity-50');
        }
    });
}
</script>
@endsection
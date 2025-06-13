@extends('layouts.master')

@section('title')
Questions Management | Admin Panel
@endsection

@section('content')
<!-- Delete Modal -->
<div class="modal fade" id="deletemodal" tabindex="-1" aria-labelledby="deleteModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Delete Question</h5>
                <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="question_delete_modal" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="text-center">
                        <i class="material-icons text-warning mb-3" style="font-size: 48px;">warning</i>
                        <h6 class="mb-3">Are you sure you want to delete this question?</h6>
                        <p class="text-muted small">This action cannot be undone. All related exam data will be affected.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Question</button>
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
                        <option value="activate">Activate Selected Questions</option>
                        <option value="deactivate">Deactivate Selected Questions</option>
                        <option value="duplicate">Duplicate Selected Questions</option>
                        <option value="delete">Delete Selected Questions</option>
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

<!-- Import Modal -->
<div class="modal fade" id="importmodal" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Questions</h5>
                <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="import_qns_modal" action="{{ route('questions.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <small><i class="material-icons">info</i> Please download the <a href="{{ asset('public/assets/templates/sample_excel.xlsx') }}" class="text-primary">import template</a>. Do not remove any columns.</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="exam_id" class="form-label"><strong>Exam</strong></label>
                            <select name="exam_id" id="exam_id" class="form-select" required>
                                <option value="">Select Exam</option>
                                @foreach ($exams as $exam)
                                    <option value="{{ $exam->id }}">{{ $exam->title }} ({{ $exam->exam_code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="file" class="form-label"><strong>File</strong></label>
                            <input type="file" name="file" id="file" class="form-control" accept=".xlsx,.csv" required />
                            <small class="text-muted">Supported formats: Excel (.xlsx), CSV (.csv)</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="material-icons me-1">upload</i> Import
                    </button>
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Questions</p>
                                <h5 class="font-weight-bolder mb-0">{{ $stats['total'] }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                <i class="material-icons opacity-10">quiz</i>
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Active Questions</p>
                                <h5 class="font-weight-bolder mb-0">{{ $stats['active'] }}</h5>
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Inactive Questions</p>
                                <h5 class="font-weight-bolder mb-0">{{ $stats['inactive'] }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="material-icons opacity-10">pause_circle</i>
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Exams</p>
                                <h5 class="font-weight-bolder mb-0">{{ $exams->count() }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="material-icons opacity-10">school</i>
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
                    <h5 class="text-capitalize"><i class="material-icons opacity-10">question_answer</i> Questions Management</h5>
                    <div class="actions_item">
                        <a class="btn btn-darken" href="{{ route('questions.create') }}" title="Add New Question">
                            <i class="material-icons">add</i> New Question
                        </a>
                        <button type="button" class="btn btn-info" id="importQuestionsBtn" title="Import Questions">
                            <i class="material-icons">playlist_add</i> Import
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

                    @if ($message = Session::get('error'))
                    <div class="alert alert-danger alert-dismissible text-white mx-4">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <span><i class="material-icons">error</i> {{ $message }}</span>
                    </div>
                    @endif

                    <!-- Filters -->
                    <div class="row mx-4 mb-3">
                        <div class="col-md-3">
                            <select id="examFilter" class="form-control form-control-sm">
                                <option value="">All Exams</option>
                                @foreach ($exams as $exam)
                                    <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                                        {{ $exam->title }} ({{ $exam->exam_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="typeFilter" class="form-control form-control-sm">
                                <option value="">All Types</option>
                                <option value="true_false" {{ request('question_type') == 'true_false' ? 'selected' : '' }}>True/False</option>
                                <option value="single_choice" {{ request('question_type') == 'single_choice' ? 'selected' : '' }}>Single Choice</option>
                                <option value="multiple_choice" {{ request('question_type') == 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                                <option value="fill_in_the_blank_text" {{ request('question_type') == 'fill_in_the_blank_text' ? 'selected' : '' }}>Fill in the Blank (Text)</option>
                                <option value="fill_in_the_blank_choice" {{ request('question_type') == 'fill_in_the_blank_choice' ? 'selected' : '' }}>Fill in the Blank (Choice)</option>
                                <option value="matching" {{ request('question_type') == 'matching' ? 'selected' : '' }}>Matching</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="statusFilter" class="form-control form-control-sm">
                                <option value="">All Status</option>
                                <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="button" id="clearFilters" class="btn btn-outline-secondary btn-sm">
                                <i class="material-icons">clear</i> Clear Filters
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table" id="questiontable">
                            <thead>
                                <tr>
                                    <th class="not-export-col">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                        </div>
                                    </th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Question</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Exam</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Type</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Status</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Created</th>
                                    <th class="not-export-col text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($questions as $question)
                                <tr id="question-{{ $question->id }}" data-status="{{ $question->is_active ? '1' : '0' }}" data-type="{{ $question->question_type }}" data-exam="{{ $question->exam_id }}">
                                    <td class="not-export-col">
                                        <div class="form-check">
                                            <input class="form-check-input row-checkbox" type="checkbox" value="{{ $question->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="text-sm font-weight-bold mb-0">{{ Str::limit(strip_tags($question->question_text), 60) }}</h6>
                                            <p class="text-xs text-secondary mb-0">
                                                @if($question->question_image)
                                                    <span class="badge badge-sm bg-gradient-info">Has Image</span>
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <span class="text-sm font-weight-bold">{{ $question->exam->title ?? 'N/A' }}</span>
                                            @if($question->exam)
                                                <br><span class="badge badge-sm bg-gradient-info">{{ $question->exam->exam_code }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-sm bg-gradient-secondary">
                                            {{ str_replace('_', ' ', ucwords($question->question_type)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($row->is_active == 'active')
                                            <span class="status-badge badge badge-sm bg-gradient-success">Active</span>
                                        @elseif($row->is_active == 'inactive')
                                            <span class="status-badge badge badge-sm bg-gradient-danger">Inactive</span>
                                        @else
                                            <span class="status-badge badge badge-sm bg-gradient-warning">Draft</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-sm">{{ $question->created_at->format('M d, Y') }}</span>
                                        <br>
                                        <small class="text-secondary">{{ $question->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td class="not-export-col">
                                        <div class="dropdown float-lg-end pe-4" id="question-{{ $question->id }}-dropdown">
                                            <a class="cursor-pointer" id="dropdownTable" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v text-secondary"></i>
                                            </a>
                                            <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5" aria-labelledby="dropdownTable">
                                                <li><a class="dropdown-item border-radius-md" href="{{ route('questions.show', $question->id) }}">
                                                    <i class="material-icons">remove_red_eye</i> View Details</a></li>
                                                <li><a class="dropdown-item border-radius-md" href="{{ route('questions.edit', $question->id) }}">
                                                    <i class="material-icons">edit</i> Edit Question</a></li>
                                                <li><a class="dropdown-item border-radius-md duplicate-btn" href="#" data-id="{{ $question->id }}">
                                                    <i class="material-icons">content_copy</i> Duplicate</a></li>
                                                @if(!$question->is_active)
                                                    <li><a class="dropdown-item border-radius-md" href="#" onclick="toggleStatus({{ $question->id }}, 'activate')">
                                                        <i class="material-icons">check_circle</i> Activate</a></li>
                                                @else
                                                    <li><a class="dropdown-item border-radius-md" href="#" onclick="toggleStatus({{ $question->id }}, 'inactive')">
                                                        <i class="material-icons">pause_circle</i> Deactivate</a></li>
                                                @endif
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="deletebtn dropdown-item border-radius-md text-danger" href="#" 
                                                    data-action="{{ route('questions.destroy', $question->id) }}">
                                                    <i class="material-icons">delete</i> Delete Question</a></li>
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
    var table = $('#questiontable').DataTable({
        columnDefs: [{
            orderable: false,
            targets: [0, -1] // First and last columns not orderable
        }, {
            targets: 0, width: '50px'
        }, {
            targets: 1, width: '250px'
        }, {
            targets: 2, width: '150px'
        }],
        order: [[7, 'desc']], // Order by created date
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
                    orientation: 'landscape',
                    exportOptions: { columns: ':visible:not(.not-export-col)' },
                    customize: function(doc) {
                        doc.title = 'Questions Report | OEMS';
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
    $('#examFilter, #typeFilter, #statusFilter').on('change', function() {
        var exam = $('#examFilter').val();
        var type = $('#typeFilter').val();
        var status = $('#statusFilter').val();
        
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var row = $(table.row(dataIndex).node());
            var rowExam = row.data('exam');
            var rowType = row.data('type');
            var rowStatus = row.data('status').toString();
            
            var examMatch = !exam || rowExam == exam;
            var typeMatch = !type || rowType === type;
            var statusMatch = !status || rowStatus === status;
            
            return examMatch && typeMatch && statusMatch;
        });
        
        table.draw();
        $.fn.dataTable.ext.search.pop();
    });

    $('#clearFilters').on('click', function() {
        $('#examFilter, #typeFilter, #statusFilter').val('');
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
        $('#selectedCount').text(selectedCount + ' questions selected');
        $('#bulkActionModal').modal('show');
    });

    // Delete modal
    $('#questiontable').on('click', '.deletebtn', function () {
        var action = $(this).attr("data-action");
        $('#question_delete_modal').attr('action', action);
        $('#deletemodal').modal('show');
    });

    // Import modal
    $('#importQuestionsBtn').on('click', function (e) {
        e.preventDefault();
        $('#importmodal').modal('show');
    });

    // Duplicate question
    $(document).on('click', '.duplicate-btn', function(e) {
        e.preventDefault();
        var questionId = $(this).data('id');
        
        if (confirm('Are you sure you want to duplicate this question?')) {
            window.location.href = '/admin/questions/' + questionId + '/duplicate';
        }
    });

    // Export buttons
    $('.dropdown-item-tools').on('click', function (e) {
        e.preventDefault();
        var action = $(this).data('action');
        var buttonClass = '.buttons-' + action;
        table.button(buttonClass).trigger();
    });
});

function updateStatusUI(questionId, newStatus) {
    const dropdown = $(`#question-${questionId}-dropdown`);
    const statusToggle = dropdown.find('[onclick*="toggleStatus"]');
    const statusBadge = $(`#question-${questionId}`).find('.status-badge');
    
    if (newStatus == 1) {
        statusToggle.attr('onclick', `toggleStatus(${questionId}, 'inactive')`);
        statusToggle.html('<i class="material-icons">pause_circle</i> Deactivate');

        statusBadge.removeClass('bg-gradient-danger')
                  .addClass('bg-gradient-success')
                  .text('Active');
    } else {
        statusToggle.attr('onclick', `toggleStatus(${questionId}, 'activate')`);
        statusToggle.html('<i class="material-icons">check_circle</i> Activate');

        statusBadge.removeClass('bg-gradient-success')
                  .addClass('bg-gradient-danger')
                  .text('Inactive');
    }
}

function toggleStatus(questionId, newStatus) {
    const token = $('meta[name="csrf-token"]').attr('content');
    
    if (!token) {
        console.error('CSRF token not found');
        return;
    }
    
    $.ajax({
        url: `/admin/questions/${questionId}/toggle-status`,
        type: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            status: newStatus,
        },
        beforeSend: function() {
            $(`#question-${questionId}`).addClass('opacity-50');
        },
        success: function(response) {
            if (response.success) {
                updateStatusUI(questionId, newStatus);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error toggling status:', error);
            alert('Failed to update question status. Please try again.');
        },
        complete: function() {
            $(`#question-${questionId}`).removeClass('opacity-50');
        }
    });
}
</script>
@endsection
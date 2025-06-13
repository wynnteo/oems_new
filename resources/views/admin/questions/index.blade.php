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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="question_delete_modal" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Are you sure you want to delete this question?</p>
                    <p class="text-danger"><small>This action cannot be undone.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Delete Modal -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-labelledby="bulkDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkDeleteModalLabel">Delete Selected Questions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <span id="selectedCount">0</span> selected questions?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmBulkDelete">Delete Selected</button>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">quiz</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Total Questions</p>
                        <h4 class="mb-0">{{ $stats['total'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">check_box</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Active Questions</p>
                        <h4 class="mb-0">{{ $stats['active'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">disabled_by_default</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Inactive Questions</p>
                        <h4 class="mb-0">{{ $stats['inactive'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">school</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Total Exams</p>
                        <h4 class="mb-0">{{ $exams->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="material-icons opacity-10 me-2">question_answer</i>
                            Questions Management
                        </h5>
                        <div class="btn-group">
                            <a class="btn btn-primary" href="{{ route('questions.create') }}" title="Add New Question">
                                <i class="material-icons me-1">add</i> New Question
                            </a>
                            <button type="button" class="btn btn-info" id="importQuestionsBtn" title="Import Questions">
                                <i class="material-icons me-1">playlist_add</i> Import
                            </button>
                            <div class="btn-group">
                                <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="material-icons me-1">download</i> Export
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item dropdown-item-tools" href="#" data-action="csv">
                                        <i class="material-icons me-2">description</i> CSV
                                    </a></li>
                                    <li><a class="dropdown-item dropdown-item-tools" href="#" data-action="excel">
                                        <i class="material-icons me-2">table_chart</i> Excel
                                    </a></li>
                                    <li><a class="dropdown-item dropdown-item-tools" href="#" data-action="pdf">
                                        <i class="material-icons me-2">picture_as_pdf</i> PDF
                                    </a></li>
                                    <li><a class="dropdown-item dropdown-item-tools" href="#" data-action="print">
                                        <i class="material-icons me-2">print</i> Print
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="examFilter" class="form-label">Filter by Exam</label>
                            <select id="examFilter" class="form-select">
                                <option value="">All Exams</option>
                                @foreach ($exams as $exam)
                                    <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                                        {{ $exam->title }} ({{ $exam->exam_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="typeFilter" class="form-label">Filter by Type</label>
                            <select id="typeFilter" class="form-select">
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
                            <label for="statusFilter" class="form-label">Filter by Status</label>
                            <select id="statusFilter" class="form-select">
                                <option value="">All Status</option>
                                <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="searchInput" class="form-label">Search</label>
                            <input type="text" id="searchInput" class="form-control" placeholder="Search questions..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <!-- Bulk Actions -->
                    <div class="row mb-3" id="bulkActions" style="display: none;">
                        <div class="col-12">
                            <div class="alert alert-info d-flex align-items-center">
                                <span id="selectedItemsText">0 items selected</span>
                                <div class="ms-auto">
                                    <button type="button" class="btn btn-sm btn-danger" id="bulkDeleteBtn">
                                        <i class="material-icons me-1">delete</i> Delete Selected
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="material-icons me-2">check_circle</i>
                        <span>{{ $message }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    @if ($message = Session::get('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="material-icons me-2">error</i>
                        <span>{{ $message }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table align-items-center mb-0" id="questiontable">
                            <thead>
                                <tr>
                                    <th class="not-export-col">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                        </div>
                                    </th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Question</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Exam</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Type</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Difficulty</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Points</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Status</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Created</th>
                                    <th class="not-export-col text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($questions as $question)
                                <tr>
                                    <td class="not-export-col">
                                        <div class="form-check">
                                            <input class="form-check-input question-checkbox" type="checkbox" value="{{ $question->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input status-toggle" type="checkbox" 
                                                   data-id="{{ $question->id }}" 
                                                   {{ $question->is_active ? 'checked' : '' }}>
                                            <label class="form-check-label text-sm">
                                                {{ $question->is_active ? 'Active' : 'Inactive' }}
                                            </label>
                                        </div>
                                    </td>
                                    <td class="text-sm">
                                        {{ $question->created_at->format('M d, Y') }}
                                        <br>
                                        <small class="text-secondary">{{ $question->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td class="not-export-col">
                                        <div class="dropdown">
                                            <button class="btn btn-link text-secondary mb-0" data-bs-toggle="dropdown">
                                                <i class="fa fa-ellipsis-v text-xs"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end px-2 py-3">
                                                <li>
                                                    <a class="dropdown-item border-radius-md" href="{{ route('questions.show', $question->id) }}">
                                                        <i class="material-icons me-2">visibility</i> View
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item border-radius-md" href="{{ route('questions.edit', $question->id) }}">
                                                        <i class="material-icons me-2">edit</i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item border-radius-md duplicate-btn" href="#" data-id="{{ $question->id }}">
                                                        <i class="material-icons me-2">content_copy</i> Duplicate
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item border-radius-md text-danger deletebtn" href="#" 
                                                       data-action="{{ route('questions.destroy', $question->id) }}">
                                                        <i class="material-icons me-2">delete</i> Delete
                                                    </a>
                                                </li>
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
    // Initialize DataTable
    var table = $('#questiontable').DataTable({
        columnDefs: [
            {
                orderable: false,
                targets: [0, -1] // First and last columns
            },
            {
                targets: 0,
                width: '50px'
            },
            {
                targets: -1,
                width: '100px'
            }
        ],
        order: [[7, 'desc']], // Order by created date
        pageLength: 25,
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'csvHtml5',
                text: 'CSV',
                exportOptions: {
                    columns: ':visible:not(.not-export-col)'
                }
            },
            {
                extend: 'excelHtml5',
                text: 'Excel',
                exportOptions: {
                    columns: ':visible:not(.not-export-col)'
                }
            },
            {
                extend: 'pdfHtml5',
                text: 'PDF',
                pageSize: 'A4',
                orientation: 'landscape',
                exportOptions: {
                    columns: ':visible:not(.not-export-col)'
                },
                customize: function(doc) {
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    doc.defaultStyle.fontSize = 10;
                    doc.styles.tableHeader.fontSize = 11;
                }
            },
            {
                extend: 'print',
                text: 'Print',
                exportOptions: {
                    columns: ':visible:not(.not-export-col)'
                }
            }
        ]
    });

    // Hide DataTable buttons (we'll use our custom ones)
    $('.dt-buttons').hide();

    // Custom export buttons
    $('.dropdown-item-tools').on('click', function (e) {
        e.preventDefault();
        var action = $(this).data('action');
        
        switch (action) {
            case 'csv':
                table.button('.buttons-csv').trigger();
                break;
            case 'excel':
                table.button('.buttons-excel').trigger();
                break;
            case 'pdf':
                table.button('.buttons-pdf').trigger();
                break;
            case 'print':
                table.button('.buttons-print').trigger();
                break;
        }
    });

    // Filters
    $('#examFilter, #typeFilter, #statusFilter').on('change', function() {
        applyFilters();
    });

    $('#searchInput').on('keyup', function() {
        table.search(this.value).draw();
    });

    function applyFilters() {
        var examFilter = $('#examFilter').val();
        var typeFilter = $('#typeFilter').val();
        var statusFilter = $('#statusFilter').val();

        // Apply exam filter
        if (examFilter) {
            table.column(2).search(examFilter, true, false);
        } else {
            table.column(2).search('', true, false);
        }

        // Apply type filter
        if (typeFilter) {
            table.column(3).search(typeFilter, true, false);
        } else {
            table.column(3).search('', true, false);
        }

        // Apply status filter
        if (statusFilter !== '') {
            var statusText = statusFilter === '1' ? 'Active' : 'Inactive';
            table.column(6).search(statusText, true, false);
        } else {
            table.column(6).search('', true, false);
        }

        table.draw();
    }

    // Select All functionality
    $('#selectAll').on('change', function() {
        $('.question-checkbox').prop('checked', this.checked);
        updateBulkActions();
    });

    // Individual checkbox change
    $(document).on('change', '.question-checkbox', function() {
        updateBulkActions();
        
        // Update select all checkbox
        var totalCheckboxes = $('.question-checkbox').length;
        var checkedCheckboxes = $('.question-checkbox:checked').length;
        
        $('#selectAll').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
        $('#selectAll').prop('checked', checkedCheckboxes === totalCheckboxes);
    });

    function updateBulkActions() {
        var selectedItems = $('.question-checkbox:checked').length;
        
        if (selectedItems > 0) {
            $('#bulkActions').show();
            $('#selectedItemsText').text(selectedItems + ' items selected');
        } else {
            $('#bulkActions').hide();
        }
    }

    // Status toggle
    $(document).on('change', '.status-toggle', function() {
        var questionId = $(this).data('id');
        var isChecked = $(this).is(':checked');
        var label = $(this).siblings('label');
        
        $.ajax({
            url: '/admin/questions/' + questionId + '/toggle-status',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    label.text(response.status ? 'Active' : 'Inactive');
                    showNotification(response.message, 'success');
                }
            },
            error: function() {
                // Revert the toggle on error
                $('.status-toggle[data-id="' + questionId + '"]').prop('checked', !isChecked);
                showNotification('Failed to update status', 'error');
            }
        });
    });

    // Delete single question
    $(document).on('click', '.deletebtn', function () {
        var action = $(this).data('action');
        $('#question_delete_modal').attr('action', action);
        $('#deletemodal').modal('show');
    });

    // Bulk delete
    $('#bulkDeleteBtn').on('click', function() {
        var selectedIds = $('.question-checkbox:checked').map(function() {
            return $(this).val();
        }).get();
        
        $('#selectedCount').text(selectedIds.length);
        $('#bulkDeleteModal').modal('show');
    });

    $('#confirmBulkDelete').on('click', function() {
        var selectedIds = $('.question-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        $.ajax({
            url: '/admin/questions/bulk-delete',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                ids: selectedIds
            },
            success: function(response) {
                if (response.success) {
                    $('#bulkDeleteModal').modal('hide');
                    showNotification(response.message, 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                }
            },
            error: function() {
                showNotification('Failed to delete questions', 'error');
            }
        });
    });

    // Duplicate question
    $(document).on('click', '.duplicate-btn', function(e) {
        e.preventDefault();
        var questionId = $(this).data('id');
        
        if (confirm('Are you sure you want to duplicate this question?')) {
            window.location.href = '/admin/questions/' + questionId + '/duplicate';
        }
    });

    // Import modal
    $('#importQuestionsBtn').on('click', function (e) {
        e.preventDefault();
        $('#importmodal').modal('show');
    });

    // Notification function
    function showNotification(message, type) {
        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        var icon = type === 'success' ? 'check_circle' : 'error';
        
        var notification = `
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="material-icons me-2">${icon}</i>
                <span>${message}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('body').append(notification);
        
        // Auto dismiss after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
});
</script>

<style>
.status-toggle:checked {
    background-color: #28a745;
    border-color: #28a745;
}

.badge {
    font-size: 0.75em;
}

.avatar-sm img {
    width: 40px;
    height: 40px;
    object-fit: cover;
}

.table td {
    vertical-align: middle;
}

.dropdown-menu {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.form-check-input:indeterminate {
    background-color: #6c757d;
    border-color: #6c757d;
}

@media (max-width: 768px) {
    .btn-group {
        flex-direction: column;
    }
    
    .btn-group .btn {
        margin-bottom: 0.25rem;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
}
</style>
@endsection
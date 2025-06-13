@extends('layouts.master')

@section('title')
Courses | Admin Panel
@endsection

@section('content')
<!-- Delete Modal -->
<div class="modal fade" id="deletemodal" tabindex="-1" aria-labelledby="deleteModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Delete Course</h5>
                <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="course_delete_modal" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="text-center">
                        <i class="material-icons text-warning mb-3" style="font-size: 48px;">warning</i>
                        <h6 class="mb-3">Are you sure you want to delete this course?</h6>
                        <p class="text-muted small">This action cannot be undone. All related enrollments and exams will also be affected.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Course</button>
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
                        <option value="activate">Activate Selected Courses</option>
                        <option value="deactivate">Deactivate Selected Courses</option>
                        <option value="feature">Mark as Featured</option>
                        <option value="unfeature">Remove from Featured</option>
                        <option value="draft">Move to Draft</option>
                        <option value="delete">Delete Selected Courses</option>
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Courses</p>
                                <h5 class="font-weight-bolder mb-0">{{ $courses->count() }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                <i class="material-icons opacity-10">book</i>
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Active Courses</p>
                                <h5 class="font-weight-bolder mb-0">{{ $courses->where('is_active', 'active')->count() }}</h5>
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Featured Courses</p>
                                <h5 class="font-weight-bolder mb-0">{{ $courses->where('is_featured', true)->count() }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="material-icons opacity-10">star</i>
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Draft Courses</p>
                                <h5 class="font-weight-bolder mb-0">{{ $courses->where('is_active', 'draft')->count() }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="material-icons opacity-10">draft</i>
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
                    <h5 class="text-capitalize"><i class="material-icons opacity-10">book</i> Courses Management</h5>
                    <div class="actions_item">
                        <a class="btn btn-darken" href="{{ route('courses.create') }}" title="Add New Course">
                            <i class="material-icons">add</i> New Course
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
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="difficultyFilter" class="form-control form-control-sm">
                                <option value="">All Difficulties</option>
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="featuredFilter" class="form-control form-control-sm">
                                <option value="">All Courses</option>
                                <option value="1">Featured Only</option>
                                <option value="0">Non-Featured</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="button" id="clearFilters" class="btn btn-outline-secondary btn-sm">
                                <i class="material-icons">clear</i> Clear Filters
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table" id="coursetable">
                            <thead>
                                <tr>
                                    <th class="not-export-col">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                        </div>
                                    </th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Thumbnail</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Course Code</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Course Details</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Instructor</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Price</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Status</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Enrollments</th>
                                    <th class="not-export-col text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($courses as $row)
                                <tr id="course-{{ $row->id }}" data-status="{{ $row->is_active }}" data-difficulty="{{ $row->difficulty_level }}" data-featured="{{ $row->is_featured ? '1' : '0' }}">
                                    <td class="not-export-col">
                                        <div class="form-check">
                                            <input class="form-check-input row-checkbox" type="checkbox" value="{{ $row->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        @if($row->thumbnail)
                                            <img src="{{ asset('storage/' . $row->thumbnail) }}" alt="{{ $row->title }}" 
                                                 class="img-thumbnail" style="width: 50px; height: 35px; object-fit: cover;">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center" 
                                                 style="width: 50px; height: 35px; border-radius: 4px;">
                                                <i class="material-icons text-muted" style="font-size: 18px;">image</i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-sm bg-gradient-info">{{ $row->course_code }}</span>
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="text-sm font-weight-bold mb-0">{{ $row->title }}</h6>
                                            <p class="text-xs text-secondary mb-0">
                                                <span class="badge badge-sm bg-gradient-secondary">{{ ucfirst($row->difficulty_level) }}</span>
                                                @if($row->category)
                                                    <span class="badge badge-sm bg-gradient-dark">{{ $row->category }}</span>
                                                @endif
                                                @if($row->is_featured)
                                                    <span class="badge badge-sm bg-gradient-warning">Featured</span>
                                                @endif
                                            </p>
                                            @if($row->duration)
                                                <small class="text-muted">{{ $row->duration }} hours</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-xs">{{ $row->instructor ?? 'Not assigned' }}</span>
                                    </td>
                                    <td>
                                        <span class="text-sm font-weight-bold">
                                            @if($row->price)
                                                @money($row->price)
                                            @else
                                                <span class="text-success">Free</span>
                                            @endif
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
                                        <span class="text-sm">{{ $row->enrolments->count() ?? 0 }}</span>
                                    </td>
                                    <td class="not-export-col">
                                        <div class="dropdown float-lg-end pe-4" id="course-{{ $row->id }}-dropdown">
                                            <a class="cursor-pointer" id="dropdownTable" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v text-secondary"></i>
                                            </a>
                                            <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5" aria-labelledby="dropdownTable">
                                                <li><a class="dropdown-item border-radius-md" href="{{ route('courses.show', $row->id) }}">
                                                    <i class="material-icons">remove_red_eye</i> View Details</a></li>
                                                <li><a class="dropdown-item border-radius-md" href="{{ route('courses.edit', $row->id) }}">
                                                    <i class="material-icons">edit</i> Edit Course</a></li>
                                                @if($row->is_active != 'active')
                                                    <li><a class="dropdown-item border-radius-md" href="#" onclick="toggleStatus({{ $row->id }}, 'active')">
                                                        <i class="material-icons">check_circle</i> Activate</a></li>
                                                @else
                                                    <li><a class="dropdown-item border-radius-md" href="#" onclick="toggleStatus({{ $row->id }}, 'inactive')">
                                                        <i class="material-icons">pause_circle</i> Deactivate</a></li>
                                                @endif
                                                @if(!$row->is_featured)
                                                    <li><a class="dropdown-item border-radius-md" href="#" onclick="toggleFeatured({{ $row->id }}, 1)">
                                                        <i class="material-icons">star</i> Mark Featured</a></li>
                                                @else
                                                    <li><a class="dropdown-item border-radius-md" href="#" onclick="toggleFeatured({{ $row->id }}, 0)">
                                                        <i class="material-icons">star_border</i> Remove Featured</a></li>
                                                @endif
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="deletebtn dropdown-item border-radius-md text-danger" href="#" 
                                                    data-action="{{ route('courses.destroy', $row->id) }}">
                                                    <i class="material-icons">delete</i> Delete Course</a></li>
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
    var table = $('#coursetable').DataTable({
        columnDefs: [{
            orderable: false,
            targets: [0, -1] // First and last columns not orderable
        }, {
            targets: 0, width: '50px'
        }, {
            targets: 1, width: '80px'
        }, {
            targets: 2, width: '120px'
        }],
        order: [[2, 'asc']], // Order by course code
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
                        doc.title = 'Courses Report | OEMS';
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
    $('#statusFilter, #difficultyFilter, #featuredFilter').on('change', function() {
        var status = $('#statusFilter').val();
        var difficulty = $('#difficultyFilter').val();
        var featured = $('#featuredFilter').val();
        
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var row = $(table.row(dataIndex).node());
            var rowStatus = row.data('status');
            var rowDifficulty = row.data('difficulty');
            var rowFeatured = row.data('featured').toString();
            
            var statusMatch = !status || rowStatus === status;
            var difficultyMatch = !difficulty || rowDifficulty === difficulty;
            var featuredMatch = !featured || rowFeatured === featured;
            
            return statusMatch && difficultyMatch && featuredMatch;
        });
        
        table.draw();
        $.fn.dataTable.ext.search.pop();
    });

    $('#clearFilters').on('click', function() {
        $('#statusFilter, #difficultyFilter, #featuredFilter').val('');
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
        $('#selectedCount').text(selectedCount + ' courses selected');
        $('#bulkActionModal').modal('show');
    });

    // Delete modal
    $('#coursetable').on('click', '.deletebtn', function () {
        var action = $(this).attr("data-action");
        $('#course_delete_modal').attr('action', action);
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

function updateStatusUI(courseId, newStatus) {
    const dropdown = $(`#course-${courseId}-dropdown`);
    const statusToggle = dropdown.find('[onclick*="toggleStatus"]');
    const statusBadge = $(`#course-${courseId}`).find('.status-badge');
    
    if (newStatus === 'active') {
        statusToggle.attr('onclick', `toggleStatus(${courseId}, 'inactive')`);
        statusToggle.html('<i class="material-icons">pause_circle</i> Deactivate');

        statusBadge.removeClass('bg-gradient-danger bg-gradient-warning')
                  .addClass('bg-gradient-success')
                  .text('Active');
    } else {
        statusToggle.attr('onclick', `toggleStatus(${courseId}, 'active')`);
        statusToggle.html('<i class="material-icons">check_circle</i> Activate');

        statusBadge.removeClass('bg-gradient-success bg-gradient-warning')
                  .addClass('bg-gradient-danger')
                  .text('Inactive');
    }
}

function updateFeaturedUI(courseId, featured) {
    const dropdown = $(`#course-${courseId}-dropdown`);
    const featuredToggle = dropdown.find('[onclick*="toggleFeatured"]');
    const courseRow = $(`#course-${courseId}`);
    const badgeContainer = courseRow.find('.text-xs.text-secondary');
    const featuredBadge = badgeContainer.find('.badge:contains("Featured")');
    
    if (featured) {
        featuredToggle.attr('onclick', `toggleFeatured(${courseId}, false)`);
        featuredToggle.html('<i class="material-icons">star_border</i> Remove Featured');
        if (featuredBadge.length === 0) {
            badgeContainer.append('<span class="badge badge-sm bg-gradient-warning">Featured</span>');
        }
    } else {
        featuredToggle.attr('onclick', `toggleFeatured(${courseId}, true)`);
        featuredToggle.html('<i class="material-icons">star</i> Mark Featured');
        featuredBadge.remove();
    }
}

function toggleStatus(courseId, newStatus) {
    const token = $('meta[name="csrf-token"]').attr('content');
    
    // Debug: Check if token exists
    if (!token) {
        console.error('CSRF token not found');
        return;
    }
    $.ajax({
        url: `/admin/courses/${courseId}/toggle-status`,
        type: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            status: newStatus,
        },
        beforeSend: function() {
            $(`#course-${courseId}`).addClass('opacity-50');
        },
        success: function(response) {
            if (response.success) {
                //location.reload();
                updateStatusUI(courseId, newStatus);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error toggling status:', error);
            alert('Failed to update course status. Please try again.');
        },
        complete: function() {
            $(`#course-${courseId}`).removeClass('opacity-50');
        }
    });
}

function toggleFeatured(courseId, featured) {
    $.ajax({
        url: `/admin/courses/${courseId}/toggle-featured`,
        type: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            featured: featured,
        },
        beforeSend: function() {
            // Optional: Show loading state
            $(`#course-${courseId}`).addClass('opacity-50');
        },
        success: function(response) {
            if (response.success) {
                //location.reload();
                updateFeaturedUI(courseId, featured);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error toggling featured status:', error);
            alert('Failed to update featured status. Please try again.');
        },
        complete: function() {
            $(`#course-${courseId}`).removeClass('opacity-50');
        }
    });
}
</script>
@endsection
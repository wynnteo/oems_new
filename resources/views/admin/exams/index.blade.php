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
                <h5 class="modal-title" id="exampleModalLabel">Delete Record</h5>
                <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="exam_delete_modal" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    Are you sure you want to delete?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header actions">
                    <h5 class="text-capitalize"><i class="material-icons opacity-10">assignment</i> Exams</h5>
                    <div class="actions_item">

                        <a class="btn btn-darken" href="{{ route('exams.create') }}" title="Add New Exam">
                            <i class="material-icons">add</i> New Exam
                        </a>
                        <div class="btn-group">
                            <button type="button" class="btn btn-light dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                Export
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <li><a class="dropdown-item dropdown-item-tools border-radius-md" href="#" data-action="csv">CSV</a></li>
                                <li><a class="dropdown-item dropdown-item-tools" href="#" data-action="excel">Excel</a></li>
                                <li><a class="dropdown-item dropdown-item-tools" href="#" data-action="pdf">PDF</a></li>
                                <li><a class="dropdown-item dropdown-item-tools" href="#" data-action="print">Print</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible text-white">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <span>{{ $message }}</span>
                    </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table" id="examtable">
                            <thead>
                                <tr>
                                    <th class="not-export-col"></th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Course Code</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Title</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Exam Date</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Duration</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Status</th>
                                    <th class="not-export-col text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($exams as $row)
                                <tr>
                                    <td></td>
                                    <td>{{ $row->course->course_code }}</td>
                                    <td>{{ $row->title }}</td>
                                    <td>{{ $row->start_time }}</td>
                                    
                                    <td>
                                        {{ $row->formatDuration() }}
                                    </td>
                                    <td>@if ($row->status == 'available')
                                        <span class="badge bg-success">Available</span>
                                        @else
                                            <span class="badge bg-warning">Not Available</span>
                                        @endif</td>
                                    <td>
                                        <div class="dropdown float-lg-end pe-4">
                                            <a class="cursor-pointer" id="dropdownTable" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fa fa-ellipsis-v text-secondary"></i>
                                            </a>
                                            <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5"
                                                aria-labelledby="dropdownTable">
                                                <li><a class="dropdown-item border-radius-md"
                                                    href="{{ route('exams.show', $row->id) }}"> <i class="material-icons">remove_red_eye</i> View</a></li>
                                                <li><a class="dropdown-item border-radius-md"
                                                        href="{{ route('exams.edit', $row->id) }}"> <i class="material-icons">edit</i> Edit</a></li>
                                                <li><a class="dropdown-item border-radius-md"
                                                    href="{{ route('questions.create', $row->id) }}"> <i class="material-icons">question_answer</i> Add Question</a></li>
                                                <li><a class="deletebtn dropdown-item border-radius-md" href="#"
                                                        data-action="{{ route('exams.destroy', $row->id) }}"> <i class="material-icons">delete</i>Delete</a></li>
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
    @endsection


    @section('scripts')
    <script>
        $(document).ready(function () {
            $('#examtable').DataTable({
                columnDefs: [{
                    orderable: false,
                    render: DataTable.render.select(),
                    targets: 0
                },{
                    targets: 0,
                    width: '50px' 
                },
                {
                    targets: 1,
                    width: '150px' 
                },
                {
                    targets: -1,
                    width: '100px'
                }],
                order: [[1, 'asc']],
                select: {
                    style: 'os',
                    selector: 'td:first-child'
                },
                layout: {
                    top1Start: {
                        buttons: [{
                            text: 'csv',
                            extend: 'csvHtml5',
                            exportOptions: {
                                columns: ':visible:not(.not-export-col)'
                            }
                        },
                        {
                            text: 'excel',
                            extend: 'excelHtml5',
                            exportOptions: {
                                columns: ':visible:not(.not-export-col)'
                            }
                        },
                        {
                            text: 'pdf',
                            extend: 'pdfHtml5',
                            pageSize: 'A4',
                            exportOptions: {
                                columns: ':visible:not(.not-export-col)'
                            },
                            customize: function(doc) {
                                doc.title = 'All Exams | OEMS';
                                doc.styles.title = {
                                    fontSize: 14,
                                    bold: true,
                                    color: 'black',
                                    alignment: 'center'
                                };

                                doc.content.forEach(function (item) {
                                    if (item.table) {
                                        // Set table width to 100%
                                        item.table.widths = Array(item.table.body[0].length).fill('*'); // '*' makes columns stretch to full width
                                    }
                                });
                            }
                        },
                        {
                            text: 'print',
                            extend: 'print',
                            exportOptions: {
                                columns: ':visible:not(.not-export-col)'
                            }
                        }]
                    }
                }
            });

            $('#examtable').on('click', '.deletebtn', function () {
                $action = $(this).attr("data-action");
                $('#exam_delete_modal').attr('action', $action);
                $('#deletemodal').modal('show');
            });

            $('.dropdown-item-tools').on('click', function (e) {
                e.preventDefault();

                var table = $('#examtable').DataTable();
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
                    default:
                        alert("Unknown action");
                }
            });
        });
    </script>
    @endsection
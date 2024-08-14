@extends('layouts.master')

@section('title')
Questions | Admin Panel
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
            <form id="question_delete_modal" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    Are you sure you want to delete this question?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="importmodal" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Questions</h5>
                <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="import_qns_modal" action="{{ route('questions.import') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <small><em>Please download the <a
                                            href="{{ asset('public/assets/sample_excel.xlsx') }}">import template</a>.
                                        Do not remove any columns.</em></small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>Exam:</strong>
                                <select name="exam_id" class="form-control">
                                    @foreach ($exams as $exam)
                                        <option value="{{ $exam->id }}">
                                            {{ $exam->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>File:</strong>
                                <input type="file" name="file" id="file" class="form-control" />
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-darken">Import</button>
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
                    <h5 class="text-capitalize"><i class="material-icons opacity-10">question_answer</i> Questions</h5>
                    <div class="actions_item">
                        
                        <a class="btn btn-darken" href="{{ route('questions.create') }}" title="Add New Question">
                            <i class="material-icons">add</i> New Question
                        </a>
                        <a id="importQuestionsBtn" class="btn btn-darken" href="#" title="Import Questions">
                            <i class="material-icons">playlist_add</i> Import Questions
                        </a>
                        <div class="btn-group">
                            <button type="button" class="btn btn-light dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                Export
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <li><a class="dropdown-item" href="#" data-action="csv">CSV</a></li>
                                <li><a class="dropdown-item" href="#" data-action="excel">Excel</a></li>
                                <li><a class="dropdown-item" href="#" data-action="pdf">PDF</a></li>
                                <li><a class="dropdown-item" href="#" data-action="print">Print</a></li>
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
                        <table class="table" id="questiontable">
                            <thead>
                                <tr>
                                    <th class="not-export-col"></th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Question Text</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Exam Title</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Question Type</th>
                                    <th class="not-export-col text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($questions as $row)
                                <tr>
                                    <td></td>
                                    <td>{{ $row->question_text }}</td>
                                    <td>{{ $row->exam->title }}</td>
                                    <td>
                                        @switch($row->question_type)
                                            @case('true_false')
                                                True/False
                                                @break
                                            @case('single_choice')
                                                Single Choice
                                                @break
                                            @case('multiple_choice')
                                                Multiple Choice
                                                @break
                                            @case('fill_in_the_blank_choice')
                                                Fill in the Blank (Choice)
                                                @break
                                            @case('fill_in_the_blank_text')
                                                Fill in the Blank (Text)
                                                @break
                                            @case('matching')
                                                Matching
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="dropdown float-lg-end pe-4">
                                            <a class="cursor-pointer" id="dropdownTable" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fa fa-ellipsis-v text-secondary"></i>
                                            </a>
                                            <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5"
                                                aria-labelledby="dropdownTable">
                                                <!-- <li><a class="dropdown-item border-radius-md"
                                                    href="{{ route('questions.show', $row->id) }}"> <i class="material-icons">remove_red_eye</i> View</a></li> -->
                                                <li><a class="dropdown-item border-radius-md"
                                                        href="{{ route('questions.edit', $row->id) }}"> <i class="material-icons">edit</i> Edit</a></li>
                                                <li><a class="deletebtn dropdown-item border-radius-md" href="#"
                                                        data-action="{{ route('questions.destroy', $row->id) }}"> <i class="material-icons">delete</i> Delete</a></li>
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
        
        $('#questiontable').DataTable({
            columnDefs: [{
                orderable: false,
                render: DataTable.render.select(),
                targets: 0
            }],
            order: [[1, 'asc']],
            select: {
                style: 'os',
                selector: 'td:first-child'
            },
            layout: {
                    topStart: {
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
                                doc.title = 'All Questions | OEMS';
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

        $('#questiontable').on('click', '.deletebtn', function () {
            $action = $(this).attr("data-action");
            $('#question_delete_modal').attr('action', $action);
            $('#deletemodal').modal('show');
        });

        $('#importQuestionsBtn').on('click', function (e) {
            e.preventDefault(); // Prevent default action
            $('#importmodal').modal('show'); // Show the import modal
        });

        $('.dropdown-item').on('click', function (e) {
                e.preventDefault();

                var table = $('#questiontable').DataTable();
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

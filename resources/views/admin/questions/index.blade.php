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
                                    <th></th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Question Text</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Exam Title</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Question Type</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
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
                                            @case('fill_in_the_blank')
                                                Fill in the Blank
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
                                                <li><a class="dropdown-item border-radius-md"
                                                    href="{{ route('questions.show', $row->id) }}"> <i class="material-icons">remove_red_eye</i> View</a></li>
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
            }
        });

        $('#questiontable').on('click', '.deletebtn', function () {
            $action = $(this).attr("data-action");
            $('#question_delete_modal').attr('action', $action);
            $('#deletemodal').modal('show');
        });
    });
</script>
@endsection

@extends('layouts.master')

@section('title')
Exams | Admin Panel
@endsection

@section('content')


<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header actions">
                    <h5 class="text-capitalize">Exams</h5>
                    <div class="actions_item">
                        <a class="btn btn-darken" href="{{ route('exams.edit', $exam->id) }}" title="Edit Student">
                            <i class="material-icons">edit</i> Edit Exam
                        </a>
                        <a class="btn btn-darken" href="{{ route('exams.index') }}" title="Back">
                            <i class="material-icons">arrow_back</i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="row">
                        <!-- Total Pass -->
                        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                            <div class="card">
                                <div class="card-header p-3 pt-2">
                                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                                        <i class="material-icons opacity-10">check_circle</i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0 text-capitalize">Total Pass</p>
                                        <h4 class="mb-0">145</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Total Fail -->
                        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                            <div class="card">
                                <div class="card-header p-3 pt-2">
                                    <div class="icon icon-lg icon-shape bg-gradient-danger shadow-danger text-center border-radius-xl mt-n4 position-absolute">
                                        <i class="material-icons opacity-10">cancel</i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0 text-capitalize">Total Fail</p>
                                        <h4 class="mb-0">48</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Highest Mark -->
                        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                            <div class="card">
                                <div class="card-header p-3 pt-2">
                                    <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                                        <i class="material-icons opacity-10">trending_up</i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0 text-capitalize">Highest Mark</p>
                                        <h4 class="mb-0">97</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Lowest Mark -->
                        <div class="col-xl-3 col-sm-6">
                            <div class="card">
                                <div class="card-header p-3 pt-2">
                                    <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                                        <i class="material-icons opacity-10">trending_down</i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0 text-capitalize">Lowest Mark</p>
                                        <h4 class="mb-0">56</h4>
                                    </div>
                                </div>
                            </div>
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
            
        });
    </script>
    @endsection
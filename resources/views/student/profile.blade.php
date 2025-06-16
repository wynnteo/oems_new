@extends('layouts.studentmaster')

@section('title')
    Profile | Student Portal
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center">
                        <p class="mb-0">Edit Profile</p>
                        <button class="btn btn-primary btn-sm ms-auto" type="submit" form="profileForm">Save Changes</button>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form id="profileForm" method="POST" action="{{ route('student.profile.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group input-group-outline mb-3 @if(old('name', $student->name)) is-filled @endif">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $student->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-outline mb-3 @if(old('email', $student->email)) is-filled @endif">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                           value="{{ old('email', $student->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group input-group-outline mb-3 @if(old('student_code', $student->student_code)) is-filled @endif">
                                    <label class="form-label">Student Code</label>
                                    <input type="text" class="form-control" value="{{ $student->student_code }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-outline mb-3 @if(old('phone_number', $student->phone_number)) is-filled @endif">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" 
                                           value="{{ old('phone_number', $student->phone_number) }}">
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group input-group-outline mb-3 @if(old('date_of_birth', $student->date_of_birth)) is-filled @endif">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                           value="{{ old('date_of_birth', $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('Y-m-d') : '') }}">
                                    @error('date_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-outline mb-3">
                                    <label class="form-label">Gender</label>
                                    <select name="gender" class="form-control @error('gender') is-invalid @enderror">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender', $student->gender) == 'M' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $student->gender) == 'F' ? 'selected' : '' }}>Female</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="input-group input-group-outline mb-3 @if(old('address', $student->address)) is-filled @endif">
                                    <label class="form-label">Address</label>
                                    <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" 
                                           value="{{ old('address', $student->address) }}">
                                    
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-profile">
                <img src="../assets/img/bg-profile.jpg" alt="Image placeholder" class="card-img-top">
                <div class="row justify-content-center">
                    <div class="col-4 col-lg-4 order-lg-2">
                        <div class="mt-n4 mt-lg-n6 mb-4 mb-lg-0">
                            <a href="javascript:;">
                                <img src="../assets/img/team-2.jpg" class="rounded-circle img-fluid border border-2 border-white">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-header text-center border-0 pt-0 pt-lg-2 pb-4 pb-lg-3">
                    <div class="d-flex justify-content-between">
                        <a href="javascript:;" class="btn btn-sm btn-info mb-0 d-none d-lg-block">Connect</a>
                        <a href="javascript:;" class="btn btn-sm btn-info mb-0 d-block d-lg-none"><i class="ni ni-collection"></i></a>
                        <a href="javascript:;" class="btn btn-sm btn-dark float-right mb-0 d-none d-lg-block">Message</a>
                        <a href="javascript:;" class="btn btn-sm btn-dark float-right mb-0 d-block d-lg-none"><i class="ni ni-email-83"></i></a>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <div class="col">
                            <div class="d-flex justify-content-center">
                                <div class="d-grid text-center">
                                    <span class="text-lg font-weight-bolder">{{ $student->enrollments()->count() }}</span>
                                    <span class="text-sm opacity-8">Courses</span>
                                </div>
                                <div class="d-grid text-center mx-4">
                                    <span class="text-lg font-weight-bolder">{{ $student->studentExams()->where('status', 'completed')->count() }}</span>
                                    <span class="text-sm opacity-8">Exams</span>
                                </div>
                                <div class="d-grid text-center">
                                    <span class="text-lg font-weight-bolder">
                                        @php
                                            $totalExams = $student->studentExams()->where('status', 'completed')->count();
                                            $passedExams = $student->studentExams()
                                                ->where('status', 'completed')
                                                ->whereHas('examResult', function($query) {
                                                    $query->whereRaw('score > (SELECT passing_grade FROM exams WHERE exams.id = student_exams.exam_id)');
                                                })
                                                ->count();
                                            echo $totalExams > 0 ? number_format(($passedExams / $totalExams) * 100, 1) . '%' : '0%';
                                        @endphp
                                    </span>
                                    <span class="text-sm opacity-8">Pass Rate</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <h5>
                            {{ $student->name }}
                        </h5>
                        <div class="h6 font-weight-300">
                            <i class="ni location_pin mr-2"></i>Student ID: {{ $student->student_code }}
                        </div>
                        <div class="h6 mt-4">
                            <i class="ni business_briefcase-24 mr-2"></i>Status: 
                            <span class="badge bg-gradient-{{ $student->status == 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($student->status) }}
                            </span>
                        </div>
                        <div>
                            <i class="ni education_hat mr-2"></i>Joined: {{ $student->created_at->format('M Y') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Settings -->
            <!-- <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">Account Settings</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action border-0 px-0 py-2">
                            <div class="d-flex align-items-center">
                                <i class="material-icons text-info me-3">notifications</i>
                                <div>
                                    <h6 class="mb-0 text-sm">Notification Settings</h6>
                                    <p class="text-xs text-secondary mb-0">Manage your notifications</p>
                                </div>
                            </div>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action border-0 px-0 py-2">
                            <div class="d-flex align-items-center">
                                <i class="material-icons text-success me-3">download</i>
                                <div>
                                    <h6 class="mb-0 text-sm">Download Certificate</h6>
                                    <p class="text-xs text-secondary mb-0">Download your certificates</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div> -->
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-fill form labels
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.input-group-outline input, .input-group-outline select, .input-group-outline textarea');
        
        inputs.forEach(function(input) {
            if (input.value !== '') {
                input.closest('.input-group-outline').classList.add('is-filled');
            }
            
            input.addEventListener('focus', function() {
                this.closest('.input-group-outline').classList.add('is-focused');
            });
            
            input.addEventListener('blur', function() {
                this.closest('.input-group-outline').classList.remove('is-focused');
                if (this.value !== '') {
                    this.closest('.input-group-outline').classList.add('is-filled');
                } else {
                    this.closest('.input-group-outline').classList.remove('is-filled');
                }
            });
        });
    });
</script>
@endsection
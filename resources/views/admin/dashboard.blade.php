@extends('layouts.master')

@section('title')
    Dashboard | Online Exam Management System
@endsection

@section('content')   
<div class="container-fluid py-4">
    <!-- Main Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">school</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Total Courses</p>
                        <h4 class="mb-0">{{ number_format($totalCourses) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">people</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Total Students</p>
                        <h4 class="mb-0">{{ number_format($totalStudents) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">assignment</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Total Exams</p>
                        <h4 class="mb-0">{{ number_format($totalExams) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">card_membership</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Certificates Issued</p>
                        <h4 class="mb-0">{{ number_format($totalCertificates) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Statistics Cards -->
    <div class="row mt-4">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">trending_up</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Total Revenue</p>
                        <h4 class="mb-0">${{ number_format($totalRevenue, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-danger shadow-danger text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">calendar_today</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">This Month Revenue</p>
                        <h4 class="mb-0">${{ number_format($monthlyRevenue, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-secondary shadow-secondary text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">analytics</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Average Score</p>
                        <h4 class="mb-0">{{ number_format($averageScore, 1) }}%</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">how_to_reg</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Total Enrollments</p>
                        <h4 class="mb-0">{{ number_format($totalEnrollments) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mt-4">
        <div class="col-lg-4 col-md-6 mt-4 mb-4">
            <div class="card z-index-2">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                        <div class="chart">
                            <canvas id="chart-registrations" class="chart-canvas" height="170"></canvas>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h6 class="mb-0">Exam Registrations</h6>
                    <p class="text-sm">Monthly registration trends</p>
                    <hr class="dark horizontal">
                    <div class="d-flex">
                        <i class="material-icons text-sm my-auto me-1">schedule</i>
                        <p class="mb-0 text-sm">{{ $recentRegistrations }} registrations in last 30 days</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mt-4 mb-4">
            <div class="card z-index-2">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                    <div class="bg-gradient-success shadow-success border-radius-lg py-3 pe-1">
                        <div class="chart">
                            <canvas id="chart-revenue" class="chart-canvas" height="170"></canvas>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h6 class="mb-0">Monthly Revenue</h6>
                    <p class="text-sm">Revenue growth over time</p>
                    <hr class="dark horizontal">
                    <div class="d-flex">
                        <i class="material-icons text-sm my-auto me-1">trending_up</i>
                        <p class="mb-0 text-sm">Updated in real-time</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mt-4 mb-3">
            <div class="card z-index-2">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
                        <div class="chart">
                            <canvas id="chart-performance" class="chart-canvas" height="170"></canvas>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h6 class="mb-0">Performance Distribution</h6>
                    <p class="text-sm">Student exam performance</p>
                    <hr class="dark horizontal">
                    <div class="d-flex">
                        <i class="material-icons text-sm my-auto me-1">assessment</i>
                        <p class="mb-0 text-sm">{{ number_format($completedExamAttempts) }} completed attempts</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Exams and Activities -->
    <div class="row mb-4">
        <div class="col-lg-8 col-md-6 mb-md-0 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-lg-6 col-7">
                            <h6>Recent Exams</h6>
                            <p class="text-sm mb-0">
                                <i class="fa fa-list-alt text-info" aria-hidden="true"></i>
                                <span class="font-weight-bold ms-1">Performance Overview</span>
                            </p>
                        </div>
                        <div class="col-lg-6 col-5 my-auto text-end">
                            <div class="dropdown float-lg-end pe-4">
                                <a class="cursor-pointer" id="dropdownTable" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa fa-ellipsis-v text-secondary"></i>
                                </a>
                                <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5" aria-labelledby="dropdownTable">
                                    <li><a class="dropdown-item border-radius-md" href="{{ route('exams.index') }}">View All Exams</a></li>
                                    <li><a class="dropdown-item border-radius-md" href="{{ route('exams.create') }}">Create New Exam</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Exam</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Course</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Avg Score</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Completion</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentExams as $exam)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ Str::limit($exam->title, 30) }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $exam->total_attempts }} attempts</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ Str::limit($exam->course->title ?? 'N/A', 25) }}</p>
                                        <p class="text-xs text-secondary mb-0">{{ $exam->course->category ?? '' }}</p>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="text-xs font-weight-bold">{{ number_format($exam->average_score, 1) }}%</span>
                                    </td>
                                    <td class="align-middle">
                                        <div class="progress-wrapper w-75 mx-auto">
                                            <div class="progress-info">
                                                <div class="progress-percentage">
                                                    <span class="text-xs font-weight-bold">{{ $exam->completion_rate }}%</span>
                                                </div>
                                            </div>
                                            <div class="progress">
                                                <div class="progress-bar 
                                                    @if($exam->completion_rate >= 80) bg-gradient-success
                                                    @elseif($exam->completion_rate >= 60) bg-gradient-info
                                                    @elseif($exam->completion_rate >= 40) bg-gradient-warning
                                                    @else bg-gradient-danger
                                                    @endif
                                                    w-{{ min(100, $exam->completion_rate) }}" 
                                                    role="progressbar" 
                                                    aria-valuenow="{{ $exam->completion_rate }}" 
                                                    aria-valuemin="0" 
                                                    aria-valuemax="100">
                                                </div>
                                            </div>
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
        <div class="col-lg-4 col-md-6">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <h6>Recent Activities</h6>
                    <p class="text-sm">
                        <i class="fa fa-clock text-info" aria-hidden="true"></i>
                        <span class="font-weight-bold">Latest updates</span>
                    </p>
                </div>
                <div class="card-body p-3">
                    <div class="timeline timeline-one-side">
                        @foreach($recentActivities as $activity)
                        <div class="timeline-block mb-3">
                            <span class="timeline-step">
                                <i class="material-icons text-{{ $activity->color }} text-gradient">{{ $activity->icon }}</i>
                            </span>
                            <div class="timeline-content">
                                <h6 class="text-dark text-sm font-weight-bold mb-0">{{ $activity->title }}</h6>
                                <p class="text-secondary text-xs mt-1 mb-0">{{ $activity->description }}</p>
                                <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">{{ $activity->time->format('M d, Y g:i A') }}</p>
                            </div>
                        </div>
                        @endforeach
                        
                        @if($recentActivities->isEmpty())
                        <div class="timeline-block mb-3">
                            <span class="timeline-step">
                                <i class="material-icons text-secondary text-gradient">info</i>
                            </span>
                            <div class="timeline-content">
                                <h6 class="text-dark text-sm font-weight-bold mb-0">No recent activities</h6>
                                <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">Activities will appear here</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Popular Courses Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h6>Popular Courses</h6>
                    <p class="text-sm">
                        <i class="fa fa-star text-warning" aria-hidden="true"></i>
                        <span class="font-weight-bold ms-1">Most enrolled courses</span>
                    </p>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Course</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Category</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Enrollments</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($popularCourses as $course)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $course->title }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $course->course_code }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $course->category }}</p>
                                        <p class="text-xs text-secondary mb-0">{{ $course->difficulty_level }}</p>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="badge badge-sm bg-gradient-success">{{ $course->enrolments_count }}</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">
                                            @if($course->is_active)
                                                <span class="badge badge-sm bg-gradient-success">Active</span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-secondary">Inactive</span>
                                            @endif
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{ route('courses.show', $course->id) }}" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="View course">
                                            View
                                        </a>
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

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Registration Chart
    const regCtx = document.getElementById('chart-registrations');
    if (regCtx) {
     
        new Chart(regCtx, {
            type: 'line',
            data: {
                labels: @json(collect($monthlyRegistrations ?? [])->pluck('month')->toArray()),
                datasets: [{
                    label: 'Registrations',
                    data: @json(collect($monthlyRegistrations ?? [])->pluck('count')->toArray()),
                    borderColor: 'rgb(255, 255, 255)',
                    backgroundColor: 'rgba(255, 255, 255, 0.2)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgb(255, 255, 255)',
                    pointBorderColor: 'rgb(255, 255, 255)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.8)',
                            stepSize: 1
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.2)'
                        }
                    },
                    x: {
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.8)'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.2)'
                        }
                    }
                },
                elements: {
                    point: {
                        radius: 4,
                        hoverRadius: 6
                    }
                }
            }
        });
    }

    // Revenue Chart
    const revCtx = document.getElementById('chart-revenue');
    if (revCtx) {
        new Chart(revCtx, {
            type: 'bar',
            data: {
                labels: @json(collect($monthlyRevenueData ?? [])->pluck('month')->toArray()),
                datasets: [{
                    label: 'Revenue ($)',
                    data: @json(collect($monthlyRevenueData ?? [])->pluck('revenue')->toArray()),
                    backgroundColor: 'rgba(255, 255, 255, 0.8)',
                    borderColor: 'rgb(255, 255, 255)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.8)',
                            callback: function(value) {
                                return '$' + Number(value).toLocaleString();
                            }
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.2)'
                        }
                    },
                    x: {
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.8)'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.2)'
                        }
                    }
                }
            }
        });
    }

    // Performance Distribution Chart
    const perfCtx = document.getElementById('chart-performance');
    if (perfCtx) {
        const performanceData = @json($performanceDistribution ?? []);
        const labels = Object.keys(performanceData);
        const data = Object.values(performanceData);
        
        new Chart(perfCtx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: [
                        'rgba(76, 175, 80, 0.8)',   // Excellent - Green
                        'rgba(33, 150, 243, 0.8)',  // Good - Blue
                        'rgba(255, 193, 7, 0.8)',   // Average - Yellow
                        'rgba(255, 152, 0, 0.8)',   // Below Average - Orange
                        'rgba(244, 67, 54, 0.8)'    // Poor - Red
                    ],
                    borderColor: [
                        'rgba(76, 175, 80, 1)',
                        'rgba(33, 150, 243, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(255, 152, 0, 1)',
                        'rgba(244, 67, 54, 1)'
                    ],
                    borderWidth: 2,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            color: 'rgba(255, 255, 255, 0.8)',
                            padding: 15,
                            font: {
                                size: 11
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                },
                cutout: '60%'
            }
        });
    }
});
</script>
@endsection

@endsection
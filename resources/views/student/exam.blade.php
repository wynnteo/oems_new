@extends('layouts.exammaster')

@section('title')
    Exam | Student Portal
@endsection

<style>
.exam-container {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.exam-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease;
}

.exam-card:hover {
    transform: translateY(-5px);
}

.exam-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    position: relative;
    overflow: hidden;
}

.exam-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    transform: rotate(45deg);
}

.exam-header h4 {
    position: relative;
    z-index: 2;
    margin: 0;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.exam-code-section {
    background: #f8f9fa;
    border-left: 4px solid #667eea;
    padding: 1rem;
    border-radius: 0 8px 8px 0;
    margin-bottom: 1.5rem;
}

.exam-code-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.exam-code-value {
    font-family: 'Courier New', monospace;
    font-size: 1.1rem;
    font-weight: bold;
    color: #667eea;
    background: white;
    padding: 0.5rem;
    border-radius: 4px;
    display: inline-block;
    margin: 0;
}

.exam-description {
    padding: 1.5rem;
    background: #fff;
    border-radius: 10px;
    border: 1px solid #e9ecef;
    margin-bottom: 1.5rem;
}

.instructions-container {
    background: #fff;
    border-radius: 10px;
    padding: 1.5rem;
    border-left: 4px solid #ffc107;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.instructions-title {
    color: #495057;
    font-weight: 600;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
}

.instructions-title::before {
    content: '⚠️';
    margin-right: 0.5rem;
    font-size: 1.2rem;
}

.instructions-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.instructions-list li {
    padding: 0.75rem 0;
    border-bottom: 1px solid #f1f3f4;
    position: relative;
    padding-left: 2rem;
}

.instructions-list li:last-child {
    border-bottom: none;
}

.instructions-list li::before {
    content: '✓';
    position: absolute;
    left: 0;
    top: 0.75rem;
    color: #28a745;
    font-weight: bold;
    font-size: 1.1rem;
}

.time-highlight {
    background: linear-gradient(45deg, #ffc107, #ffeb3b);
    color: #495057;
    padding: 2px 8px;
    border-radius: 4px;
    font-weight: bold;
}

.start-exam-section {
    text-align: center;
    padding: 2rem 0;
}

.card-footer-enhanced {
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
    text-align: center;
    padding: 1.5rem;
    color: #6c757d;
    font-style: italic;
}

.alert-danger {
    border: none;
    border-radius: 10px;
    background: #fff5f5;
    color: #e53e3e;
    border-left: 4px solid #e53e3e;
}

.exam-stats {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.stat-item {
    text-align: center;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.5);
    border-radius: 12px;
    min-width: 120px;
    transition: all 0.3s ease;
}

.stat-item:hover {
    transform: translateY(-2px);
    background: rgba(255, 255, 255, 0.7);
}

.stat-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.875rem;
    color: var(--text-light);
    font-weight: 500;
}

.stat-value {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-dark);
}

@media (max-width: 768px) {
    .exam-container {
        padding: 1rem;
    }
    
    .exam-header {
        padding: 1.5rem;
    }
    
    .exam-stats {
        gap: 1rem;
    }

    .stat-item {
        min-width: 100px;
        padding: 0.75rem;
    }
}
</style>

@section('content')
<div class="exam-container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card exam-card">
                <div class="exam-header">
                    <h4>{{ $exam->title }}</h4>
                </div>
                
                <div class="card-body">
                    <div class="exam-code-section">
                        <div class="exam-code-label">Exam Code</div>
                        <p class="exam-code-value">{{ $exam->exam_code }}</p>
                    </div>

                    <!-- Display Validation Errors -->
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <div class="exam-description">
                        <p class="mb-0">{!! $exam->description !!}</p>
                    </div>

                    <div class="instructions-container">
                        <h5 class="instructions-title">Please read the following instructions carefully before starting the exam</h5>
                        <ul class="instructions-list">
                            <li>Ensure you have a stable internet connection</li>
                            <li>The exam has a time limit of <span class="time-highlight">{{ $exam->formatDuration() }}</span></li>
                            <li>Once started, the exam cannot be paused</li>
                            <li>All answers must be submitted before the time runs out</li>
                            <li>Do not refresh the page during the exam</li>
                            <li>Make sure you are in a quiet environment to avoid distractions</li>
                        </ul>
                    </div>

                    <div class="start-exam-section">
                        <div class="exam-stats">
                            <div class="stat-item">
                                <div class="stat-icon text-primary">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="stat-value">{{ $exam->formatDuration() }}</div>
                                <div class="stat-label">Duration</div>
                            </div>
                            @if($exam->number_of_questions)
                                <div class="stat-item">
                                    <div class="stat-icon text-success">
                                        <i class="fas fa-question-circle"></i>
                                    </div>
                                    <div class="stat-value">{{ $exam->number_of_questions }}</div>
                                    <div class="stat-label">Questions</div>
                                </div>
                            @endif
                            @if($exam->passing_grade)
                                <div class="stat-item">
                                    <div class="stat-icon text-warning">
                                        <i class="fas fa-trophy"></i>
                                    </div>
                                    <div class="stat-value">{{ $exam->passing_grade }}%</div>
                                    <div class="stat-label">Pass Mark</div>
                                </div>
                            @endif
                        </div>


                        @if(!isset($error))
                            <form action="{{ route('student.exam.start', ['examId' => $exam->id]) }}" method="POST" id="startExamForm">
                                @csrf
                                <button type="submit" class="start-exam-btn" id="startBtn">
                                    <i class="fas fa-play me-2"></i>
                                    Start Exam
                                </button>
                            </form>
                        @else
                            <div class="text-center">
                                <button class="start-exam-btn" disabled style="opacity: 0.6; cursor: not-allowed;">
                                    <i class="fas fa-lock me-2"></i>
                                    Exam Unavailable <br/>{{ $error }}
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card-footer-enhanced">
                    🍀 Good luck! Stay calm and do your best.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === "F12" || (e.ctrlKey && e.shiftKey && e.key === 'I') || (e.ctrlKey && e.shiftKey && e.key === 'J') || (e.ctrlKey && e.key === 'U')) {
            e.preventDefault();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === "F5" || (e.ctrlKey && e.key === "r")) {
            e.preventDefault();
        }
    });

    // Add subtle animation when page loads
    document.addEventListener('DOMContentLoaded', function() {
        const card = document.querySelector('.exam-card');
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100);
    });
</script>
@endsection
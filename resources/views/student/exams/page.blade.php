@extends('layouts.exammaster')

@section('content')
<style>
    :root {
        --primary-color: #2563eb;
        --success-color: #16a34a; 
        --warning-color: #d97706;
        --danger-color: #dc2626;
        --secondary-color: #64748b;
        --light-bg: #f8fafc;
        --border-color: #e2e8f0;
    }

    body {
        background-color: var(--light-bg);
    }

    .exam-header {
        padding: 1.5rem;
        margin-bottom: 2rem;
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .question-card {
        border: none;
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        background: white;
    }

    .question-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .question-text {
        font-size: 1.1rem;
        line-height: 1.6;
        margin-bottom: 1.5rem;
        padding: 1.5rem;
        background: linear-gradient(135deg, #f8fafc, #e2e8f0);
        border-radius: 0.5rem;
        border-left: 4px solid var(--primary-color);
    }

    .options_panel {
        min-height: 300px;
        padding: 1.5rem;
    }

    .form-check {
        margin-bottom: 1rem;
        padding: 0.75rem;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
        border: 2px solid transparent;
    }

    .form-check-input:checked + .form-check-label {
        font-weight: 600;
        color: var(--primary-color);
    }

    .form-check-label {
        display: inline;
        padding-left: 0.5rem;
        cursor: pointer;
        font-size: 1rem;
        line-height: 1.5;
    }

    .timer-card {
        border: none;
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    #exam-timer {
        font-size: 2.5rem;
        font-weight: bold;
        font-family: 'Courier New', monospace;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .timer-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706) !important;
    }

    .timer-critical {
        background: linear-gradient(135deg, #ef4444, #dc2626) !important;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .navigation-card {
        border: none;
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        background: white;
    }

    .btn-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 0.5rem;
        margin-top: 1rem;
    }

    .btn-square {
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
        text-decoration: none;
        position: relative;
        border: 2px solid #e2e8f0;
        background: white;
        color: #64748b;
    }

    /* Default state - not answered */
    .btn-square:not(.answered):not(.active):not(.reviewed) {
        background: white;
        color: #64748b;
        border-color: #e2e8f0;
    }

    .btn-square:not(.answered):not(.active):not(.reviewed):hover {
        background: #f1f5f9;
        border-color: var(--primary-color);
        color: var(--primary-color);
    }

    /* Answered state */
    .btn-square.answered {
        background: var(--success-color);
        color: white;
        border-color: var(--success-color);
    }

    .btn-square.answered:hover {
        background: #15803d;
        border-color: #15803d;
    }

    /* Current question (active) */
    .btn-square.active {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.3);
    }

    /* Marked for review */
    .btn-square.reviewed {
        background: var(--warning-color);
        color: white;
        border-color: var(--warning-color);
    }

    .btn-square.reviewed:hover {
        background: #b45309;
        border-color: #b45309;
    }

    /* Answered + Marked for review */
    .btn-square.answered.reviewed {
        background: linear-gradient(135deg, var(--success-color), var(--warning-color));
        border-color: var(--warning-color);
    }

    /* Active + Answered */
    .btn-square.active.answered {
        background: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.3);
    }

    /* Active + Reviewed */
    .btn-square.active.reviewed {
        background: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.3);
    }

    .btn-square::after {
        content: '';
        position: absolute;
        top: 2px;
        right: 2px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: none;
    }

    .btn-square.reviewed::after {
        display: block;
        background: #fbbf24;
        border: 1px solid #f59e0b;
    }

    .btn-square.answered::after {
        display: block;
        background: #22c55e;
        border: 1px solid #16a34a;
    }

    .btn-square.answered.reviewed::after {
        background: linear-gradient(45deg, #22c55e, #fbbf24);
    }

    .navigation-buttons {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 2rem;
        padding: 1.5rem;
        background: #f8fafc;
        border-radius: 0.75rem;
        border-top: 1px solid #e2e8f0;
    }

    .btn-nav {
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        transition: all 0.2s ease;
        border: none;
        min-width: 120px;
    }

    .btn-nav:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .mark-review-section {
        background: #f8fafc;
        padding: 1rem 1.5rem;
        border-radius: 0.5rem;
        margin: 1.5rem 0;
        border: 1px solid #e2e8f0;
    }

    .mark-review-section input[type="checkbox"] {
        margin-right: 0.5rem;
        transform: scale(1.2);
    }

    .mark-review-section label {
        font-weight: 500;
        color: #374151;
        cursor: pointer;
    }

    .question-image {
        max-width: 100%;
        height: auto;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        margin: 1rem 0;
    }

    .progress-stats {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1rem;
        font-size: 0.875rem;
        color: #64748b;
    }

    .legend {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
        font-size: 0.8rem;
        flex-wrap: wrap;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 2px;
    }

    .fill-blank-input {
        border: 2px solid #e2e8f0;
        border-radius: 0.375rem;
        padding: 0.5rem;
        margin: 0 0.25rem;
        min-width: 120px;
        transition: border-color 0.2s ease;
    }

    .fill-blank-input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .alert-success {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        border: 1px solid #16a34a;
        color: #15803d;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    @media (max-width: 768px) {
        .btn-grid {
            grid-template-columns: repeat(4, 1fr);
        }
        
        #exam-timer {
            font-size: 2rem;
        }
        
        .navigation-buttons {
            flex-direction: column;
            gap: 1rem;
        }
        
        .navigation-buttons .btn-nav {
            width: 100%;
        }
    }
</style>
<div class="container">
    <!-- Header -->
    <div class="exam-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h3 class="mb-1">
                    <i class="fas fa-file-alt me-2"></i>
                    {{ $exam->title }}
                </h3>
                <p class="mb-0 opacity-75">Exam Code: {{ $exam->exam_code }}</p>
            </div>
            <div class="col-md-4 text-md-end">
                <h4 class="mb-0">
                    <i class="fas fa-question-circle me-2"></i>
                    Question {{ $currentIndex + 1 }} of {{ $questions->count() }}
                </h4>
            </div>
        </div>
    </div>
    <!-- Main Content -->
    <div class="row">
        <!-- Left Side: Question Panel -->
        <div class="col-lg-8">
            <div class="question-card">
                <div class="card-body p-0">
                    <div class="question-text">
                        <i class="fas fa-clipboard-question me-2 text-primary"></i>
                        @if($currentQuestion->question_type == 'fill_in_the_blank_with_text')
                            @foreach(explode('[]', $currentQuestion->question_text) as $index => $segment)
                                {!! $segment !!}
                                @if ($index < count(explode('[]', $currentQuestion->question_text)) - 1)
                                    <span class="text-primary fw-bold">_________</span>
                                @endif
                            @endforeach
                        @elseif($currentQuestion->question_type == 'fill_in_the_blank_with_choice')
                            @php
                                $textWithBlanks = str_replace('[]', '_________', $currentQuestion->question_text);
                            @endphp
                            {!! $textWithBlanks !!}
                        @else
                            {!! $currentQuestion->question_text !!}
                        @endif
                    </div>

                    @if($currentQuestion->description)
                        <div class="px-4 mb-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                {{ $currentQuestion->description }}
                            </small>
                        </div>
                    @endif

                    @if($currentQuestion->image_name)
                        <div class="px-4 mb-3">
                            <img src="{{ asset('storage/' . $currentQuestion->image_name) }}" 
                                 alt="Question Image" 
                                 class="question-image">
                        </div>
                    @endif

                    <form action="{{ route('student.exam.submit_answer', ['examId' => $exam->id]) }}" method="POST" id="examForm">
                        @csrf
                        <input type="hidden" name="question_id" value="{{ $currentQuestion->id }}">
                        <input type="hidden" name="session_key" value="{{ $session_key }}">
                        <input type="hidden" name="action" id="action">
                        
                        <div class="options_panel">
                            @php
                                $progressItem = collect($progress)->firstWhere('question_id', $currentQuestion->id);
                                $studentAnswer = $progressItem['student_answer'] ?? [];
                                // Ensure $studentAnswer is always an array
                                if (!is_array($studentAnswer)) {
                                    $studentAnswer = $studentAnswer ? [$studentAnswer] : [];
                                }
                            @endphp
                        
                            <!-- Question Options -->
                            @if($currentQuestion->question_type == 'single_choice' || $currentQuestion->question_type == 'fill_in_the_blank_with_choice')
                                @foreach($currentQuestion->options as $option)
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="radio" 
                                               name="answer[]" 
                                               id="option{{ $loop->index }}" 
                                               value="{{ $option }}"
                                               {{ in_array($option, $studentAnswer) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="option{{ $loop->index }}">
                                            {{ chr(65 + $loop->index) }}. {{ $option }}
                                        </label>
                                    </div>
                                @endforeach
                        
                            @elseif($currentQuestion->question_type == 'multiple_choice')
                                @foreach($currentQuestion->options as $option)
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="answer[]" 
                                               id="option{{ $loop->index }}" 
                                               value="{{ $option }}"
                                               {{ in_array($option, $studentAnswer) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="option{{ $loop->index }}">
                                            {{ chr(65 + $loop->index) }}. {{ $option }}
                                        </label>
                                    </div>
                                @endforeach
                        
                            @elseif($currentQuestion->question_type == 'true_false')
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="answer[]" 
                                           id="true" 
                                           value="true" 
                                           {{ in_array('true', $studentAnswer) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="true">
                                        <i class="fas fa-check text-success me-2"></i>True
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="answer[]" 
                                           id="false" 
                                           value="false" 
                                           {{ in_array('false', $studentAnswer) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="false">
                                        <i class="fas fa-times text-danger me-2"></i>False
                                    </label>
                                </div>
                        
                            @elseif($currentQuestion->question_type == 'fill_in_the_blank_with_text')
                                @php
                                    $segments = explode('[]', $currentQuestion->question_text);
                                    $totalInputs = count($segments) - 1;
                                @endphp
                                @foreach ($segments as $index => $segment)
                                    @if ($index < $totalInputs)
                                        <input type="text" 
                                            name="answer[]" 
                                            class="fill-blank-input" 
                                            value="{{ $studentAnswer[$index] ?? '' }}"
                                            placeholder="Enter answer...">
                                        @if ($index < $totalInputs - 1)
                                            ,
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    
                        <!-- Mark for Review -->
                        <div class="mark-review-section">
                            <input type="checkbox" 
                                   name="question_marked_review" 
                                   id="markForReview" 
                                   {{ ($progressItem['question_marked_review'] ?? false) ? 'checked' : '' }}>
                            <label for="markForReview">
                                <i class="fas fa-flag text-warning me-2"></i>
                                Mark this question for review
                            </label>
                        </div>
                    
                        <!-- Navigation Buttons -->
                        <div class="navigation-buttons">
                            <button type="submit" 
                                    class="btn btn-secondary btn-nav" 
                                    name="action" 
                                    value="previous" 
                                    {{ $previousQuestion !== null ? '' : 'disabled' }}>
                                <i class="fas fa-chevron-left me-2"></i>Previous
                            </button>
                            
                            <div class="text-center">
                                @php
                                    $allAnswered = true;
                                    foreach($progress as $item) {
                                        $hasAnswer = false;
                                        if (!empty($item['student_answer'])) {
                                            if (is_array($item['student_answer'])) {
                                                $hasAnswer = !empty(array_filter($item['student_answer']));
                                            } else {
                                                $hasAnswer = !empty($item['student_answer']);
                                            }
                                        }
                                        if (!$hasAnswer) {
                                            $allAnswered = false;
                                            break;
                                        }
                                    }
                                @endphp
                                
                                @if($allAnswered)
                                    <button type="submit" 
                                            class="btn btn-success btn-nav" 
                                            name="action" 
                                            value="submit"
                                            onclick="return confirm('Are you sure you want to submit the exam? You cannot change your answers after submission.')">
                                        <i class="fas fa-paper-plane me-2"></i>Submit Exam
                                    </button>
                                @endif
                            </div>
                            
                            <button type="submit" 
                                    class="btn btn-primary btn-nav" 
                                    name="action" 
                                    value="next" 
                                    {{ $nextQuestion !== null ? '' : 'disabled' }}>
                                Next<i class="fas fa-chevron-right ms-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Side: Navigation & Timer -->
        <div class="col-lg-4">
            <!-- Timer Card -->
            <div class="card timer-card mb-3" id="timer-card">
                <div class="card-body text-center">
                    <h5 class="mb-2">
                        <i class="fas fa-clock me-2"></i>Time Remaining
                    </h5>
                    <div id="exam-timer">
                        <!-- Timer script will populate here -->
                    </div>
                    <small class="opacity-75 mt-2 d-block">
                        <i class="fas fa-info-circle me-1"></i>
                        Exam will auto-submit when time expires
                    </small>
                </div>
            </div>

            <!-- Navigation Card -->
            <div class="card navigation-card">
                <div class="card-body">
                    <h5>
                        <i class="fas fa-list-ol me-2"></i>Question Navigation
                    </h5>
                    
                    <!-- Progress Stats -->
                    @php
                        $answeredCount = 0;
                        $reviewedCount = 0;
                        foreach($progress as $item) {
                            if (!empty($item['student_answer']) && (is_array($item['student_answer']) ? array_filter($item['student_answer']) : $item['student_answer'])) {
                                $answeredCount++;
                            }
                            if ($item['question_marked_review']) {
                                $reviewedCount++;
                            }
                        }
                    @endphp
                    
                    <div class="progress-stats">
                        <span><strong>{{ $answeredCount }}</strong> / {{ count($progress) }} Answered</span>
                        <span><strong>{{ $reviewedCount }}</strong> Marked for Review</span>
                    </div>
                    
                    <!-- Legend -->
                    <div class="legend">
                        <div class="legend-item">
                            <div class="legend-color" style="background: #e2e8f0;"></div>
                            <span>Not Answered</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background: var(--success-color);"></div>
                            <span>Answered</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background: var(--warning-color);"></div>
                            <span>Review</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background: var(--primary-color);"></div>
                            <span>Current</span>
                        </div>
                    </div>
                    
                    <!-- Question Grid -->
                    <div class="btn-grid">
                        @foreach($questions as $index => $question)
                            @php
                                // Find the corresponding progress entry for this question
                                $progressItem = $progress[$index];
                                
                                // Check if question is answered
                                $isAnswered = false;
                                if (!empty($progressItem['student_answer'])) {
                                    if (is_array($progressItem['student_answer'])) {
                                        // For array answers, check if any element is not empty
                                        $isAnswered = !empty(array_filter($progressItem['student_answer']));
                                    } else {
                                        // For non-array answers
                                        $isAnswered = !empty($progressItem['student_answer']);
                                    }
                                }
                                
                                $isReviewed = $progressItem['question_marked_review'] ?? false;
                                $isCurrent = $currentIndex == $index;
                                
                                // Build CSS classes
                                $classes = ['btn-square'];
                                if ($isCurrent) $classes[] = 'active';
                                if ($isAnswered) $classes[] = 'answered';
                                if ($isReviewed) $classes[] = 'reviewed';
                            @endphp
                            <a href="#" 
                                class="{{ implode(' ', $classes) }} question-nav-btn"
                                data-question-index="{{ $index }}"
                                title="Question {{ $index + 1 }}{{ $isAnswered ? ' - Answered' : '' }}{{ $isReviewed ? ' - Marked for Review' : '' }}">
                                    {{ $index + 1 }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let timeLeftInSeconds = @json($timeLeftInSeconds);
    console.log("Time left in seconds:", timeLeftInSeconds);

    function updateTimer() {
        if (timeLeftInSeconds <= 0) {
            document.getElementById('exam-timer').innerText = "Time's up!";

            document.getElementById('action').value = 'submit';
            document.getElementById('examForm').submit();
            // Optionally, handle exam expiration here
            return;
        }

        let minutes = Math.floor(timeLeftInSeconds / 60);
        let seconds = Math.floor(timeLeftInSeconds % 60);
        
        // Format minutes and seconds with leading zeros
        let formattedMinutes = minutes.toString().padStart(2, '0');
        let formattedSeconds = seconds.toString().padStart(2, '0');

        // Update the timer display
        document.getElementById('exam-timer').innerText = `${formattedMinutes}:${formattedSeconds}`;
        
        timeLeftInSeconds--;
        setTimeout(updateTimer, 1000);
    }

    updateTimer();
    
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

document.addEventListener('DOMContentLoaded', function() {
    const questionNavBtns = document.querySelectorAll('.question-nav-btn');
    
    questionNavBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const targetIndex = this.dataset.questionIndex;
            
            // Create a form to save current answer and navigate
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("exam.page.post", ["code" => $exam->id, "session_key" => $session_key]) }}';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            // Add current question data
            const currentQuestionId = document.querySelector('input[name="question_id"]').value;
            const currentAnswers = [];
            
            // Collect current answers
            const checkedInputs = document.querySelectorAll('input[name="answer[]"]:checked, input[name="answer[]"][type="text"]');
            checkedInputs.forEach(input => {
                if (input.type === 'text' && input.value.trim()) {
                    currentAnswers.push(input.value);
                } else if (input.checked) {
                    currentAnswers.push(input.value);
                }
            });
            
            const markReviewCheckbox = document.querySelector('input[name="question_marked_review"]');

            // Add hidden inputs
            const inputs = [
                { name: 'question_index', value: targetIndex },
                { name: 'save_current_answer', value: '1' },
                { name: 'current_question_id', value: currentQuestionId },
                { name: 'current_marked_review', value: markReviewCheckbox?.checked ? '1' : '0' }
            ];
            console.log(inputs);
            // Add current answers
            currentAnswers.forEach((answer, index) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'current_answer[]';
                input.value = answer;
                form.appendChild(input);
            });
            
            inputs.forEach(inputData => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = inputData.name;
                input.value = inputData.value;
                form.appendChild(input);
            });
            
            document.body.appendChild(form);
            form.submit();
        });
    });
});
</script>
@endsection

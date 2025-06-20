<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate - {{ $certificate->certificate_number }}</title>
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            margin: 0;
            padding: 40px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: calc(100vh - 80px);
            position: relative;
        }
        
        .certificate-container {
            background: white;
            border: 8px solid #2c3e50;
            border-radius: 20px;
            padding: 60px;
            text-align: center;
            position: relative;
            min-height: 500px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .certificate-border {
            border: 3px solid #e74c3c;
            padding: 40px;
            border-radius: 15px;
            height: 100%;
            position: relative;
        }
        
        .header {
            margin-bottom: 30px;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: #3498db;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            font-weight: bold;
        }
        
        .title {
            font-size: 48px;
            color: #2c3e50;
            margin: 20px 0;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 4px;
        }
        
        .subtitle {
            font-size: 24px;
            color: #7f8c8d;
            margin-bottom: 40px;
            font-style: italic;
        }
        
        .content {
            margin: 40px 0;
            line-height: 1.8;
        }
        
        .recipient {
            font-size: 18px;
            color: #34495e;
            margin-bottom: 20px;
        }
        
        .student-name {
            font-size: 36px;
            color: #e74c3c;
            font-weight: bold;
            margin: 20px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-bottom: 3px solid #e74c3c;
            display: inline-block;
            padding-bottom: 10px;
        }
        
        .course-info {
            font-size: 20px;
            color: #2c3e50;
            margin: 30px 0;
        }
        
        .course-name {
            font-weight: bold;
            color: #3498db;
        }
        
        .achievement {
            font-size: 18px;
            color: #27ae60;
            margin: 20px 0;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        
        .signature-section {
            text-align: center;
            width: 200px;
        }
        
        .signature-line {
            border-top: 2px solid #2c3e50;
            margin-bottom: 10px;
            height: 50px;
        }
        
        .signature-label {
            font-size: 14px;
            color: #7f8c8d;
            font-weight: bold;
        }
        
        .certificate-details {
            text-align: right;
            font-size: 12px;
            color: #95a5a6;
        }
        
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 120px;
            color: rgba(52, 152, 219, 0.1);
            font-weight: bold;
            z-index: 0;
            pointer-events: none;
        }
        
        .decorative-elements {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, #f39c12, #e67e22);
            border-radius: 50%;
            opacity: 0.1;
        }
        
        .decorative-elements::before {
            content: '';
            position: absolute;
            top: -20px;
            left: -20px;
            width: 60px;
            height: 60px;
            background: radial-gradient(circle, #9b59b6, #8e44ad);
            border-radius: 50%;
        }
        
        .qr-section {
            margin-top: 20px;
            font-size: 10px;
            color: #95a5a6;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="certificate-border">
            <div class="watermark">CERTIFIED</div>
            <div class="decorative-elements"></div>
            
            <div class="header">
                <div class="logo">
                    {{ substr(config('app.name', 'LMS'), 0, 1) }}
                </div>
                <div class="title">Certificate</div>
                <div class="subtitle">of Achievement</div>
            </div>
            
            <div class="content">
                <div class="recipient">This is to certify that</div>
                
                <div class="student-name">{{ $certificate->student->name }}</div>
                
                <div class="recipient">has successfully completed</div>
                
                <div class="course-info">
                    <div class="course-name">{{ $certificate->course->title }}</div>
                    @if($certificate->course->course_code)
                        <div style="font-size: 16px; color: #7f8c8d; margin-top: 5px;">
                            Course Code: {{ $certificate->course->course_code }}
                        </div>
                    @endif
                </div>
                
                @if($certificate->exam)
                    <div class="recipient">by passing the examination</div>
                    <div style="font-size: 18px; color: #3498db; font-weight: bold; margin: 10px 0;">
                        {{ $certificate->exam->title }}
                    </div>
                @endif
                
                <div class="achievement">
                    with a score of {{ number_format($certificate->score, 1) }}%
                    @if(isset($certificate->certificate_data['distinction']) && $certificate->certificate_data['distinction'])
                        <br>
                        <span style="color: #e74c3c;">
                            {{ ucwords(str_replace('_', ' ', $certificate->certificate_data['distinction'])) }}
                        </span>
                    @endif
                </div>
                
                @if(isset($certificate->certificate_data['notes']) && $certificate->certificate_data['notes'])
                    <div style="font-size: 14px; color: #7f8c8d; margin-top: 20px; font-style: italic;">
                        {{ $certificate->certificate_data['notes'] }}
                    </div>
                @endif
            </div>
            
            <div class="footer">
                <div class="signature-section">
                    <div class="signature-line"></div>
                    <div class="signature-label">DIRECTOR</div>
                </div>
                
                <div class="signature-section">
                    <div class="signature-line"></div>
                    <div class="signature-label">
                        @if($certificate->course->instructor)
                            {{ strtoupper($certificate->course->instructor) }}
                        @else
                            INSTRUCTOR
                        @endif
                    </div>
                </div>
                
                <div class="certificate-details">
                    <div><strong>Date Issued:</strong> {{ $certificate->issued_at->format('F d, Y') }}</div>
                    <div><strong>Certificate No:</strong> {{ $certificate->certificate_number }}</div>
                    <div><strong>Verification Code:</strong> {{ $certificate->verification_code }}</div>
                    
                    <div class="qr-section">
                        <div>Verify this certificate at:</div>
                        <div>{{ url('/verify-certificate') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
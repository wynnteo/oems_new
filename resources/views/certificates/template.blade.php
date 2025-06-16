<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin: 50px; }
        .certificate { border: 5px solid #0066cc; padding: 50px; }
        h1 { color: #0066cc; font-size: 36px; margin-bottom: 20px; }
        .student-name { font-size: 28px; font-weight: bold; margin: 30px 0; }
        .details { font-size: 18px; line-height: 1.6; }
        .verification { margin-top: 40px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="certificate">
        <h1>CERTIFICATE OF COMPLETION</h1>
        
        <p class="details">This is to certify that</p>
        
        <div class="student-name">{{ $certificate->certificate_data['student_name'] }}</div>
        
        <p class="details">
            has successfully completed the examination for<br>
            <strong>{{ $certificate->certificate_data['course_title'] }}</strong><br>
            {{ $certificate->certificate_data['exam_title'] }}
        </p>
        
        <p class="details">
            Score: <strong>{{ $certificate->certificate_data['score'] }}%</strong><br>
            Date: {{ $certificate->certificate_data['issued_date'] }}
        </p>
        
        <div class="verification">
            Certificate Number: {{ $certificate->certificate_number }}<br>
            Verification Code: {{ $certificate->verification_code }}
        </div>
    </div>
</body>
</html>
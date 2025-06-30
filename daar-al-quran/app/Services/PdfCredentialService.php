<?php

namespace App\Services;

use App\Models\Student;
use App\Models\ClassRoom;
use Mpdf\Mpdf;
use SimpleSoftwareIO\QrCode\Generator;
use Illuminate\Support\Collection;

class PdfCredentialService
{
    /**
     * Generate PDF with student credentials and QR codes using mPDF (upgraded from DomPDF)
     *
     * @param Collection $students
     * @param string $youtubeUrl
     * @param string $title
     * @return string PDF content
     */
    public function generateCredentialsPdf(Collection $students, string $youtubeUrl, string $title = 'Student Login Credentials'): string
    {
        $studentPages = $students->chunk(8); // 8 students per page (4 rows of 2)
        $html = $this->generateHtml($studentPages, $youtubeUrl, $title);
        return $this->generatePdf($html);
    }
    
    /**
     * Generate HTML for the PDF with mPDF-optimized styling
     *
     * @param Collection $studentPages
     * @param string $youtubeUrl
     * @param string $title
     * @return string
     */
    private function generateHtml(Collection $studentPages, string $youtubeUrl, string $title): string
    {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>' . $title . '</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 15mm;
            font-size: 14px;
            color: #333;
        }
        
        .header {
            text-align: center;
            background: #007bff;
            color: white;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        
        .header p {
            margin: 5px 0 0 0;
            font-size: 12px;
        }
        
        .student-grid {
            width: 100%;
        }
        
        .student-row {
            width: 100%;
            margin-bottom: 20px;
        }
        
        .student-card {
            width: 43%;
            border: 2px solid #007bff;
            border-radius: 8px;
            padding: 8px;
            margin-bottom: 25px;
            background: #f9f9f9;
            min-height: 100px;
            page-break-inside: avoid;
            position: relative;
        }
        
        .student-card.left {
            float: left;
            margin-right: 30%;
        }
        
        .student-card.right {
            float: right;
        }
        
        .student-name {
            background: #28a745;
            color: white;
            text-align: center;
            padding: 6px;
            margin: -8px -8px 8px -8px;
            border-radius: 6px 6px 0 0;
            font-weight: bold;
            font-size: 12px;
        }
        
        .arabic-name {
            direction: rtl;
            text-align: center;
        }
        
        .student-info {
            margin-bottom: 8px;
            direction: rtl;
            text-align: right;
        }
        
        .info-item {
            margin: 3px 0;
            padding: 3px;
            background: white;
            border-radius: 3px;
            border: 1px solid #ddd;
            direction: rtl;
            text-align: right;
        }
        
        .info-label {
            font-weight: bold;
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
            direction: rtl;
            text-align: right;
            display: block;
        }
        
        .info-value {
            font-family: monospace;
            font-size: 10px;
            color: #007bff;
            font-weight: bold;
            text-align: center;
            margin-top: 2px;
            direction: ltr;
        }
        
        .qr-section {
            text-align: center;
            border-top: 1px dashed #ccc;
            padding-top: 6px;
        }
        
        .qr-label {
            font-size: 8px;
            color: #666;
            margin-bottom: 3px;
        }
        
        .clear {
            clear: both;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>';

        $pageCount = 0;
        foreach ($studentPages as $studentsOnPage) {
            $pageCount++;
            
            // Add page break for subsequent pages
            if ($pageCount > 1) {
                $html .= '<div class="page-break"></div>';
            }
            
            $html .= '<div class="student-grid">';
            
            $studentCount = 0;
            foreach ($studentsOnPage as $student) {
                if ($studentCount % 2 === 0) {
                    $html .= '<div class="student-row">';
                }
                
                $position = ($studentCount % 2 === 0) ? 'left' : 'right';
                $html .= $this->generateStudentCard($student, $youtubeUrl, $position);
                $studentCount++;
                
                if ($studentCount % 2 === 0 || $studentCount === $studentsOnPage->count()) {
                    $html .= '<div class="clear"></div></div>';
                }
            }
            
            $html .= '</div>';
        }
        
        $html .= '</body></html>';
        
        return $html;
    }
    
    /**
     * Generate HTML for a single student card
     *
     * @param object $student
     * @param string $youtubeUrl
     * @return string
     */
    private function generateStudentCard($student, string $youtubeUrl, $position = 'left'): string
    {
        // Generate QR code
        $qrCodeContent = $this->generateQrCode($student, $youtubeUrl);
        
        // Get student name
        $studentName = $student->full_name ?? $student->name ?? 'Unknown Student';
        
        $positionClass = $position === 'left' ? 'left' : 'right';
        
        return '<div class="student-card ' . $positionClass . '">
            <div class="student-name">' . htmlspecialchars($studentName, ENT_QUOTES, 'UTF-8') . '</div>
            
            <div class="student-info">
                <div class="info-item">
                    <span class="info-label">اسم المستخدم:</span>
                    <span class="info-value">' . htmlspecialchars($student->username) . '</span>
                </div>
                <div class="info-item">
                    <span class="info-label">كلمة المرور:</span>
                    <span class="info-value">' . htmlspecialchars($student->username) . '</span>
                </div>
            </div>
            
            ' . $qrCodeContent . '
        </div>';
    }
    
    /**
     * Generate QR code for student login
     *
     * @param object $student
     * @param string $youtubeUrl
     * @return string
     */
    private function generateQrCode($student, string $youtubeUrl): string
    {
        try {
            $qrCodeGenerator = new \SimpleSoftwareIO\QrCode\Generator;
            
            // QR code contains only the YouTube URL for direct access
            $qrData = $youtubeUrl;
            
            $qrCode = $qrCodeGenerator
                ->format('svg')
                ->size(60)
                ->generate($qrData);
            
            $base64QrCode = base64_encode($qrCode);
            
            return '<div class="qr-section">
                <div class="qr-label">QR امسح رمز ال</div>
                <img src="data:image/svg+xml;base64,' . $base64QrCode . '" 
                     alt="QR Code" 
                     style="width: 60px; height: 60px; border: 1px solid #ddd;">
            </div>';
            
        } catch (Exception $e) {
            return '<div class="qr-section">
                <div class="qr-label">QR امسح رمز ال </div>
                <div style="width: 60px; height: 60px; border: 2px solid #007bff; 
                           background: #f8f9fa; display: inline-block; 
                           text-align: center; line-height: 60px; font-size: 8px;">
                    QR Error
                </div>
            </div>';
        }
    }
    
    /**
     * Generate PDF using mPDF
     *
     * @param string $html
     * @return string
     */
    private function generatePdf(string $html): string
    {
        try {
            // Configure mPDF with Arabic support
            $config = [
                'mode' => 'utf-8',
                'format' => 'A4',
                'default_font_size' => 14,
                'default_font' => 'dejavusans',
                'autoScriptToLang' => true,
                'autoLangToFont' => true,
                'autoArabic' => true,
            ];
            
            $mpdf = new Mpdf($config);
            
            // Enable better CSS support
            $mpdf->SetDisplayMode('fullpage');
            
            // Write the HTML content
            $mpdf->WriteHTML($html);
            
            // Return the PDF as string
            return $mpdf->Output('', 'S');
            
        } catch (Exception $e) {
            // Log error if Log is available
            if (class_exists('\Log')) {
                \Log::error('mPDF generation failed: ' . $e->getMessage());
            }
            throw new Exception('Failed to generate PDF: ' . $e->getMessage());
        }
    }
} 
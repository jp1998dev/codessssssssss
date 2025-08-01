    <?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;

class PrintController extends Controller
{

    function convertAmountToWords($amount)
    {
        $formatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
        $whole = floor($amount);
        $decimal = round(($amount - $whole) * 100);

        $words = $formatter->format($whole);
        if ($decimal > 0) {
            $words .= ' and ' . $formatter->format($decimal) . ' centavos';
        }
        return ucfirst($words);
    }


    public function reprintReceipt(Request $request)
    {
        try {
            // dd($request->all());
            $sourceFile = storage_path('app/templates/receipt_template.docx');
            if (!file_exists($sourceFile)) {
                throw new \Exception("Template not found: $sourceFile");
            }

            $tmpPath = storage_path('app/temp_receipt.docx');
            copy($sourceFile, $tmpPath);

            $replacements = [
                'date'            => $request->input('date', ''),
                'name'            => $request->input('name', ''),
                'school_year'     => $request->input('schoolYear', ''),
                'student_number'  => $request->input('student_number', ''),
                'amount'          => $request->input('amount', ''),
                'remarks'         => $request->input('remarks', ''),
                'cashier'         => $request->input('cashier', ''),
                'balance'          => $request->input('balance', ''),
                'word'             => $this->convertAmountToWords($request->input('amount', '')),
            ];

            $zip = new \ZipArchive;
            if ($zip->open($tmpPath) === true) {
                $xml = $zip->getFromName('word/document.xml');

                foreach ($replacements as $placeholder => $value) {
                    $xml = str_replace($placeholder, $value, $xml);
                }

                $zip->addFromString('word/document.xml', $xml);
                $zip->close();
            } else {
                throw new \Exception("Could not open DOCX file");
            }

            $newFile = 'edited_receipt_' . time() . '.docx';
            Storage::disk('local')->put($newFile, file_get_contents($tmpPath));
            $filePath = Storage::disk('local')->path($newFile);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Reprint error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    // public function reprintReceipt(Request $request)
    // {
    //     try {
    //         function convertAmountToWords($amount)
    //         {
    //             $formatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
    //             $whole = floor($amount);
    //             $decimal = round(($amount - $whole) * 100);

    //             $words = $formatter->format($whole);
    //             if ($decimal > 0) {
    //                 $words .= ' and ' . $formatter->format($decimal) . ' centavos';
    //             }
    //             return ucfirst($words);
    //         }

    //         $templatePath = storage_path('app/templates/receipt_template.xlsx'); 
    //         $spreadsheet = IOFactory::load($templatePath);
    //         $sheet = $spreadsheet->getActiveSheet();

    //         $sheet->setCellValue('B2', $request->date);
    //         $sheet->setCellValue('B3', $request->student_number);
    //         $sheet->setCellValue('B4', $request->name);
    //         $sheet->setCellValue('B5', '1ST SEM SY ' . $request->school_year);
    //         $sheet->setCellValue('B6', strtoupper($request->remarks));
    //         $sheet->setCellValue('B7', '₱' . number_format($request->amount, 2));
    //         $sheet->setCellValue('B8', strtoupper(convertAmountToWords($request->amount)) . ' PESOS ONLY');
    //         $sheet->setCellValue('B9', $request->cashier);

    //         IOFactory::registerWriter('Pdf', PdfWriter::class);
    //         $writer = IOFactory::createWriter($spreadsheet, 'Pdf');

    //         return response()->streamDownload(function () use ($writer) {
    //             $writer->save('php://output');
    //         }, 'receipt.pdf', [
    //             'Content-Type' => 'application/pdf'
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error('Reprint receipt error: ' . $e->getMessage() . ' for student ' . $request->name);
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }

    public function printReceipt(Request $request)
    {
        $data = [
            'name' => $request->query('name', 'N/A'),
            'amount' => $request->query('amount', '0.00'),
            'date' => $request->query('date', now()->format('Y-m-d')),
            'remarks' => $request->query('remarks', ''),
            'student_number' => $request->query('student_number', ''),
            'amount_words' => $request->query('amount_words', ''),
            'balance' => $request->query('balance', '0.00'),
            'cashier' => $request->query('cashier', ''),
            'sem_sy' => $request->query('sem_sy', ''),
        ];


        $templatePath = resource_path('templates/name2.docx');
        if (!file_exists($templatePath)) {
            abort(404, 'Template not found');
        }


        $template = new TemplateProcessor($templatePath);
        foreach ($data as $key => $value) {
            $template->setValue($key, $value);
        }


        $docxPath = storage_path('app/temp_receipt.docx');
        $template->saveAs($docxPath);


        $phpWord = IOFactory::load($docxPath);
        $pdfWriter = IOFactory::createWriter($phpWord, 'PDF');
        $pdfPath = storage_path('app/temp_receipt.pdf');
        $pdfWriter->save($pdfPath);

        return response()->file($pdfPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="receipt.pdf"'
        ]);
    }
}

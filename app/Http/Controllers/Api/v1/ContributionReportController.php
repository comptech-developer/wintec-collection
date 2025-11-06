<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class ContributionReportController extends Controller
{
    //
   public function generatePdfPost(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'branch' => 'nullable|string',
            'date' => 'nullable|date',
        ]);
        
        // Setup temp directory
        $this->setupTempDirectory();
        
        // Get data based on request parameters
        $data = [
            'title' => 'BIKIRA MARIA WA LURD KIDIMU',
            'date_on' => now()->format('F d, Y'),
            'station' => DB::table('branch')->where('id',$validated['branch'])->first()->branch ?? null,
            'date' => $validated['date'] ?? null,
            'items' => $this->getReportData($validated),
            'digitals' => $this->digitalPayment($validated['date'],$validated['branch']),
            'physicals' => $this->physicalPayment($validated['date'],$validated['branch']),
            'total' => 0,
            'totalP' => 0,
            'totalD' => 0
        ];
        Log::info('branch' .$validated['branch']);
        
        // Calculate total
        $data['totalP'] = collect($data['physicals'])->sum('paid');
        $data['totalD'] = collect($data['digitals'])->sum('amount');
        
        // Generate PDF
        $pdf = Pdf::loadView('report.pdf.pdfreport', $data)
            ->setPaper('a4', 'portrait')
            ->setOption('temp_dir', storage_path('app/temp'));
        
        // Return as response
        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, 'report-' . now()->format('Y-m-d-His') . '.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }


      private function getReportData($filters)
    {
        // Replace with your actual database query
        // Example:
        // return DB::table('sales')
        //     ->whereBetween('created_at', [$filters['date_from'], $filters['date_to']])
        //     ->get();
        
        // Sample data for demonstration
        return [
            ['name' => 'Product A', 'quantity' => 10, 'price' => 100],
            ['name' => 'Product B', 'quantity' => 5, 'price' => 200],
            ['name' => 'Product C', 'quantity' => 8, 'price' => 150],
        ];
    }

    private function digitalPayment($doj=null,$branch=null)
    {
      
        // ==========================
        // DIGITAL PAYMENTS QUERY
        // ==========================
        $digitalQuery = DB::table('payments as p')
            ->leftJoin('student as s', 'p.utilityref', '=', 's.refno')
            ->where('p.payment_status', 'completed');

        // Optional branch filter
        if (!empty($branch)) {
            $digitalQuery->where('s.branch_id', $branch);
        }

        // Optional month filter
        if (!empty($doj)) {
            $digitalQuery->whereRaw("DATE_FORMAT(p.created_at, '%Y-%m') = ?", [$doj]);
        }

        // Order by creation date descending
        $digitalpayments = $digitalQuery->orderBy('p.created_at', 'desc')->get();

        return $digitalpayments;

    }

    private function physicalPayment($doj=null,$branch=null)
    {
         
      
    // ==========================
    // FEES TRANSACTION QUERY
    // ==========================
    $feesQuery = DB::table('fees_transaction as f')
        ->join('student', 'student.refno', '=', 'f.refno')
        ->join('jumuiya', 'jumuiya.id', '=', 'student.jumuiya_id');

    // Optional branch filter
    if (!empty($branch)) {
        $feesQuery->where('jumuiya.branch_id', $branch);
    }
    // Optional month filter
    if (!empty($doj)) {
        $feesQuery->whereRaw("DATE_FORMAT(f.submitdate, '%Y-%m') = ?", [$doj]);
    }
    // Order by date
    $payments = $feesQuery->orderByRaw('UNIX_TIMESTAMP(f.submitdate)')->get();
     
    return $payments;

    }

    //temp files
    private function setupTempDirectory()
    {
        $tempDir = storage_path('app/temp');
        
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }
        
        putenv('TMPDIR=' . $tempDir);
    }
}

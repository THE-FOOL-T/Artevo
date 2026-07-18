<?php

namespace App\Jobs;

use App\Models\Certificate;
use App\Services\CertificateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GenerateCertificatePdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Certificate $certificate)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(CertificateService $service): void
    {
        // Generate the binary PDF string
        $pdfData = $service->renderPdf($this->certificate);

        // Store the PDF securely on the local disk (not public)
        $path = "certificates/{$this->certificate->serial}.pdf";
        Storage::disk('local')->put($path, $pdfData);
    }
}

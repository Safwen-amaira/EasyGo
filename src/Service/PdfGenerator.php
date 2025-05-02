<?php
namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\String\Slugger\SluggerInterface;

class PdfGenerator
{
    private $slugger;
    private $pdfDir;

    public function __construct(SluggerInterface $slugger, string $pdfDir)
    {
        $this->slugger = $slugger;
        $this->pdfDir = $pdfDir;
    }

    public function generatePdfFromHtml(string $html, string $filename = 'document'): string
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $safeFilename = $this->slugger->slug($filename);
        $pdfPath = $this->pdfDir . '/' . $safeFilename . '-' . uniqid() . '.pdf';

        file_put_contents($pdfPath, $dompdf->output());

        return $pdfPath;
    }
}

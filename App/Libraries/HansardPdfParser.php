<?php

namespace App\Libraries;

use setasign\Fpdi\Fpdi;

class HansardPdfParser
{
    /**
     * Extracts text from a Hansard PDF retrieved from the provided URL.
     *
     * @param string $url The URL of the Hansard PDF JSON data.
     * @return string The extracted text from the PDF.
     * @throws \Exception If the PDF data is not found or the URL is malformed.
     */
    public function extractText(string $url): string
    {
        // 1. Fetch the JSON data from the URL
        $jsonData = @file_get_contents($url); // @ suppresses potential warnings

        // 2. Check if the JSON data was fetched successfully
        if ($jsonData === false) {
            throw new \Exception('Failed to fetch Hansard PDF data. The URL might be malformed.');
        }

        // 3. Decode the JSON data
        $data = json_decode($jsonData, true);

        // 4. Check if the JSON data is valid and contains the 'Data' key
        if ($data === 'null') {
            throw new \Exception('Hansard PDF data not found. The record might not exist.');
        } elseif (!isset($data['Data'])) {
            throw new \Exception('Invalid Hansard PDF data format. The URL might be malformed.');
        }

        // 5. Extract the base64 encoded PDF
        $base64Pdf = $data['Data'];

        // 6. Initialize FPDI
        $pdf = new Fpdi();

        // 7. Load the PDF from the base64 data
        $pageCount = $pdf->setSourceFile(base64_decode($base64Pdf));

        // 8. Extract text from each page
        $text = '';
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $pdf->AddPage();
            $pdf->useTemplate($pdf->importPage($pageNo));
            $text .= $pdf->ExtractText();
        }

        return $text;
    }
}

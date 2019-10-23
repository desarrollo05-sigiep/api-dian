<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Stenfrank\UBL21dian\Templates\SOAP\GetStatus;
use Stenfrank\UBL21dian\Templates\SOAP\GetStatusZip;

class StateController extends Controller
{
    /**
     * Zip.
     *
     * @param string $trackId
     *
     * @return array
     */
    public function zip($trackId)
    {
        // User
        $user = auth()->user();
        $billData = [];
        $filename = '';
        $xmlFile = '';
        $statusCode = 66;

        $getStatusZip = new GetStatusZip($user->company->certificate->path, $user->company->certificate->password);
        $getStatusZip->trackId = $trackId;

        $response = $getStatusZip->signToSend()->getResponseToObject();
        if(!empty($response)) {
            $statusCode = $response->Envelope->Body->GetStatusZipResponse->GetStatusZipResult->DianResponse->StatusCode;

            if ((float)$statusCode == 00) {
                $filename = $response->Envelope->Body->GetStatusZipResponse->GetStatusZipResult->DianResponse->XmlFileName;
                $storagePath = storage_path('app/xml/' . $user->company->id . '/' . $filename . '.xml');
                $xmlFile = File::get($storagePath);

                $xmlDom = new \DOMDocument();
                $xmlDom->loadXML($xmlFile);
                foreach ($xmlDom->getElementsByTagNameNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'UUID') as $currency) {
                    $billData['CUFE'] = $currency->nodeValue;
                }
                foreach ($xmlDom->getElementsByTagNameNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'IssueDate') as $currency) {
                    $billData['issueDate'] = $currency->nodeValue;
                }
                foreach ($xmlDom->getElementsByTagNameNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'IssueTime') as $currency) {
                    $billData['issueTime'] = $currency->nodeValue;
                }
            }
        }

        return [
            'message' => 'Consulta generada con éxito',
            'ResponseDian' => $getStatusZip->signToSend()->getResponseToObject(),
            'fileName' => $filename.'.xml',
            'content' => $xmlFile,
            'statusCode' => $statusCode,
            'billData' => $billData
        ];
    }

    /**
     * Document.
     *
     * @param string $trackId
     *
     * @return array
     */
    public function document($trackId)
    {
        // User
        $user = auth()->user();

        $getStatus = new GetStatus($user->company->certificate->path, $user->company->certificate->password);
        $getStatus->trackId = $trackId;

        return [
            'message' => 'Consulta generada con éxito',
            'ResponseDian' => $getStatus->signToSend()->getResponseToObject(),
        ];
    }
}

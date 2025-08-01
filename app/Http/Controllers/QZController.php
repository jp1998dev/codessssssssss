<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QZController extends Controller
{
    public function sign(Request $request)
    {
        $data = $request->input('request');

        $privateKey = Storage::get('qz/qz-private-key.pem');
        $pkeyid = openssl_get_privatekey($privateKey);

        openssl_sign($data, $signature, $pkeyid);
        openssl_sign($data, $signature, $pkeyid);

        return base64_encode($signature);
    }

    public function cert()
    {
        $cert = Storage::get('qz/qz-public-cert.pem');
        return response($cert, 200, ['Content-Type' => 'text/plain']);
    }
}

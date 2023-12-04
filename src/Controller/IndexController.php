<?php

namespace App\Controller;

use Smalot\PdfParser\Parser;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController
{
    #[Route('/gethalid', name: 'app_index')]
    public function number(Request $request): Response
    {
        $pdf_url = $request->query->get('url');
        try {
            $httpclient = HttpClient::create();
            $response = $httpclient->request('GET', $pdf_url);
            $content = $response->getContent();
        } catch (\Exception $e) {
            if ($response->getStatusCode() == 403) {
                $pdf_url = preg_replace("/^(https?):\/\/([^\.]*)\./", "$1://hal.", $pdf_url);
                $response = $httpclient->request('GET', $pdf_url);
                $content = $response->getContent();
            }
        }
        $parser = new Parser();
        $text = $parser->parseContent($content);

        $lines = preg_split("/\n/", $text->getText());
        $halidFound = false;
        $canonicUrl = "";
        foreach ($lines as $line) {
            if ($halidFound == true ) {
                $canonicUrl = $line;
                break;
            } elseif (preg_match("/^HAL Id/", $line)) {
                $halidFound = true;
            }
        }
        return new Response($canonicUrl);
    }
}
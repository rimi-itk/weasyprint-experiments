<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Environment;

class PrintService
{
    public function __construct(
        private Environment $twig,
        private HttpClientInterface $httpClient,
        private array $weasyPrintRestOptions
    ) {
    }

    public function print(string $template): string {
        $html = $this->twig->render('test.html.twig', [
            'tabular_data' => [
                'header' => [
                    ['Name', 'Number'],
                ],
                'footer' => [
                    ['Total', 7],
                ],
                'rows' => [
                    ['A', 1],
                    ['B', 2],
                    ['C', 4],
                ],
            ],
        ]);

        $url = sprintf('http://%s:%s/api/v1.0/print', $this->weasyPrintRestOptions['host'],
            $this->weasyPrintRestOptions['port']);
        $response = $this->httpClient->request('POST', $url, [
            'body' => [
                'html' => $html,
                'template' => $template,
            ]
        ]);

        if (Response::HTTP_OK === $response->getStatusCode()) {
            return $response->getContent();
        }

        throw new \RuntimeException('Error printing');
    }
}

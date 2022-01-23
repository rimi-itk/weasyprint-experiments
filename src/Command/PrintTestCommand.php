<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Environment;

#[AsCommand(
    name: 'app:print:test',
    description: 'Add a short description for your command',
)]
class PrintTestCommand extends Command
{
    public function __construct(
        private Environment $twig,
        private HttpClientInterface $httpClient,
        private Filesystem $filesystem,
        private array $weasyPrintRestOptions
    ) {
        parent::__construct(null);
    }

    protected function configure()
    {
        $this->addOption('template', null, InputOption::VALUE_REQUIRED, 'The template to use', 'default');
        $this->addOption('dump-html', null, InputOption::VALUE_NONE, 'Dump generated HTML to stdout');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $template = $input->getOption('template');

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
        if ($input->getOption('dump-html')) {
            $io->write($html);
        }

//        $url = sprintf('http://%s:%s/api/v1.0/health', $this->weasyPrintRestOptions['host'], $this->weasyPrintRestOptions['port']);

        $url = sprintf('http://%s:%s/api/v1.0/print', $this->weasyPrintRestOptions['host'],
            $this->weasyPrintRestOptions['port']);
        $response = $this->httpClient->request('POST', $url, [
            'body' => [
                'html' => $html,
                'template' => $template,
            ]
        ]);

        if (Response::HTTP_OK === $response->getStatusCode()) {
            $filename = __DIR__ . '/../../test.pdf';
            $this->filesystem->dumpFile($filename, $response->getContent());
            $io->writeln(sprintf('PDF written to %s', realpath($filename)));
        }

        return Command::SUCCESS;
    }
}

<?php

namespace App\Controller;

use App\Service\PrintService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PrintController extends AbstractController
{
    public function __construct(private PrintService $printService)
    {
    }

    #[Route('/print', name: 'print')]
    public function index(Request $request): Response
    {
        $templates = ['default', 'demo'];
        $form = $this->createFormBuilder()
            ->add('template', ChoiceType::class, [
                'choices' => array_combine($templates, $templates)
            ])
            ->add('Print', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $template = $form->get('template')->getData();
            $pdf = $this->printService->print($template);

            return new Response($pdf, Response::HTTP_OK, ['content-type' => 'application/pdf']);
        }

        return $this->renderForm('print/index.html.twig', [
            'form' => $form,
        ]);
    }
}

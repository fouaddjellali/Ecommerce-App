<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ErrorController extends AbstractController
{
    #[Route('/error/{code}', name: 'app_error')]
    public function show(\Throwable $exception): Response
    {
        $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;

        if ($statusCode === 404) {
            return $this->render('bundles/TwigBundle/Exception/error404.html.twig', [], new Response('', 404));
        }

        return new Response('Une erreur est survenue.', $statusCode);
    }
}

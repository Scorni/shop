<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
    #[Route('/error', name: 'app_error')]
    public function index(): Response
    {
        return $this->render('error/index.html.twig', [
            'error' => '404 Found',
        ]);
    }
    #[Route('/error/eligi', name: 'app_error_eligi')]
    public function wrongEligibility(): Response
    {
        return $this->render('error/index.html.twig', [
            'error' => 'You are not supposed to be there !',
        ]);
    }
}

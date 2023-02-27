<?php

namespace App\Controller;

use App\Service\CacheService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    public function __construct(private readonly CacheService $storageService)
    {
    }

    #[Route('/', name: 'app_homepage')]
    public function index(): Response
    {
        $document1 = ['doc1' => 'test doc 1'];

        $this->storageService->persist(1, $document1);

        $result = $this->storageService->fetch(1);

        return $this->render('homepage/index.html.twig', [
            'controller_name' => 'HomepageController',
        ]);
    }
}

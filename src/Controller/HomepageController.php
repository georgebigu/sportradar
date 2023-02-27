<?php

namespace App\Controller;


use App\Service\GameService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    public function __construct(private readonly GameService $gameService) {
    }

    #[Route('/', name: 'app_homepage')]
    public function index(): Response
    {
        $gamesList = $this->gameService->getGamesList();

//        $this->storageService->persist(1, $document1);

        return $this->render('homepage/list.html.twig', [
            'controller_name' => 'HomepageController',
        ]);
    }
}

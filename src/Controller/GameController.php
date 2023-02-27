<?php

namespace App\Controller;

use App\Entity\Game;
use App\Form\GameType;
use App\Service\GameService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    public function __construct(private readonly GameService $gameService)
    {
    }

    #[Route('/add-game', name: 'add_game')]
    public function addGame(Request $request): Response
    {
        $game = new Game();
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Game $formData */
            $formData = $form->getData();
            switch ($request->getMethod()) {
                case 'POST':
                    if(true === $this->gameService->addGame($formData)) {
                        return $this->redirectToRoute('app_homepage');
                    }
                    $form->addError(new FormError('There was an error while adding the game'));
                    break;
                default:
                    break;
            }
        }

        return $this->render('game/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/update-game', name: 'update_game')]
    public function updateGame(Request $request): Response
    {
        $game = new Game();
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        return $this->render('game/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

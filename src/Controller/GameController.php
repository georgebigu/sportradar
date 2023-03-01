<?php

namespace App\Controller;

use App\Entity\Game;
use App\Exception\CacheException;
use App\Form\GameType;
use App\Service\GameService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    private const ADD_GAME_ERROR = 'There was an error while adding the game.';
    private const UPDATE_GAME_ERROR = 'There was an error while updating the game.';

    public function __construct(private readonly GameService $gameService)
    {
    }

    #[Route('/manage-games', name: 'manage_games')]
    public function index(string $error = ''): Response
    {
        $gamesList = $this->gameService->getGamesList();

        return $this->render('game/list.html.twig', [
            'games'           => $gamesList,
            'error'           => $error
        ]);
    }

    #[Route('/add-game', name: 'add_game')]
    public function addGame(Request $request): Response
    {
        $form = $this->createForm(GameType::class, new Game());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if(true !== $this->gameService->addGame($form->getData())) {
                return $this->redirectToRoute('manage_games');
            }
            $form->addError(new FormError(self::ADD_GAME_ERROR));
        }

        return $this->render('game/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/update-game/{homeTeam}/{awayTeam}', name: 'update_game')]
    public function updateGame(string $homeTeam, string $awayTeam, Request $request): Response
    {
        $form = $this->createForm(GameType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (true === $this->gameService->updateGame($form->getData())) {
                return $this->redirectToRoute('manage_games');
            }
            $form->addError(new FormError(self::UPDATE_GAME_ERROR));
        }

        try {
            $game = $this->gameService->fetchGame($homeTeam, $awayTeam);
            $form->setData($game);
        } catch (CacheException $exception) {
            $form->setData(new Game());
            $form->addError(new FormError($exception->getMessage()));
        }

        return $this->render('game/update.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/finish-game/{homeTeam}/{awayTeam}', name: 'finish_game')]
    public function finishGame(string $homeTeam, string $awayTeam): Response
    {
        $error = '';
        try {
            $this->gameService->finishGame($homeTeam, $awayTeam);
        } catch (CacheException $exception) {
            $error = $exception->getMessage();
        }

        return $this->redirectToRoute('manage_games', ['error' => $error]);
    }

    #[Route('/start-game/{homeTeam}/{awayTeam}', name: 'start_game')]
    public function startGame(string $homeTeam, string $awayTeam): Response
    {
        $error = '';
        try {
            $this->gameService->startGame($homeTeam, $awayTeam);
        } catch (CacheException $exception) {
            $error = $exception->getMessage();
        }

        return $this->redirectToRoute('manage_games', ['error' => $error]);
    }
}

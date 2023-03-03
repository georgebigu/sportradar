<?php

declare(strict_types=1);

namespace unitary;

use App\Entity\Game;
use App\Exception\CacheException;
use App\Service\CacheService;
use App\Service\GameService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GameServiceTest extends TestCase
{
    private CacheService|MockObject $cacheService;
    private GameService $gameService;

    public function setUp(): void
    {
        $this->cacheService = $this->createMock(CacheService::class);
        $this->gameService = new GameService($this->cacheService);
    }

    /** @test */
    public function it_adds_a_new_game(): void
    {
        //arrange
        $game = new Game('HomeTeam', 'AwayTeam', 3, 4);
        $this->cacheService->method('fetch')->willReturn(false);
        $this->cacheService->expects(self::once())->method('persist');

        //act
        $response = $this->gameService->addGame($game);

        //assert
        self::assertTrue($response);
        self::assertEquals(7, $game->getTotalScore());
    }

    /** @test */
    public function it_returns_error_when_adding_an_existing_game(): void
    {
        //arrange
        $game = new Game('HomeTeam', 'AwayTeam', 3, 4);
        $this->cacheService->method('fetch')->willReturn(true);
        $this->cacheService->expects(self::never())->method('persist');

        //assert
        self::expectException(CacheException::class);

        //act
        $this->gameService->addGame($game);
    }

    /** @test */
    public function it_returns_error_when_adding_a_new_game(): void
    {
        //arrange
        $game = new Game('HomeTeam', 'AwayTeam', 3, 4);
        $this->cacheService->method('fetch')->willReturn(true);
        $this->cacheService->method('persist')
            ->willThrowException(new CacheException());

        //assert
        self::expectException(CacheException::class);

        //act
        $this->gameService->addGame($game);
    }

    /** @test */
    public function it_updates_a_game(): void
    {
        //arrange
        $game = new Game('HomeTeam', 'AwayTeam', 3, 4);
        $this->cacheService->method('fetch')->willReturn(true);
        $this->cacheService->expects(self::once())->method('persist');

        //act
        $response = $this->gameService->updateGame($game);

        //assert
        self::assertTrue($response);
        self::assertEquals(7, $game->getTotalScore());
    }

    /** @test */
    public function it_returns_error_when_updating_an_nonexistent_game(): void
    {
        //arrange
        $game = new Game('HomeTeam', 'AwayTeam', 3, 4);
        $this->cacheService->method('fetch')->willReturn(false);
        $this->cacheService->expects(self::never())->method('persist');

        //assert
        self::expectException(CacheException::class);

        //act
        $this->gameService->updateGame($game);
    }

    /** @test */
    public function it_returns_error_when_updating_a_new_game(): void
    {
        //arrange
        $game = new Game('HomeTeam', 'AwayTeam', 3, 4);
        $this->cacheService->method('fetch')->willReturn(true);
        $this->cacheService->method('persist')
            ->willThrowException(new CacheException());

        //assert
        self::expectException(CacheException::class);

        //act
        $this->gameService->updateGame($game);
    }

    /** @test */
    public function it_starts_a_game(): void
    {
        //arrange
        $game = new Game('HomeTeam', 'AwayTeam', 3, 4, true);
        $this->cacheService->method('fetch')->willReturn($game);

        //act
        $this->gameService->startGame('HomeTeam', 'AwayTeam');

        //assert
        self::assertFalse($game->isFinished());
    }

    /** @test */
    public function it_returns_error_when_trying_to_start_a_game(): void
    {
        //arrange
        $game = new Game('HomeTeam', 'AwayTeam', 3, 4, true);
        $this->cacheService->method('fetch')->willReturn($game);
        $this->cacheService->method('persist')->willThrowException(new CacheException());

        //assert
        self::expectException(CacheException::class);

        //act
        $this->gameService->startGame('HomeTeam', 'AwayTeam');
    }

    /** @test */
    public function it_finishes_a_game(): void
    {
        //arrange
        $game = new Game('HomeTeam', 'AwayTeam', 3, 4, false);
        $this->cacheService->method('fetch')->willReturn($game);

        //act
        $this->gameService->finishGame('HomeTeam', 'AwayTeam');

        //assert
        self::assertTrue($game->isFinished());
    }

    /** @test */
    public function it_returns_error_when_trying_to_finish_a_game(): void
    {
        //arrange
        $game = new Game('HomeTeam', 'AwayTeam', 3, 4, false);
        $this->cacheService->method('fetch')->willReturn($game);
        $this->cacheService->method('persist')->willThrowException(new CacheException());

        //assert
        self::expectException(CacheException::class);

        //act
        $this->gameService->finishGame('HomeTeam', 'AwayTeam');
    }

    /** @test */
    public function it_returns_a_sorted_list_of_games(): void
    {
        //arrange
        $cacheKeys = ['test:Spain_Brazil', 'test:Uruguay_Italy', 'test:Germany_France'];
        $gameOne = new Game('Spain', 'Brazil', 10, 2, false, 12, new \DateTime('2023-03-03 13:15:00'));
        $gameTwo = new Game('Uruguay', 'Italy', 6, 6, false, 12, new \DateTime('2023-03-03 13:16:00'));
        $gameThree = new Game('Germany', 'France', 2, 2, false, 4, new \DateTime('2023-03-03 13:17:00'));
        $this->cacheService->method('getAllKeys')->willReturn($cacheKeys);
        $this->cacheService->method('fetchAll')->willReturn([$gameOne, $gameTwo, $gameThree]);

        //act
        $response = $this->gameService->getGamesList();

        //assert
        self::assertEquals([$gameTwo, $gameOne, $gameThree], $response);
    }

    /** @test */
    public function it_returns_an_empty_list_of_games(): void
    {
        //arrange
        $cacheKeys = ['test:Spain_Brazil', 'test:Uruguay_Italy', 'test:Germany_France'];
        $this->cacheService->method('getAllKeys')->willReturn($cacheKeys);
        $this->cacheService->method('fetchAll')->willThrowException(new CacheException());

        //act
        $response = $this->gameService->getGamesList();

        //assert
        self::assertEquals([], $response);
    }
}

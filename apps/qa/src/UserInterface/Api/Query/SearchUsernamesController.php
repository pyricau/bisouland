<?php

declare(strict_types=1);

namespace Bl\Qa\UserInterface\Api\Query;

use Bl\Qa\Domain\Game\SearchUsernames;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final readonly class SearchUsernamesController
{
    public function __construct(
        private SearchUsernames $searchUsernames,
    ) {
    }

    #[Route('/api/v1/usernames', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        $query = (string) $request->query->get('q', '');

        return new JsonResponse([
            'usernames' => '' !== $query ? $this->searchUsernames->search($query) : [],
        ]);
    }
}

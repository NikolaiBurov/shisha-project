<?php

namespace App\Http\Controllers;

use App\Http\Constants\StatusCodes;
use App\Http\Requests\SearchRequest;
use App\Http\Services\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchApiController extends BaseApiController
{
    /**
     * @var SearchService
     */
    private SearchService $searchService;

    /**
     * @param SearchService $searchService
     */
    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * @param SearchRequest $request
     * @return JsonResponse
     */
    public function search(SearchRequest $request): JsonResponse
    {
        $data = $this->searchService->findResults($request->onlyInRules());

        if (empty($data)) {
            return $this->buildResult(StatusCodes::HTTP_NOT_FOUND, 'Not found', null);
        }
        return $this->buildResult(StatusCodes::HTTP_OK, null, $data);
    }
}

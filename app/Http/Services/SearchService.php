<?php

declare(strict_types=1);

namespace App\Http\Services;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Repositories\FlavourRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class SearchService
{
    /**
     * @var CategoryRepository
     */
    private CategoryRepository $categoryRepository;

    /**
     * @var FlavourRepository
     */
    private FlavourRepository $flavourRepository;

    public function __construct(
        CategoryRepository $categoryRepository,
        FlavourRepository $flavourRepository
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->flavourRepository = $flavourRepository;
    }

    /**
     * Combining flavour results found from categories relation and just by querying the flavours
     * @param string $term
     * @return array
     */
    public function findResults(array $input): array
    {
        $result = new Collection();

        $flavoursFromCategories = $this->findFlavoursFromCategories($input['term']);
        $flavours = $this->flavourRepository->findByTerm($input['term'])->get()->toArray() ?? [];

        //Combining found results from categories->flavours and flavours and excludig null and duplicated results
        $result = $result
            ->concat($flavours)
            ->concat($flavoursFromCategories)
            ->unique('id')
            ->filter(function ($item) {
                return !is_null($item);
            });

        //paginating the results
        return $this->paginate($result, $input['per_page'], $input['current_page']);
    }

    /**
     * Find categories by lowercased term then taking their flavours by relation
     * @param string $term
     * @return array
     */
    protected function findFlavoursFromCategories(string $term): array
    {
        $flavours = [];
        $flavoursFromCategories = $this->categoryRepository->findCategoriesByTerm($term)
            ->chunkMap(
                function (Category $entity) {
                    return $entity
                        ->flavours()
                        ->get()
                        ->toArray();
                },
                20
            );

        foreach ($flavoursFromCategories as $categoryFlavour) {
            $flavours[] = array_shift($categoryFlavour);
        }

        return $flavours;
    }

    /**
     * @param Collection $items
     * @param int $perPage
     * @param int $currentPage
     * @param array $options
     * @return array
     */
    public function paginate(Collection $items, int $perPage, int $currentPage, array $options = []): array
    {
        $page = $currentPage ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        $paginated = (new LengthAwarePaginator(
            $items->forPage($page, $perPage), $items->count(), $perPage, $page, $options
        ))->toArray()['data'];

        return array_values($paginated);
    }

}


<?php

declare(strict_types=1);

namespace App\Controller\Api\Work\Projects;

use App\Controller\Api\PaginationNormalizer;
use App\ReadModel\Work\Projects\Project\Filter;
use App\ReadModel\Work\Projects\Project\ProjectFetcher;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ProjectsController extends AbstractController
{
    private const PER_PAGE = 50;

    private DenormalizerInterface $denormalizer;

    public function __construct(DenormalizerInterface $denormalizer)
    {
        $this->denormalizer = $denormalizer;
    }

    /**
     * @OA\Get(
     *     path="/work/projects",
     *     tags={"Work Projects"},
     *     @OA\Parameter(
     *         name="filter[name]",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         style="form"
     *     ),
     *     @OA\Parameter(
     *         name="filter[status]",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         style="form"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="items", type="array", @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="string"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="status", type="string"),
     *             )),
     *             @OA\Property(property="pagination", ref="#/components/schemas/Pagination"),
     *         )
     *     ),
     *     security={{"oauth2": {"common"}}, "basicAuth"}
     * )
     * @Route("/work/projects", name="work.projects", methods={"GET"})
     */
    public function index(Request $request, ProjectFetcher $projects): Response
    {
        if ($this->isGranted('ROLE_WORK_MANAGE_PROJECTS')) {
            $filter = Filter\Filter::all();
        } else {
            $filter = Filter\Filter::forMember($this->getUser()->getId());
        }

        $filter = $this->denormalizer->denormalize($request->query->all('filter'), Filter\Filter::class, 'array', [
            'object_to_populate' => $filter,
            'ignored_attributes' => ['member'],
        ]);

        $pagination = $projects->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort', 'sort'),
            $request->query->get('direction', 'asc')
        );

        return $this->json([
            'items' => \array_map(static fn (array $item): array => [
                'id' => $item['id'],
                'name' => $item['name'],
                'status' => $item['status'],
            ], (array) $pagination->getItems()),
            'pagination' => PaginationNormalizer::normalize($pagination),
        ]);
    }
}

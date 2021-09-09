<?php

declare(strict_types=1);

namespace App\Controller\Api;

use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Dashboard API",
 *     description="HTTP JSON API",
 * ),
 * @OA\Server(
 *     url="/api"
 * ),
 * @OA\SecurityScheme(
 *     type="oauth2",
 *     securityScheme="oauth2",
 *     @OA\Flow(
 *         flow="implicit",
 *         authorizationUrl="/authorize",
 *         scopes={
 *             "common": "Common"
 *         }
 *     )
 * )
 * @OA\SecurityScheme(
 *     type="http",
 *     securityScheme="basicAuth",
 *     scheme="basic"
 * )
 */
class HomeController extends AbstractController
{
    /**
     * @OA\Get(
     *     path="/",
     *     tags={"API"},
     *     description="API Home",
     *     @OA\Response(
     *         response="200",
     *         description="Success response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string")
     *         )
     *     )
     * )
     * @Route("", name="home", methods={"GET"})
     */
    public function home(): Response
    {
        return $this->json([
            'name' => 'JSON API',
        ]);
    }
}

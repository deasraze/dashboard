<?php

declare(strict_types=1);

namespace App\Controller\Api\Profile;

use App\Model\User\UseCase\Name;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NameController extends AbstractController
{
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * @Route("/profile/name", name="profile.name", methods={"PUT"})
     */
    public function request(Request $request, Name\Handler $handler): Response
    {
        $command = $this->serializer->deserialize($request->getContent(), Name\Command::class, 'json', [
            'object_to_populate' => new Name\Command($this->getUser()->getId()),
            'ignored_attributes' => ['id'],
        ]);
        $violations = $this->validator->validate($command);

        if (\count($violations) > 0) {
            $json = $this->serializer->serialize($violations, 'json');

            return new JsonResponse($json, 400, [], true);
        }

        $handler->handle($command);

        return $this->json([]);
    }
}

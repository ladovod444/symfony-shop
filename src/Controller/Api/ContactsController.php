<?php

namespace App\Controller\Api;

use App\Contacts\Contacts;
use App\Entity\Contacts as ContactsEntity;
use App\Service\ContactsService;
use App\Service\Mailer;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Doctrine\ORM\EntityManagerInterface;

#[OA\Tag(name: "Contacts api")]
#[Route('/api/v1')]
#[Security(name: "Bearer")]
class ContactsController extends AbstractController
{
    public function __construct(
        private readonly ContactsService $contactsService,
    ) {

    }

    #[Route('/send-contacts', name: "api-send-contacts", methods: ["POST"])]
    public function sendContacts(#[MapRequestPayload] Contacts $contacts): JsonResponse
    {
        $contacts_entity = $this->contactsService->createContacts($contacts);

        return $this->json($contacts_entity, Response::HTTP_CREATED);
    }


}
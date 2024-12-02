<?php

namespace App\Controller\Api;

use App\Dto\ProductDto;
use App\Dto\UserDto;
use App\Entity\User;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use OpenApi\Attributes as OA;
#[OA\Tag(name: "Users api")]
#[Route('/api/v1')]
#[Security(name: "Bearer")]
class UserController extends AbstractController
{
    const ITEMS_PER_PAGE = 10;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserService $userService,
    ) {

    }

    #[Route('/user/list', name: 'api-users-list', methods: ['GET'], format: 'json')]
    #[OA\Response(
        response: 200,
        description: 'Returns Users list, yes!!!',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: User::class, groups: ['full']))
        )
    )]
    public function index(Request $request): Response
    {
        $page = $request->get('page', 0);

        // Добавлена простая пагинация.
        if ($page && is_numeric($page)) {
            $offset = ($page - 1) * self::ITEMS_PER_PAGE;
        } else { // Чтобы не выводить все пока выведем по умолчанию только 10
            $offset = 0;
            //$page = 1;
        }

        $users = $this->userService->getUsers($offset, self::ITEMS_PER_PAGE);
        return $this->json($users, Response::HTTP_OK, context: [
            AbstractNormalizer::GROUPS => ['products:api:list'],
        ]);
    }

    #[Route('/user/{user}', name: 'api-user', methods: ['GET'], format: 'json')]
    #[OA\Parameter(
        name: "Accept-Language",
        description: "Set language parameter by RFC2616 <https://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4>",
        in: "header",
//        OA\Schema(
//            type="string"
//        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns User',
        content:  new Model(type: UserDto::class)
    )]
    public function getUserData(User $user): Response
    {
        return $this->json($user, Response::HTTP_OK, context: [
            AbstractNormalizer::GROUPS => ['products:api:list'],
        ]);
    }

    #[Route('/user/email/{email}', name: 'api-user-by-email', methods: ['GET'], format: 'json')]
    #[OA\Parameter(
        name: "Accept-Language",
        description: "Set language parameter by RFC2616 <https://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4>",
        in: "header",
//        OA\Schema(
//            type="string"
//        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns User',
        content:  new Model(type: UserDto::class)
    )]
    public function getUserByEmail(#[MapEntity(mapping: ["email" => "email"])]User $user): Response
    {
        return $this->json($user, Response::HTTP_OK, context: [
            AbstractNormalizer::GROUPS => ['products:api:list'],
        ]);
    }

    #[Route('/user/dto', name: 'api-user-add-dto', methods: ['post'], format: 'json')]
    #[OA\Response(
        response: 200,
        description: 'Create a user',
        content:  new Model(type: ProductDto::class)
    )]
    public function addDto(Request $request, #[MapRequestPayload] UserDto $userDto): Response
    //                         #[MapRequestPayload(
    //                          // acceptFormat: 'json',
    //                          // resolver: 'App\Resolver\ProductResolver',
    //                         )] ProductDto $ProductDto): Response
    {

        $user = $this->userService->createUser($userDto);

        return $this->json($user, Response::HTTP_CREATED, context: [
            AbstractNormalizer::GROUPS => ['products:api:list'],
        ]);
    }

    #[Route('/user/dto/{user}', name: 'api-user-update-dto', methods: ['put'], format: 'json')]
    #[OA\Response(
        response: 200,
        description: 'Update a product',
        content:  new Model(type: UserDto::class)
    )]
    public function updateDto(User $user, #[MapRequestPayload] UserDto $userDto): Response
    {
        $user = $this->userService->updateUser($user, $userDto);
        return $this->json($user, Response::HTTP_OK, context: [
            AbstractNormalizer::GROUPS => ['products:api:list'],
        ]);
    }

    #[Route('/user/{user}', name: 'api-user-delete', methods: ['delete'], format: 'json')]
    #[OA\Response(
        response: 204,
        description: 'Delete user',
//        content:  new Model(type: ProductDto::class)
    )]
//    #[IsGranted('PRODUCT_DELETE', 'product')]
    public function delete(User $user): Response
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->json([], Response::HTTP_NO_CONTENT);
    }

}

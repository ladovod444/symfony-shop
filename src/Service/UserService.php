<?php

namespace App\Service;

use App\Dto\UserDto;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{

    public function __construct(
        private readonly ParameterBagInterface $parameterBag,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    /**
     * @param int $page
     * @param int $offset
     * @return UserDto []
     */
    public function getUsers(int $page = 1, int $offset = 10): array
    {
        $users = $this->userRepository->findBy(
            [],
            ['id' => 'DESC'],
            //limit: self::ITEMS_PER_PAGE,
            limit: $this->parameterBag->get('app:api_per_age'),
            offset: $offset
        );

        if (!$page) {
            $users = $this->userRepository->findBy(
                [],
                ['id' => 'DESC'],
            //limit: self::ITEMS_PER_PAGE,
//                limit: $this->parameterBag->get('app:api_per_age'),
//                offset: $offset
            );
        }
        
        return array_map(
            fn(User $item) => new UserDto(
                $item->getId(),
                $item->getEmail(),
                '',
                $item->getFirstName(),
                $item->getLastName(),

            ),
            $users
        );
    }

    public function createUser(UserDto $userDto): User
    {
        $user = User::createFromDto($userDto, $this->userRepository, $this->userPasswordHasher);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function updateUser(User $user, UserDto $userDto): User
    {
        $user = User::updateFromDto($userDto, $user, $this->userPasswordHasher);
        $this->entityManager->flush();

        return $user;
    }
}
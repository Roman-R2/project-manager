<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp\Request;

use App\Model\Flusher;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\ConfirmTokinizer;
use App\Model\User\Service\ConfirmTokenSender;
use App\Model\User\Service\PasswordHasher;


class Handler
{
    private $users;
    private $hasher;
    private $flusher;
    private $tokinizer;
    private $sender;

    public function __construct(
        UserRepository $users,
        PasswordHasher $hasher,
        ConfirmTokinizer $tokinizer,
        ConfirmTokenSender $sender,
        Flusher $flusher
    )
    {
        $this->users = $users;
        $this->hasher = $hasher;
        $this->tokinizer = $tokinizer;
        $this->sender = $sender;
        $this->flusher = $flusher;
    }

    public function handle (Command $command): void
    {
        $email = new Email($command->email);

        if ($this->users->hasByEmail($email)) {
            throw new \DomainException('User already exists.');
        }



        $user = new User(
            Id::next(),
            new \DateTimeImmutable()
        );

        $user->signUpByEmail(
            $email,
            $this->hasher->hash($command->password),
            $token = $this->tokinizer->generete()
        );

        $this->users->add($user);

        $this->sender->send($email, $token);

        $this->flusher-flush();
    }
}














class Handler0
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function handle(Command $command) : void
    {
        $email = mb_strtolower($command->email);

        if ($this->em->getRepository(User::class)->findOneBy(['email' => $email])){
            throw new \DomainException('User alredy exists');
        }

        $user = new User(
            Uuid::uuid4()->toString(),
            new \DateTimeImmutable(),
            $email,
            password_hash($command->password, PASSWORD_ARGON2I)
        );

        $this->em->persist($user);
        $this->em->flush();

    }
}
<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

class User
{
    private const STATUS_WAIT = 'wait';
    private const STATUS_ACTIVE = 'active';


    /**
     * @var int
     */
    private $id;
    /**
     * @var \DateTimeImmutable
     */
    private $date;
    /**
     * @var string
     */
    private $email;
    /**
     * @var string
     */
    private $passwordHash;
    /**
     * @var string|null
     */
    private $confirmToken;
    /**
     * @var string
     */
    private $status;

    public function __construct(
        Id $id,
        \DateTimeImmutable $date,
        Email $email,
        string $hash,
        string $token
    )
    {
        $this->id = $id;
        $this->date = $date;
        $this->email = $email;
        $this->passwordHash = $hash;
        $this->confirmToken = $token;
        $this->status = self::STATUS_WAIT;
    }

    public function confirmSignUp(): void
    {
        if (!$this->isWait()) {
            throw new \DomainException('User is already confirmed.');
        }

        $this->status = self::STATUS_ACTIVE;
        $this->confirmToken = null;
    }

    public function isWait(): bool
    {
        return $this->status === self::STATUS_WAIT;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getConfirmToken(): ?string
    {
        return $this->confirmToken;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
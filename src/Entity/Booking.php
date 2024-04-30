<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?bool $archived = false;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    private ?Mission $mission = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $ConfirmStarted = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $confirmEnded = null;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $validate = false;

    public function totalHoursPassed(): int
    {
        $dateDebut = $this->getConfirmStarted();
        $dateFin = $this->getConfirmEnded();
        
        $result = $dateDebut->diff($dateFin);

        if (!$result->d) {
            return $result->h;
        }

        return ($result->d * 8) + $result->h;
    }


    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function isArchived(): ?bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): self
    {
        $this->archived = $archived;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getMission(): ?Mission
    {
        return $this->mission;
    }

    public function setMission(?Mission $mission): self
    {
        $this->mission = $mission;

        return $this;
    }

    public function getConfirmStarted(): ?\DateTimeInterface
    {
        return $this->ConfirmStarted;
    }

    public function setConfirmStarted(?\DateTimeInterface $ConfirmStarted): self
    {
        $this->ConfirmStarted = $ConfirmStarted;

        return $this;
    }

    public function getConfirmEnded(): ?\DateTimeInterface
    {
        return $this->confirmEnded;
    }

    public function setConfirmEnded(?\DateTimeInterface $confirmEnded): self
    {
        $this->confirmEnded = $confirmEnded;

        return $this;
    }

    public function isValidate(): ?bool
    {
        return $this->validate;
    }

    public function setValidate(bool $validate): self
    {
        $this->validate = $validate;

        return $this;
    }
}

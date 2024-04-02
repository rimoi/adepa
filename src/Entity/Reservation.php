<?php

namespace App\Entity;

use App\Constant\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?Educatheure $educatheure = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?User $owner = null;

    #[ORM\Column(length: 255)]
    private ?string $status = ReservationType::PENDING;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $note = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateSlot = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $timeSlot = null;

    public function isTerminated(): bool
    {
        if ($this->status === ReservationType::CONFIRMED) {

            $stringDate = sprintf('%s %s', $this->dateSlot->format('d/m/Y'), $this->timeSlot);

            try {
                $dateObj = date_create_from_format('d/m/Y H\\hi', $stringDate);
            } catch (\Exception $e) {
                $dateObj = null;
            }

            if (
                $dateObj
                && ($dateObj <= new \DateTime()))
            {
                return true;
            }
        }

        return false;
    }

    #[ORM\PrePersist]
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt()
    {
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEducatheure(): ?Educatheure
    {
        return $this->educatheure;
    }

    public function setEducatheure(?Educatheure $educatheure): self
    {
        $this->educatheure = $educatheure;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getDateSlot(): ?\DateTimeInterface
    {
        return $this->dateSlot;
    }

    public function setDateSlot(?\DateTimeInterface $dateSlot): self
    {
        $this->dateSlot = $dateSlot;

        return $this;
    }

    public function getTimeSlot(): ?string
    {
        return $this->timeSlot;
    }

    public function setTimeSlot(?string $timeSlot): self
    {
        $this->timeSlot = $timeSlot;

        return $this;
    }
}

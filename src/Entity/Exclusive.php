<?php

namespace App\Entity;

use App\Repository\ExclusiveRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExclusiveRepository::class)]
class Exclusive
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'exclusives')]
    private ?Mission $mission = null;

    #[ORM\ManyToOne(inversedBy: 'exclusives')]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    /*
        Si la personne qui Crée la mission demande des personnes précisément
        sur le formulaire, seuls eux reçoivent les messages MISSION DISPO et si
        mission non prise en 2 jours, l’envoi de la mission est envoyé à toutes les
        personnes ayant le profil
     */
    public function isAlwaysOnTime(): bool
    {
        if ($this->createdAt) {
            $twoDays = clone ($this->createdAt);
            $twoDays = $twoDays->modify('+2 days');

            if ((new \DateTime()) <= $twoDays) {
                return true;
            }
        }

        return false;
    }

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}

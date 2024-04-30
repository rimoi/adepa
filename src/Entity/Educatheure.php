<?php

namespace App\Entity;

use App\Repository\EducatheureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[ORM\Entity(repositoryClass: EducatheureRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Educatheure
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private ?int $price = 30;


    #[ORM\Column(nullable: true)]
    private ?int $numberParticipant = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $publicType = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?bool $archived = false;

    #[ORM\Column(nullable: true)]
    private ?bool $published = false;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(nullable: true)]
    private ?int $zipCode = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $departement = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $noteBooking = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?File $image = null;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'educatheures')]
    private Collection $categories;

    // Tous les personnes ajouté pas l'admin eligible à cette mission
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'elements', cascade: ['persist'])]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: 'educatheure', targetEntity: Reservation::class)]
    private Collection $reservations;

    #[ORM\Column(nullable: true)]
    private ?int $nombreIntervention = null;

    #[ORM\OneToMany(mappedBy: 'educatheur', targetEntity: EducatheureTag::class)]
    private Collection $educatheureTags;

    // Le premier gars qui valide la mission
    #[ORM\ManyToOne]
    private ?User $user = null;


//    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
//    #[Groups(['planning'])]
//    private ?\DateTimeInterface $started = null;
//
//    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
//    #[Groups(['planning'])]
//    private ?\DateTimeInterface $ended = null;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->educatheureTags = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function setSlug()
    {
        $slugger = new AsciiSlugger('fr_FR');

        $this->slug = $slugger->slug(strtolower($this->title)  .'-' . time());

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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getNumberParticipant(): ?int
    {
        return $this->numberParticipant;
    }

    public function setNumberParticipant(?int $numberParticipant): self
    {
        $this->numberParticipant = $numberParticipant;

        return $this;
    }

    public function getPublicType(): ?string
    {
        return $this->publicType;
    }

    public function setPublicType(?string $publicType): self
    {
        $this->publicType = $publicType;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    public function isArchived(): ?bool
    {
        return $this->archived;
    }

    public function setArchived(?bool $archived): self
    {
        $this->archived = $archived;

        return $this;
    }

    public function isPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(?bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getZipCode(): ?int
    {
        return $this->zipCode;
    }

    public function setZipCode(?int $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getNoteBooking(): ?string
    {
        return $this->noteBooking;
    }

    public function setNoteBooking(?string $noteBooking): self
    {
        $this->noteBooking = $noteBooking;

        return $this;
    }

    public function getImage(): ?File
    {
        return $this->image;
    }

    public function setImage(?File $image): void
    {
        $this->image = $image;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->users->removeElement($user);

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setEducatheure($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getEducatheure() === $this) {
                $reservation->setEducatheure(null);
            }
        }

        return $this;
    }

    public function getDepartement(): ?string
    {
        return $this->departement;
    }

    public function setDepartement(?string $departement): void
    {
        $this->departement = $departement;
    }

    public function getNombreIntervention(): ?int
    {
        return $this->nombreIntervention;
    }

    public function setNombreIntervention(?int $nombreIntervention): self
    {
        $this->nombreIntervention = $nombreIntervention;

        return $this;
    }

//    public function getStarted(): ?\DateTimeInterface
//    {
//        return $this->started;
//    }
//
//    public function setStarted(?\DateTimeInterface $started): void
//    {
//        $this->started = $started;
//    }
//
//    public function getEnded(): ?\DateTimeInterface
//    {
//        return $this->ended;
//    }
//
//    public function setEnded(?\DateTimeInterface $ended): void
//    {
//        $this->ended = $ended;
//    }

/**
 * @return Collection<int, EducatheureTag>
 */
public function getEducatheureTags(): Collection
{
    return $this->educatheureTags;
}

public function addEducatheureTag(EducatheureTag $educatheureTag): self
{
    if (!$this->educatheureTags->contains($educatheureTag)) {
        $this->educatheureTags->add($educatheureTag);
        $educatheureTag->setEducatheur($this);
    }

    return $this;
}

public function removeEducatheureTag(EducatheureTag $educatheureTag): self
{
    if ($this->educatheureTags->removeElement($educatheureTag)) {
        // set the owning side to null (unless already changed)
        if ($educatheureTag->getEducatheur() === $this) {
            $educatheureTag->setEducatheur(null);
        }
    }

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


}

<?php

namespace App\Entity;

use App\Repository\EducatheureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
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
    private ?int $price = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $minDuration = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $maxDuration = null;

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
    private array $days = [];

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

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $noteBooking = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?File $image = null;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'educatheures')]
    private Collection $categories;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'elements', cascade: ['persist'])]
    private Collection $users;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->users = new ArrayCollection();
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

    public function getMinDuration(): ?string
    {
        return $this->minDuration;
    }

    public function setMinDuration(?string $minDuration): self
    {
        $this->minDuration = $minDuration;

        return $this;
    }

    public function getMaxDuration(): ?string
    {
        return $this->maxDuration;
    }

    public function setMaxDuration(?string $maxDuration): self
    {
        $this->maxDuration = $maxDuration;

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


    public function getDays(): array
    {
        return $this->days;
    }

    public function setDays(?array $days): self
    {
        $this->days = $days;

        return $this;
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
}

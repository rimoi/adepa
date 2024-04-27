<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: self::class)]
    private ?self $parent = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'categories')]
    private Collection $users;

    #[ORM\ManyToMany(targetEntity: Mission::class, mappedBy: 'categories')]
    private Collection $missions;

    #[ORM\Column(options: ['default' => 0])]
    private ?bool $archived = false;

    #[ORM\ManyToMany(targetEntity: Educatheure::class, mappedBy: 'categories')]
    private Collection $educatheures;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: EducatheureTag::class)]
    private Collection $educatheureTags;


    public function __toString(): string
    {
        return $this->title;
    }

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->users = new ArrayCollection();
        $this->missions = new ArrayCollection();
        $this->educatheures = new ArrayCollection();
        $this->educatheureTags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
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

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(?User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addCategory($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeCategory($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Mission>
     */
    public function getMissions(): Collection
    {
        return $this->missions;
    }

    public function addMission(Mission $mission): self
    {
        if (!$this->missions->contains($mission)) {
            $this->missions->add($mission);
            $mission->addCategory($this);
        }

        return $this;
    }

    public function removeMission(Mission $mission): self
    {
        if ($this->missions->removeElement($mission)) {
            $mission->removeCategory($this);
        }

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

    /**
     * @return Collection<int, Educatheure>
     */
    public function getEducatheures(): Collection
    {
        return $this->educatheures;
    }

    public function addEducatheure(Educatheure $educatheure): self
    {
        if (!$this->educatheures->contains($educatheure)) {
            $this->educatheures->add($educatheure);
            $educatheure->addCategory($this);
        }

        return $this;
    }

    public function removeEducatheure(Educatheure $educatheure): self
    {
        if ($this->educatheures->removeElement($educatheure)) {
            $educatheure->removeCategory($this);
        }

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

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
            $educatheureTag->setCategory($this);
        }

        return $this;
    }

    public function removeEducatheureTag(EducatheureTag $educatheureTag): self
    {
        if ($this->educatheureTags->removeElement($educatheureTag)) {
            // set the owning side to null (unless already changed)
            if ($educatheureTag->getCategory() === $this) {
                $educatheureTag->setCategory(null);
            }
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\MissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MissionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Mission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['planning'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['planning'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['planning'])]
    private ?\DateTimeInterface $started = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['planning'])]
    private ?\DateTimeInterface $ended = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?File $file = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $booked = false;

    #[ORM\Column]
    private bool $archived = false;

    #[ORM\ManyToOne(inversedBy: 'missions')]
    private ?User $user = null;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $published = false;

    #[ORM\OneToMany(mappedBy: 'mission', targetEntity: Booking::class)]
    private Collection $bookings;

    #[ORM\Column(length: 255)]
    #[Groups(['planning'])]

    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    #[Groups(['planning'])]

    private ?string $address = null;

    #[ORM\Column]
    #[Groups(['planning'])]

    private ?int $zipCode = null;

    #[ORM\Column(length: 255)]
    #[Groups(['planning'])]

    private ?string $city = null;

    #[ORM\Column(options: ['default' => 0])]
    private bool $emergency = false;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'missions')]
    private Collection $categories;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\ManyToOne(inversedBy: 'missions')]
    private ?Service $service = null;

    #[ORM\OneToMany(mappedBy: 'mission', targetEntity: Exclusive::class, cascade: ['persist'])]
    private Collection $exclusives;

    private $price;

    public function isPossibleToCancel(): bool
    {
        return $this->started > new \DateTime('+48 hours', new \DateTimeZone('Europe/Paris'));
    }

    // a supprimé 👇
    public function getPrice()
    {
        return $this->price;
    }
    public function setPrice($price)
    {
        $this->price = $price;
    }

    // a supprimé 👆

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->exclusives = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    #[ORM\PrePersist]
    public function setSlug()
    {
        $slugger = new AsciiSlugger('fr_FR');

        $this->slug = $slugger->slug(strtolower($this->title)  .'-' . time());
    }

    public function fullAdresse(): string
    {
        return sprintf('%s, %d %s', $this->address, $this->zipCode, $this->city);
    }
    
    #[ORM\preUpdate]
    public function setUpdatedAt()
    {
        $this->updatedAt = new \DateTimeImmutable();
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }


    public function getStarted(): ?\DateTimeInterface
    {
        return $this->started;
    }

    public function setStarted(\DateTimeInterface $started): self
    {
        $this->started = $started;

        return $this;
    }

    public function getEnded(): ?\DateTimeInterface
    {
        return $this->ended;
    }

    public function setEnded(?\DateTimeInterface $ended): self
    {
        $this->ended = $ended;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function isBooked(): ?bool
    {
        return $this->booked;
    }

    public function setBooked(bool $booked): self
    {
        $this->booked = $booked;

        return $this;
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

    public function isPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->setMission($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getMission() === $this) {
                $booking->setMission(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getZipCode(): ?int
    {
        return $this->zipCode;
    }

    public function setZipCode(int $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function isEmergency(): ?bool
    {
        return $this->emergency;
    }

    public function setEmergency(bool $emergency): self
    {
        $this->emergency = $emergency;

        return $this;
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;

        return $this;
    }

    /**
     * @return Collection<int, Exclusive>
     */
    public function getExclusives(): Collection
    {
        return $this->exclusives;
    }

    public function addExclusife(Exclusive $exclusife): self
    {
        if (!$this->exclusives->contains($exclusife)) {
            $this->exclusives->add($exclusife);
            $exclusife->setMission($this);
        }

        return $this;
    }

    public function removeExclusife(Exclusive $exclusife): self
    {
        if ($this->exclusives->removeElement($exclusife)) {
            // set the owning side to null (unless already changed)
            if ($exclusife->getMission() === $this) {
                $exclusife->setMission(null);
            }
        }

        return $this;
    }


}

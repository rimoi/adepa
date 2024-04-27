<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Ce compte existe déjà ')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deletedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adress = null;

    #[ORM\Column(nullable: true)]
    private ?int $zipCode = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\OneToOne(targetEntity: File::class)]
    private ?File $iban = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $siret = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tva = null;

    #[ORM\OneToOne(targetEntity: File::class)]
    private ?File $cni = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?File $permisConduite = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?File $criminalRecord = null;

    #[ORM\OneToOne(targetEntity: File::class)]
    private ?File $autoentrepriseCertificate = null;


    // si l'email est vériffié
    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    // si le user et activé par un admin
    #[ORM\Column]
    private ?bool $enabled = false;

    // si le user et archivé par un admin
    #[ORM\Column(options: ['default' => false])]
    private bool $archived = false;

    #[ORM\OneToOne(targetEntity: File::class)]
    private ?File $file;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Article::class)]
    private Collection $articles;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Mission::class)]
    private Collection $missions;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Booking::class, orphanRemoval: true)]
    private Collection $bookings;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug = null;

    // ceci est diplome et non experience (c'est qualification )
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Experience::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $experiences;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Qualification::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $qualifications;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $gender = null;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'users')]
    private Collection $categories;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Sms::class)]
    private Collection $sms;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $socialReason = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Service::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $services;

    // à supprimer il n'est plus utilisé
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $public = null;


    #[ORM\ManyToMany(targetEntity: Educatheure::class, mappedBy: 'users', cascade: ['persist'])]
    private Collection $elements;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Reservation::class)]
    private Collection $reservations;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Facture::class)]
    private Collection $factures;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $minDuration = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $maxDuration = null;

    #[ORM\Column(nullable: true)]
    private ?array $days = [];

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Exclusive::class, cascade: ['persist'])]
    private Collection $exclusives;

    public function nickname(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function hasRole(string $role): bool
    {
        return !!in_array($role, $this->roles, true);
    }

    public function __toString(): string
    {
        return $this->email;
    }

    public function fullAddress(): string
    {
        return sprintf('%s, %d %s', $this->adress, $this->zipCode, $this->city);
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

        $this->slug = $slugger->slug(strtolower($this->nickname()) .'-' . time());
    }
    #[ORM\preUpdate]
    public function setUpdatedAt()
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
    #[ORM\PostRemove]
    public function setDeletedAt()
    {
        $this->deletedAt = new \DateTimeImmutable();
    }
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->articles = new ArrayCollection();
        $this->missions = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->experiences = new ArrayCollection();
        $this->qualifications = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->sms = new ArrayCollection();
        $this->services = new ArrayCollection();
        $this->educatheures = new ArrayCollection();
        $this->elements = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->factures = new ArrayCollection();
        $this->exclusives = new ArrayCollection();
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(?string $adress): self
    {
        $this->adress = $adress;

        return $this;
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

    public function getIban(): ?File
    {
        return $this->iban;
    }

    public function setIban(?File $iban): self
    {
        $this->iban = $iban;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): self
    {
        $this->siret = $siret;

        return $this;
    }

    public function getTva(): ?string
    {
        return $this->tva;
    }

    public function setTva(?string $tva): self
    {
        $this->tva = $tva;

        return $this;
    }

    public function isIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getCni(): ?File
    {
        return $this->cni;
    }

    public function setCni(?File $cni): self
    {
        $this->cni = $cni;

        return $this;
    }

    public function getAutoentrepriseCertificate(): ?File
    {
        return $this->autoentrepriseCertificate;
    }

    public function setAutoentrepriseCertificate(?File $autoentrepriseCertificate): self
    {
        $this->autoentrepriseCertificate = $autoentrepriseCertificate;

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

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setUser($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getUser() === $this) {
                $article->setUser(null);
            }
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
            $mission->setUser($this);
        }

        return $this;
    }

    public function removeMission(Mission $mission): self
    {
        if ($this->missions->removeElement($mission)) {
            // set the owning side to null (unless already changed)
            if ($mission->getUser() === $this) {
                $mission->setUser(null);
            }
        }

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
            $booking->setUser($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getUser() === $this) {
                $booking->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Experience>
     */
    public function getExperiences(): Collection
    {
        return $this->experiences;
    }

    public function addExperience(Experience $experience): self
    {
        if (!$this->experiences->contains($experience)) {
            $this->experiences->add($experience);
            $experience->setUser($this);
        }

        return $this;
    }

    public function removeExperience(Experience $experience): self
    {
        if ($this->experiences->removeElement($experience)) {
            // set the owning side to null (unless already changed)
            if ($experience->getUser() === $this) {
                $experience->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Qualification>
     */
    public function getQualifications(): Collection
    {
        return $this->qualifications;
    }

    public function addQualification(Qualification $qualification): self
    {
        if (!$this->qualifications->contains($qualification)) {
            $this->qualifications->add($qualification);
            $qualification->setUser($this);
        }

        return $this;
    }

    public function removeQualification(Qualification $qualification): self
    {
        if ($this->qualifications->removeElement($qualification)) {
            // set the owning side to null (unless already changed)
            if ($qualification->getUser() === $this) {
                $qualification->setUser(null);
            }
        }

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

    /**
     * @return Collection<int, Sms>
     */
    public function getSms(): Collection
    {
        return $this->sms;
    }

    public function addSms(Sms $sms): self
    {
        if (!$this->sms->contains($sms)) {
            $this->sms->add($sms);
            $sms->setUser($this);
        }

        return $this;
    }

    public function removeSms(Sms $sms): self
    {
        if ($this->sms->removeElement($sms)) {
            // set the owning side to null (unless already changed)
            if ($sms->getUser() === $this) {
                $sms->setUser(null);
            }
        }

        return $this;
    }

    public function getSocialReason(): ?string
    {
        return $this->socialReason;
    }

    public function setSocialReason(?string $socialReason): self
    {
        $this->socialReason = $socialReason;

        return $this;
    }



    /**
     * @return Collection<int, Service>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
            $service->setUser($this);
        }

        return $this;
    }

    public function removeService(Service $service): self
    {
        if ($this->services->removeElement($service)) {
            // set the owning side to null (unless already changed)
            if ($service->getUser() === $this) {
                $service->setUser(null);
            }
        }

        return $this;
    }

    public function getPermisConduite(): ?File
    {
        return $this->permisConduite;
    }

    public function setPermisConduite(?File $permisConduite): self
    {
        $this->permisConduite = $permisConduite;

        return $this;
    }

    public function getPublic(): ?string
    {
        return $this->public;
    }

    public function setPublic(?string $public): self
    {
        $this->public = $public;

        return $this;
    }

    /**
     * @return File|null
     */
    public function getCriminalRecord(): ?File
    {
        return $this->criminalRecord;
    }

    public function setCriminalRecord(?File $criminalRecord): void
    {
        $this->criminalRecord = $criminalRecord;
    }

    /**
     * @return Collection<int, Educatheure>
     */
    public function getElements(): Collection
    {
        return $this->elements;
    }

    public function addElement(Educatheure $element): self
    {
        if (!$this->elements->contains($element)) {
            $this->elements->add($element);
            $element->addUser($this);
        }

        return $this;
    }

    public function removeElement(Educatheure $element): self
    {
        if ($this->elements->removeElement($element)) {
            $element->removeUser($this);
        }

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
            $reservation->setOwner($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getOwner() === $this) {
                $reservation->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Facture>
     */
    public function getFactures(): Collection
    {
        return $this->factures;
    }

    public function addFacture(Facture $facture): self
    {
        if (!$this->factures->contains($facture)) {
            $this->factures->add($facture);
            $facture->setUser($this);
        }

        return $this;
    }

    public function removeFacture(Facture $facture): self
    {
        if ($this->factures->removeElement($facture)) {
            // set the owning side to null (unless already changed)
            if ($facture->getUser() === $this) {
                $facture->setUser(null);
            }
        }

        return $this;
    }

    public function getMinDuration(): ?string
    {
        return $this->minDuration;
    }

    public function setMinDuration(?string $minDuration): void
    {
        $this->minDuration = $minDuration;
    }

    public function getMaxDuration(): ?string
    {
        return $this->maxDuration;
    }

    public function setMaxDuration(?string $maxDuration): void
    {
        $this->maxDuration = $maxDuration;
    }

    public function getDays(): array
    {
        return $this->days;
    }

    public function setDays(array $days): void
    {
        $this->days = $days;
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
            $exclusife->setUser($this);
        }

        return $this;
    }

    public function removeExclusife(Exclusive $exclusife): self
    {
        if ($this->exclusives->removeElement($exclusife)) {
            // set the owning side to null (unless already changed)
            if ($exclusife->getUser() === $this) {
                $exclusife->setUser(null);
            }
        }

        return $this;
    }
}
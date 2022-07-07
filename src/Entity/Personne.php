<?php

namespace App\Entity;

use App\Repository\PersonneRepository;
use App\Traits\TimeStampTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;



#[ORM\Entity(repositoryClass: PersonneRepository::class)]
#[ORM\HasLifecycleCallbacks()]

class Personne
{
    use TimeStampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank(message: "veuillez renseigner ce champs")]
    #[Assert\Length(min: 2, minMessage: "Veuillez mettre au moins 2 caractères")]
    #[Assert\Regex('/\[A-Za-z]+/', message: "Veuillez ne pas saisir de nombre")]
    private $nom;

    #[ORM\Column(type: 'string', length: 200)]
    #[Assert\NotBlank(message: "veuillez renseigner ce champs")]
    #[Assert\Length(min: 2, minMessage: "Veuillez mettre au moins 2 caractères")]
    #[Assert\Regex('/\[A-Za-z]+/', message: "Veuillez ne pas saisir de nombre")]
    private $prenom;

    #[ORM\Column(type: 'smallint')]
    #[Assert\Positive]
    #[Assert\NotBlank(message: "veuillez renseigner ce champs")]
    #[Assert\Regex('/\d+/', message: "Veuillez ne pas saisir de lettres")]
    private $age;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $image;

    #[ORM\OneToOne(inversedBy: 'personne', targetEntity: Profile::class, cascade: ['persist', 'remove'])]
    private $profile;

    #[ORM\ManyToMany(targetEntity: Hobby::class)]
    private $hobbies;

    #[ORM\ManyToOne(targetEntity: Job::class, inversedBy: 'personnes')]
    private $job;

    public function __construct()
    {
        $this->hobbies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * @return Collection<int, Hobby>
     */
    public function getHobbies(): Collection
    {
        return $this->hobbies;
    }

    public function addHobby(Hobby $hobby): self
    {
        if (!$this->hobbies->contains($hobby)) {
            $this->hobbies[] = $hobby;
        }

        return $this;
    }

    public function removeHobby(Hobby $hobby): self
    {
        $this->hobbies->removeElement($hobby);

        return $this;
    }

    public function getJob(): ?Job
    {
        return $this->job;
    }

    public function setJob(?Job $job): self
    {
        $this->job = $job;

        return $this;
    }
}

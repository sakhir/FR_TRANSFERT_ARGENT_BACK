<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PartenaireRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 * attributes={
 *      "pagination_enabled"=true,
 *      "security" = "(is_granted('ROLE_Super-Admin') or is_granted('ROLE_Admin-partenaire'))",
 *      "security_message" = "vous n'avez pas accès a cette resource"
 *   },
 * collectionOperations={
 * 
 * "get_partenaires"={
 *          "method"= "GET",
 *          "path" = "/all/partenaires",
 *          "normalization_context"={"groups"={"partenaire"}}    
 *    },
 * 
 * 
 * "create_partenaire"={
 *          "route_name"="create_partenaire",
 *   },
 * },
 * itemOperations={
 * 
 * "users_get_subresource"= {
 *               "normalization_context"={"groups"={"partenaire"}},
 *               "method"= "GET",
 *                "path" = "/partenaire/{id}/users",
 *        },
 *      "get_one_partenaire"={
 *             "method"="GET",
 *             "path" = "/partenaire/{id}",
 *              "normalization_context"={"groups"={"partenaire"}},
 *      },
 *  "attribuer_users_partenaire"={
 *             "route_name"="attribuer_users_partenaire",
 *      }
 * },
 * )
 * @ORM\Entity(repositoryClass=PartenaireRepository::class)
 */
class Partenaire
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"partenaire"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"partenaire"})
     */
    private $ninea;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"partenaire"})
     */
    private $localisation;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"partenaire"})
     */
    private $domaineActivite;

      /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"partenaire"})
     */
    private $nomPartenaire;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     * @Groups({"partenaire"})
     */
    private $dateCreation;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="partenaire")
     * @ApiSubresource
     * @Groups({"partenaire"})
     */
    private $users;

    /**
     * @ORM\OneToOne(targetEntity=Compte::class, cascade={"persist", "remove"})
     * @Groups({"partenaire"})
     */
    private $compte;

    /**
     * @ORM\Column(type="boolean")
     */
    private $statut;

  

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNinea(): ?float
    {
        return $this->ninea;
    }

    public function setNinea(float $ninea): self
    {
        $this->ninea = $ninea;

        return $this;
    }

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(string $localisation): self
    {
        $this->localisation = $localisation;

        return $this;
    }

    public function getDomaineActivite(): ?string
    {
        return $this->domaineActivite;
    }

    public function setDomaineActivite(string $domaineActivite): self
    {
        $this->domaineActivite = $domaineActivite;

        return $this;
    }

    public function getNomPartenaire(): ?string
    {
        return $this->nomPartenaire;
    }

    public function setNomPartenaire(string $nomPartenaire): self
    {
        $this->nomPartenaire = $nomPartenaire;

        return $this;
    }
    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setPartenaire($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getPartenaire() === $this) {
                $user->setPartenaire(null);
            }
        }

        return $this;
    }

    public function getCompte(): ?Compte
    {
        return $this->compte;
    }

    public function setCompte(?Compte $compte): self
    {
        $this->compte = $compte;

        return $this;
    }

    public function getStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(bool $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    
}

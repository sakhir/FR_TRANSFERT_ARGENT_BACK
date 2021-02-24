<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CompteRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=CompteRepository::class)
 * @ApiResource(
 * attributes={
 *      "pagination_enabled"=true,
 *      "security" = "(is_granted('ROLE_Super-Admin') or is_granted('ROLE_Admin-partenaire'))",
 *      "security_message" = "vous n'avez pas accès a cette resource"
 *   },
 * collectionOperations={
 * 
 * "get_comptes"={
 *          "method"= "GET",
 *          "path" = "/all/comptes",
 *          "normalization_context"={"groups"={"compte"}}    
 *    },
 * 
 * 
 * 
 * },
 * itemOperations={
 * 
 *      "get_one_compte"={
 *             "method"="GET",
 *             "path" = "/compte/{id}",
 *              "normalization_context"={"groups"={"compte"}},
 *      }
 * },
 * )
 *  @UniqueEntity(
 *     fields={"numeroCompte"},
 *     message="Ce compte existe déja existe déja."
 * )
 */
class Compte
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"partenaire","compte"})
     */
    private $id;

    /**
     * @ORM\Column(type="string",unique=true)
     * @Groups({"partenaire","compte"})
     */
    private $numeroCompte;

    /**
     * @ORM\Column(type="float")
     * @Groups({"partenaire","compte"})
     */
    private $codeBank;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"partenaire","compte"})
     */
    private $nomBeneficiaire;

    /**
     * @ORM\Column(type="float")
     * @Groups({"partenaire","compte"})
     */
    private $solde;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="compte")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity=Depot::class, mappedBy="compte")
     */
    private $depots;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->depots = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroCompte(): ?string
    {
        return $this->numeroCompte;
    }

    public function setNumeroCompte(string $numeroCompte): self
    {
        $this->numeroCompte = $numeroCompte;

        return $this;
    }

    public function getCodeBank(): ?float
    {
        return $this->codeBank;
    }

    public function setCodeBank(float $codeBank): self
    {
        $this->codeBank = $codeBank;

        return $this;
    }

    public function getNomBeneficiaire(): ?string
    {
        return $this->nomBeneficiaire;
    }

    public function setNomBeneficiaire(string $nomBeneficiaire): self
    {
        $this->nomBeneficiaire = $nomBeneficiaire;

        return $this;
    }

    public function getSolde(): ?float
    {
        return $this->solde;
    }

    public function setSolde(float $solde): self
    {
        $this->solde = $solde;

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
            $user->setCompte($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getCompte() === $this) {
                $user->setCompte(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Depot[]
     */
    public function getDepots(): Collection
    {
        return $this->depots;
    }

    public function addDepot(Depot $depot): self
    {
        if (!$this->depots->contains($depot)) {
            $this->depots[] = $depot;
            $depot->setCompte($this);
        }

        return $this;
    }

    public function removeDepot(Depot $depot): self
    {
        if ($this->depots->removeElement($depot)) {
            // set the owning side to null (unless already changed)
            if ($depot->getCompte() === $this) {
                $depot->setCompte(null);
            }
        }

        return $this;
    }
}

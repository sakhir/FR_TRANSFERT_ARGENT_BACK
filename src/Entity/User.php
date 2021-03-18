<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;





/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ApiResource(
 * attributes={
 *      "pagination_enabled"=true,
 *      "security" = "is_granted('ROLE_Super-Admin')",
 *      "security_message" = "vous n'avez pas accès a cette resource"
 *   },
 * collectionOperations={
 * 
 * "get_users"={
 *          "method"= "GET",
 *          "path" = "/all/users",
 *          "normalization_context"={"groups"={"user:read"}}    
 *    },
 * 
 * 
 * "create_users"={
 *          "route_name"="create_user",
 *   },
 * },
 * itemOperations={
 *   "edit_user"={
 *             
 *             "route_name"="edit_user",
 *      },
 *      "get_one_user"={
 *             "method"="GET",
 *             "path" = "/user/{id}",
 *              "normalization_context"={"groups"={"user:read"}},
 *      }
 * },
 * )
 * @ApiFilter(SearchFilter::class, properties={ "isdeleted": "exact"})
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"profil:read","user:read","partenaire","depot"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"profil:read","user:read","partenaire","depot","transaction"})
     */
    private $email;

   
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"profil:read","user:read","partenaire","depot","transaction"})
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"profil:read","user:read","partenaire","depot","transaction"})
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"profil:read","user:read","partenaire"})
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"profil:read","user:read"})
     */
    private $telephone;

    /**
     * @ORM\Column(type="boolean")
     */
    private $statut;

    /**
     *  @ORM\Column(type="blob", nullable=true)
     */
    private $avatar;

    /**
     * @ORM\ManyToOne(targetEntity=Profil::class, inversedBy="users")
     * @Groups({"user:read","partenaire","depot","transaction"})
     */
    private $profil;

    /**
     * @ORM\ManyToOne(targetEntity=Compte::class, inversedBy="users")
     */
    private $compte;

    /**
     * @ORM\OneToMany(targetEntity=Depot::class, mappedBy="caissier")
     */
    private $depots;

    /**
     * @ORM\ManyToOne(targetEntity=Partenaire::class, inversedBy="users")
     */
    private $partenaire;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="userDepot")
     */
    private $transactions;

   

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $isdeleted;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="userRet")
     */
    private $transactionsr;


    public function __construct()
    {
        $this->depots = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->transactionsRet = new ArrayCollection();
        $this->transactionsr = new ArrayCollection();
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
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
        $roles[] = 'ROLE_'.$this->profil->getLibelle();

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

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

    public function getAvatar()
    {
        $av=@stream_get_contents($this->avatar);
           
        @fclose($av);
        return base64_encode($av);
    
    }

    public function setAvatar($avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getProfil(): ?Profil
    {
        return $this->profil;
    }

    public function setProfil(?Profil $profil): self
    {
        $this->profil = $profil;

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
            $depot->setCaissier($this);
        }

        return $this;
    }

    public function removeDepot(Depot $depot): self
    {
        if ($this->depots->removeElement($depot)) {
            // set the owning side to null (unless already changed)
            if ($depot->getCaissier() === $this) {
                $depot->setCaissier(null);
            }
        }

        return $this;
    }

    public function getPartenaire(): ?Partenaire
    {
        return $this->partenaire;
    }

    public function setPartenaire(?Partenaire $partenaire): self
    {
        $this->partenaire = $partenaire;

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setUserDepot($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getUserDepot() === $this) {
                $transaction->setUserDepot(null);
            }
        }

        return $this;
    }

    

    public function getIsdeleted(): ?int
    {
        return $this->isdeleted;
    }

    public function setIsdeleted(?int $isdeleted): self
    {
        $this->isdeleted = $isdeleted;

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactionsr(): Collection
    {
        return $this->transactionsr;
    }

    public function addTransactionsr(Transaction $transactionsr): self
    {
        if (!$this->transactionsr->contains($transactionsr)) {
            $this->transactionsr[] = $transactionsr;
            $transactionsr->setUserRet($this);
        }

        return $this;
    }

    public function removeTransactionsr(Transaction $transactionsr): self
    {
        if ($this->transactionsr->removeElement($transactionsr)) {
            // set the owning side to null (unless already changed)
            if ($transactionsr->getUserRet() === $this) {
                $transactionsr->setUserRet(null);
            }
        }

        return $this;
    }

    
}

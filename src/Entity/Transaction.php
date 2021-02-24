<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 */
class Transaction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomEnvoyeur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenomEnvoyeur;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adresseEnvoyeur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $telEnvoyeur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cinEnvoyeur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomBeneficiaire;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenomBeneficiaire;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $telBeneficiaire;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adresseBeneficiaire;

    /**
     * @ORM\Column(type="float")
     */
    private $codeTransaction;

    /**
     * @ORM\Column(type="float")
     */
    private $montantEnvoyer;

    /**
     * @ORM\Column(type="float")
     */
    private $totalEnvoyer;

    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $montantRetirer;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cinBeneficiaire;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateEnvoie;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $dateRetrait;

    /**
     * @ORM\Column(type="boolean")
     */
    private $type;

    /**
     * @ORM\Column(type="float")
     */
    private $commissionEtat;

    /**
     * @ORM\Column(type="float")
     */
    private $commissionEnvoi;

    /**
     * @ORM\Column(type="float")
     */
    private $commissionRetrait;

    /**
     * @ORM\ManyToOne(targetEntity=Commission::class, inversedBy="transactions")
     */
    private $commissionTTC;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="transactions")
     */
    private $userDepot;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="transactionsRet")
     */
    private $userRetrait;

   

    public function __construct()
    {
        $this->userRetrait = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomEnvoyeur(): ?string
    {
        return $this->nomEnvoyeur;
    }

    public function setNomEnvoyeur(string $nomEnvoyeur): self
    {
        $this->nomEnvoyeur = $nomEnvoyeur;

        return $this;
    }

    public function getPrenomEnvoyeur(): ?string
    {
        return $this->prenomEnvoyeur;
    }

    public function setPrenomEnvoyeur(string $prenomEnvoyeur): self
    {
        $this->prenomEnvoyeur = $prenomEnvoyeur;

        return $this;
    }

    public function getAdresseEnvoyeur(): ?string
    {
        return $this->adresseEnvoyeur;
    }

    public function setAdresseEnvoyeur(?string $adresseEnvoyeur): self
    {
        $this->adresseEnvoyeur = $adresseEnvoyeur;

        return $this;
    }

    public function getTelEnvoyeur(): ?string
    {
        return $this->telEnvoyeur;
    }

    public function setTelEnvoyeur(string $telEnvoyeur): self
    {
        $this->telEnvoyeur = $telEnvoyeur;

        return $this;
    }

    public function getCinEnvoyeur(): ?string
    {
        return $this->cinEnvoyeur;
    }

    public function setCinEnvoyeur(string $cinEnvoyeur): self
    {
        $this->cinEnvoyeur = $cinEnvoyeur;

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

    public function getPrenomBeneficiaire(): ?string
    {
        return $this->prenomBeneficiaire;
    }

    public function setPrenomBeneficiaire(string $prenomBeneficiaire): self
    {
        $this->prenomBeneficiaire = $prenomBeneficiaire;

        return $this;
    }

    public function getTelBeneficiaire(): ?string
    {
        return $this->telBeneficiaire;
    }

    public function setTelBeneficiaire(string $telBeneficiaire): self
    {
        $this->telBeneficiaire = $telBeneficiaire;

        return $this;
    }

    public function getAdresseBeneficiaire(): ?string
    {
        return $this->adresseBeneficiaire;
    }

    public function setAdresseBeneficiaire(?string $adresseBeneficiaire): self
    {
        $this->adresseBeneficiaire = $adresseBeneficiaire;

        return $this;
    }

    public function getCodeTransaction(): ?float
    {
        return $this->codeTransaction;
    }

    public function setCodeTransaction(float $codeTransaction): self
    {
        $this->codeTransaction = $codeTransaction;

        return $this;
    }

    public function getMontantEnvoyer(): ?float
    {
        return $this->montantEnvoyer;
    }

    public function setMontantEnvoyer(float $montantEnvoyer): self
    {
        $this->montantEnvoyer = $montantEnvoyer;

        return $this;
    }

    public function getTotalEnvoyer(): ?float
    {
        return $this->totalEnvoyer;
    }

    public function setTotalEnvoyer(float $totalEnvoyer): self
    {
        $this->totalEnvoyer = $totalEnvoyer;

        return $this;
    }

    public function getMontantRetirer(): ?float
    {
        return $this->montantRetirer;
    }

    public function setMontantRetirer(float $montantRetirer): self
    {
        $this->montantRetirer = $montantRetirer;

        return $this;
    }

    public function getCinBeneficiaire(): ?string
    {
        return $this->cinBeneficiaire;
    }

    public function setCinBeneficiaire(string $cinBeneficiaire): self
    {
        $this->cinBeneficiaire = $cinBeneficiaire;

        return $this;
    }

    public function getDateEnvoie(): ?\DateTimeInterface
    {
        return $this->dateEnvoie;
    }

    public function setDateEnvoie(\DateTimeInterface $dateEnvoie): self
    {
        $this->dateEnvoie = $dateEnvoie;

        return $this;
    }

    public function getDateRetrait(): ?\DateTimeInterface
    {
        return $this->dateRetrait;
    }

    public function setDateRetrait(\DateTimeInterface $dateRetrait): self
    {
        $this->dateRetrait = $dateRetrait;

        return $this;
    }

    public function getType(): ?bool
    {
        return $this->type;
    }

    public function setType(bool $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCommissionEtat(): ?float
    {
        return $this->commissionEtat;
    }

    public function setCommissionEtat(float $commissionEtat): self
    {
        $this->commissionEtat = $commissionEtat;

        return $this;
    }

    public function getCommissionEnvoi(): ?float
    {
        return $this->commissionEnvoi;
    }

    public function setCommissionEnvoi(float $commissionEnvoi): self
    {
        $this->commissionEnvoi = $commissionEnvoi;

        return $this;
    }

    public function getCommissionRetrait(): ?float
    {
        return $this->commissionRetrait;
    }

    public function setCommissionRetrait(float $commissionRetrait): self
    {
        $this->commissionRetrait = $commissionRetrait;

        return $this;
    }

    public function getCommissionTTC(): ?Commission
    {
        return $this->commissionTTC;
    }

    public function setCommissionTTC(?Commission $commissionTTC): self
    {
        $this->commissionTTC = $commissionTTC;

        return $this;
    }

    public function getUserDepot(): ?User
    {
        return $this->userDepot;
    }

    public function setUserDepot(?User $userDepot): self
    {
        $this->userDepot = $userDepot;

        return $this;
    }

    public function getUserRetrait()
    {
        return $this->userRetrait;
    }

    public function setUserRetrait(?User $userRetrait): self
    {
        $this->userRetrait = $userRetrait;

        return $this;
    }

    
}

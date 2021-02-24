<?php

namespace App\Entity;

use App\Repository\CommissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommissionRepository::class)
 */
class Commission
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $borneInferieur;

    

    /**
     * @ORM\Column(type="float")
     */
    private $valeur;

    /**
     * @ORM\Column(type="float")
     */
    private $borneSuperieur;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="commissionTTC")
     */
    private $transactions;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBorneInferieur(): ?float
    {
        return $this->borneInferieur;
    }

    public function setBorneInferieur(float $borneInferieur): self
    {
        $this->borneInferieur = $borneInferieur;

        return $this;
    }

    

    public function getValeur(): ?float
    {
        return $this->valeur;
    }

    public function setValeur(float $valeur): self
    {
        $this->valeur = $valeur;

        return $this;
    }

    public function getBorneSuperieur(): ?float
    {
        return $this->borneSuperieur;
    }

    public function setBorneSuperieur(float $borneSuperieur): self
    {
        $this->borneSuperieur = $borneSuperieur;

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
            $transaction->setCommissionTTC($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getCommissionTTC() === $this) {
                $transaction->setCommissionTTC(null);
            }
        }

        return $this;
    }
}

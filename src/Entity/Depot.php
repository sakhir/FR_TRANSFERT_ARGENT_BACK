<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\DepotRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * 
 * @ApiResource(
 * attributes={
 *      "pagination_enabled"=true,
 *      "security" = "(is_granted('ROLE_Super-Admin') or is_granted('ROLE_Caissier')) ",
 *      "security_message" = "vous n'avez pas accès a cette resource"
 *   },
 * collectionOperations={
 * 
 * "get_depots"={
 *          "method"= "GET",
 *          "path" = "/all/depots",
 *          "normalization_context"={"groups"={"depot"}}    
 *    },
 * 
 * 
 * "faire_depot"={
 *          "route_name"="faire_depot",
 *   },
 * },
 * itemOperations={
 *   
 *      "get_one_depot"={
 *             "method"="GET",
 *             "path" = "/depot/{id}",
 *              "normalization_context"={"groups"={"depot"}},
 *      }
 * },
 * )
 * @ORM\Entity(repositoryClass=DepotRepository::class)
 */
class Depot
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"depot"})
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"depot"})
     */
    private $dateDepot;

    /**
     * @ORM\Column(type="float")
     * @Groups({"depot"})
     */
    private $montantDepot;

    /**
     * @ORM\ManyToOne(targetEntity=Compte::class, inversedBy="depots")
     * @Groups({"depot"})
     */
    private $compte;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="depots")
     * @Groups({"depot"})
     */
    private $caissier;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDepot(): ?\DateTimeInterface
    {
        return $this->dateDepot;
    }

    public function setDateDepot(\DateTimeInterface $dateDepot): self
    {
        $this->dateDepot = $dateDepot;

        return $this;
    }

    public function getMontantDepot(): ?float
    {
        return $this->montantDepot;
    }

    public function setMontantDepot(float $montantDepot): self
    {
        $this->montantDepot = $montantDepot;

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

    public function getCaissier(): ?User
    {
        return $this->caissier;
    }

    public function setCaissier(?User $caissier): self
    {
        $this->caissier = $caissier;

        return $this;
    }
}

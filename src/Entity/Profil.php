<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProfilRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass=ProfilRepository::class)
 * @ApiResource(
 * attributes={
 *      "security" = "is_granted('ROLE_Super-Admin')",
 *      "security_message" = "vous n'avez pas accès a cette resource"
 * },
 *  * collectionOperations={
 *      "get_profils"={
 *          "method"= "GET",
 *          "path" = "/admin/profils",
 *          "normalization_context"={"groups"={"profil:read"}},
 *      },
 *      "create_profils"={
 *          "method"= "POST",
 *          "path" = "/admin/profils",
 *    
 *      }
 *     
 * },
 *  itemOperations={
 *       "users_get_subresource"= {
 *               "normalization_context"={"groups"={"profil:read"}},
 *               "method"= "GET",
 *                "path" = "/admin/profils/{id}/users",
 *        },
 * 
 *      "get_one_profil"={
 *             "method"="GET",
 *             "path" = "/admin/profils/{id}",
 *             "normalization_context"={"groups"={"profil:read"}},
 *      },
 *      "edit_profil"={
 *             "method"="PUT",
 *             "path" = "/admin/profils/{id}",
 *      }
 * },
 * )
 * @ApiFilter(SearchFilter::class, properties={ "archivage": "exact"})
 */
class Profil
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"profil:read"})
     */
    private $id;

    /**
     * @Assert\NotBlank(message="veuillez entrer le profil")
     * @ORM\Column(type="string", length=255)
     * @Groups({"profil:read","user:read","partenaire","depot","transaction"})
     */
    private $libelle;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="profil")
     * @ApiSubresource
     * @Groups({"profil:read"})
     */
    private $users;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $archivage;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

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
            $user->setProfil($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getProfil() === $this) {
                $user->setProfil(null);
            }
        }

        return $this;
    }

    public function getArchivage(): ?int
    {
        return $this->archivage;
    }

    public function setArchivage(?int $archivage): self
    {
        $this->archivage = $archivage;

        return $this;
    }
}

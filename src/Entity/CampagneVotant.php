<?php

namespace App\Entity;

use App\Repository\CampagneVotantRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CampagneVotantRepository::class)
 */
class CampagneVotant
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Campagne::class)
     */
    private $campagne;

    /**
     * @ORM\ManyToOne(targetEntity=Votant::class)
     */
    private $votant;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCampagne(): ?Campagne
    {
        return $this->campagne;
    }

    public function setCampagne(?Campagne $campagne): self
    {
        $this->campagne = $campagne;

        return $this;
    }
    public function getVotant(): ?Votant
    {
        return $this->votant;
    }

    public function setVotant(?Votant $votant): self
    {
        $this->votant = $votant;

        return $this;
    }
}

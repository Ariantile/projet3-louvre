<?php

namespace Louvre\BilletterieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tarifs
 *
 * @ORM\Table(name="tarifs")
 * @ORM\Entity(repositoryClass="Louvre\BilletterieBundle\Repository\TarifsRepository")
 */
class Tarifs
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nomtarif", type="string", length=255, unique=true)
     */
    private $nomtarif;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="prix", type="decimal", precision=10, scale=2)
     */
    private $prix;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nomtarif
     *
     * @param string $nomtarif
     *
     * @return Tarifs
     */
    public function setNomtarif($nomtarif)
    {
        $this->nomtarif = $nomtarif;

        return $this;
    }

    /**
     * Get nomtarif
     *
     * @return string
     */
    public function getNomtarif()
    {
        return $this->nomtarif;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Tarifs
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set prix
     *
     * @param string $prix
     *
     * @return Tarifs
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix
     *
     * @return string
     */
    public function getPrix()
    {
        return $this->prix;
    }
}


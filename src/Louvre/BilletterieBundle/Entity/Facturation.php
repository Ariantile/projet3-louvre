<?php

namespace Louvre\BilletterieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Facturation
 *
 * @ORM\Table(name="facturation")
 * @ORM\Entity(repositoryClass="Louvre\BilletterieBundle\Repository\FacturationRepository")
 */
class Facturation
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
     * @ORM\Column(name="courriel", type="string", length=255)
     */
    private $courriel;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_facture", type="string", length=255)
     */
    private $nomFacture;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom_facture", type="string", length=255)
     */
    private $prenomFacture;

    /**
     * @var string
     *
     * @ORM\Column(name="pays", type="string", length=255)
     */
    private $pays;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="naissance_facture", type="date")
     */
    private $naissanceFacture;


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
     * Set courriel
     *
     * @param string $courriel
     *
     * @return Facturation
     */
    public function setCourriel($courriel)
    {
        $this->courriel = $courriel;

        return $this;
    }

    /**
     * Get courriel
     *
     * @return string
     */
    public function getCourriel()
    {
        return $this->courriel;
    }

    /**
     * Set nomFacture
     *
     * @param string $nomFacture
     *
     * @return Facturation
     */
    public function setNomFacture($nomFacture)
    {
        $this->nomFacture = $nomFacture;

        return $this;
    }

    /**
     * Get nomFacture
     *
     * @return string
     */
    public function getNomFacture()
    {
        return $this->nomFacture;
    }

    /**
     * Set prenomFacture
     *
     * @param string $prenomFacture
     *
     * @return Facturation
     */
    public function setPrenomFacture($prenomFacture)
    {
        $this->prenomFacture = $prenomFacture;

        return $this;
    }

    /**
     * Get prenomFacture
     *
     * @return string
     */
    public function getPrenomFacture()
    {
        return $this->prenomFacture;
    }

    /**
     * Set pays
     *
     * @param string $pays
     *
     * @return Facturation
     */
    public function setPays($pays)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays
     *
     * @return string
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set naissanceFacture
     *
     * @param \DateTime $naissanceFacture
     *
     * @return Facturation
     */
    public function setNaissanceFacture($naissanceFacture)
    {
        $this->naissanceFacture = $naissanceFacture;

        return $this;
    }

    /**
     * Get naissanceFacture
     *
     * @return \DateTime
     */
    public function getNaissanceFacture()
    {
        return $this->naissanceFacture;
    }
}


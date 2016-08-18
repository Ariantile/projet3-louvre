<?php

namespace Louvre\BilletterieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Billet
 *
 * @ORM\Table(name="billet")
 * @ORM\Entity(repositoryClass="Louvre\BilletterieBundle\Repository\BilletRepository")
 */
class Billet
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
     * @ORM\Column(name="code_reservation", type="string", length=24, unique=true)
     */
    private $codeReservation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_reservation", type="date")
     */
    private $dateReservation;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_billet", type="string", length=255)
     */
    private $nomBillet;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom_billet", type="string", length=255)
     */
    private $prenomBillet;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="naissance_billet", type="date")
     */
    private $naissanceBillet;


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
     * Set codeReservation
     *
     * @param string $codeReservation
     *
     * @return Billet
     */
    public function setCodeReservation($codeReservation)
    {
        $this->codeReservation = $codeReservation;

        return $this;
    }

    /**
     * Get codeReservation
     *
     * @return string
     */
    public function getCodeReservation()
    {
        return $this->codeReservation;
    }

    /**
     * Set dateReservation
     *
     * @param \DateTime $dateReservation
     *
     * @return Billet
     */
    public function setDateReservation($dateReservation)
    {
        $this->dateReservation = $dateReservation;

        return $this;
    }

    /**
     * Get dateReservation
     *
     * @return \DateTime
     */
    public function getDateReservation()
    {
        return $this->dateReservation;
    }

    /**
     * Set nomBillet
     *
     * @param string $nomBillet
     *
     * @return Billet
     */
    public function setNomBillet($nomBillet)
    {
        $this->nomBillet = $nomBillet;

        return $this;
    }

    /**
     * Get nomBillet
     *
     * @return string
     */
    public function getNomBillet()
    {
        return $this->nomBillet;
    }

    /**
     * Set prenomBillet
     *
     * @param string $prenomBillet
     *
     * @return Billet
     */
    public function setPrenomBillet($prenomBillet)
    {
        $this->prenomBillet = $prenomBillet;

        return $this;
    }

    /**
     * Get prenomBillet
     *
     * @return string
     */
    public function getPrenomBillet()
    {
        return $this->prenomBillet;
    }

    /**
     * Set naissanceBillet
     *
     * @param \DateTime $naissanceBillet
     *
     * @return Billet
     */
    public function setNaissanceBillet($naissanceBillet)
    {
        $this->naissanceBillet = $naissanceBillet;

        return $this;
    }

    /**
     * Get naissanceBillet
     *
     * @return \DateTime
     */
    public function getNaissanceBillet()
    {
        return $this->naissanceBillet;
    }
}


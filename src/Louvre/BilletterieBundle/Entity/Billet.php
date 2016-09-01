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
     * @ORM\ManyToOne(targetEntity="Louvre\BilletterieBundle\Entity\Commande", inversedBy="billets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $commande;
     
    /**
     * @ORM\ManyToOne(targetEntity="Louvre\BilletterieBundle\Entity\Tarifs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tarifs;
    
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
     * @var string
     *
     * @ORM\Column(name="nom_billet", type="string", length=255)
     * @Assert\length(
     *      min = 2,
     *      max = 30
     * )
     */
    private $nomBillet;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom_billet", type="string", length=255)
     * @Assert\length(
     *      min = 2,
     *      max = 30
     * )
     */
    private $prenomBillet;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="naissance_billet", type="date")
     * @Assert\Date()
     */
    private $naissanceBillet;
    
    /**
     * @var string
     *
     * @ORM\Column(name="pays_billet", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $paysBillet;


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
    
    /**
     * Set paysBillet
     *
     * @param string $paysBillet
     *
     * @return Billet
     */
    public function setPaysBillet($paysBillet)
    {
        $this->paysBillet = $paysBillet;

        return $this;
    }

    /**
     * Get paysBillet
     *
     * @return string
     */
    public function getPaysBillet()
    {
        return $this->paysBillet;
    }

    /**
     * Set commande
     *
     * @param \Louvre\BilletterieBundle\Entity\Commande $commande
     *
     * @return Billet
     */
    public function setCommande(\Louvre\BilletterieBundle\Entity\Commande $commande)
    {
        $this->commande = $commande;

        return $this;
    }

    /**
     * Get commande
     *
     * @return \Louvre\BilletterieBundle\Entity\Commande
     */
    public function getCommande()
    {
        return $this->commande;
    }

    /**
     * Set tarifs
     *
     * @param \Louvre\BilletterieBundle\Entity\Tarifs $tarifs
     *
     * @return Billet
     */
    public function setTarifs(\Louvre\BilletterieBundle\Entity\Tarifs $tarifs)
    {
        $this->tarifs = $tarifs;

        return $this;
    }

    /**
     * Get tarifs
     *
     * @return \Louvre\BilletterieBundle\Entity\Tarifs
     */
    public function getTarifs()
    {
        return $this->tarifs;
    }
}

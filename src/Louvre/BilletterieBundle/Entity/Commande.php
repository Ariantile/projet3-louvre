<?php

namespace Louvre\BilletterieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Commande
 *
 * @ORM\Table(name="commande")
 * @ORM\Entity(repositoryClass="Louvre\BilletterieBundle\Repository\CommandeRepository")
 */
class Commande
{
    /**
     * @ORM\OneToOne(targetEntity="Louvre\BilletterieBundle\Entity\Facturation", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid()
     */
    private $facturation;
    
    /**
     * @ORM\OneToMany(targetEntity="Louvre\BilletterieBundle\Entity\Billet", mappedBy="commande")
     * @Assert\Valid()
     */
    private $billets;
       
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
     * @ORM\Column(name="num_commande", type="string", length=12, unique=true)
     */
    private $numCommande;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_commande", type="datetime")
     * @Assert\DateTime()
     */
    private $dateCommande;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_reservation", type="date")
     * @Assert\Date()
     */
    private $dateReservation;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="demi_journee", type="boolean")
     */
    private $demiJournee;
    
    /**
     * @var int
     *
     * @ORM\Column(name="qte", type="integer")
     * @Assert\Range(
     *      min = 1,
     *      max = 9
     * )
     */
    private $qte;
    
    /**
     * @var string
     *
     * @ORM\Column(name="sous_total", type="decimal", precision=10, scale=2)
     * @Assert\Range(
     *      min = 4
     * )
     */
    private $sousTotal;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;
    
    public function setFacturation (Facturation $facturation)
    {
        $this->facturation = $facturation;
    }
    
    public function getFacturation()
    {
        return $this->facturation;
    }
    
    public function __construct()
    {
        $this->dateCommande = new \Datetime();
        $this->quantites = new ArrayCollection();
    }
    
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
     * Set numCommande
     *
     * @param string $numCommande
     *
     * @return Commande
     */
    public function setNumCommande($numCommande)
    {
        $this->numCommande = $numCommande;

        return $this;
    }

    /**
     * Get numCommande
     *
     * @return string
     */
    public function getNumCommande()
    {
        return $this->numCommande;
    }
    
    /**
     * Set dateCommande
     *
     * @param \DateTime $dateCommande
     *
     * @return Commande
     */
    public function setDateCommande($dateCommande)
    {
        $this->dateCommande = $dateCommande;

        return $this;
    }

    /**
     * Get dateCommande
     *
     * @return \DateTime
     */
    public function getDateCommande()
    {
        return $this->dateCommande;
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
     * Set qte
     *
     * @param integer $qte
     *
     * @return Quantite
     */
    public function setQte($qte)
    {
        $this->qte = $qte;

        return $this;
    }

    /**
     * Get qte
     *
     * @return int
     */
    public function getQte()
    {
        return $this->qte;
    }
    
    /**
     * Set sousTotal
     *
     * @param string $sousTotal
     *
     * @return Commande
     */
    public function setSousTotal($sousTotal)
    {
        $this->sousTotal = $sousTotal;

        return $this;
    }

    /**
     * Get sousTotal
     *
     * @return string
     */
    public function getSousTotal()
    {
        return $this->sousTotal;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Commande
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Add billet
     *
     * @param \Louvre\BilletterieBundle\Entity\Billet $billet
     *
     * @return Commande
     */
    public function addBillet(\Louvre\BilletterieBundle\Entity\Billet $billet)
    {
        $this->billets[] = $billet;

        return $this;
    }

    /**
     * Remove billet
     *
     * @param \Louvre\BilletterieBundle\Entity\Billet $billet
     */
    public function removeBillet(\Louvre\BilletterieBundle\Entity\Billet $billet)
    {
        $this->billets->removeElement($billet);
    }

    /**
     * Get billets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBillets()
    {
        return $this->billets;
    }

    /**
     * Set demiJournee
     *
     * @param boolean $demiJournee
     *
     * @return Commande
     */
    public function setDemiJournee($demiJournee)
    {
        $this->demiJournee = $demiJournee;

        return $this;
    }

    /**
     * Get demiJournee
     *
     * @return boolean
     */
    public function getDemiJournee()
    {
        return $this->demiJournee;
    }
}

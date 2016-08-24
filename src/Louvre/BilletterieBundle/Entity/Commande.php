<?php

namespace Louvre\BilletterieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     */
    private $facturation;
    
    /**
     * @ORM\OneToMany(targetEntity="Louvre\BilletterieBundle\Entity\quantite", mappedBy="commande")
     */
    private $quantites;
    
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
     */
    private $dateCommande;

    /**
     * @var string
     *
     * @ORM\Column(name="sous_total", type="decimal", precision=10, scale=2)
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

    public function addQuantite(Quantite $quantite)
    {
        $this->quantites[] = $quantite;
        $quantite->setCommande($this);
        return $this;
    }

    public function removeQuantite (Quantite $quantite)
    {
        $this->quantites->removeElement($quantite);
    }

    public function getQuantite()
    {
        return $this->quantites;
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
}


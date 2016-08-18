<?php

namespace Louvre\BilletterieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Quantite
 *
 * @ORM\Table(name="quantite")
 * @ORM\Entity(repositoryClass="Louvre\BilletterieBundle\Repository\QuantiteRepository")
 */
class Quantite
{
    
    /**
     * @ORM\ManyToOne(targetEntity="Louvre\BilletterieBundle\Entity\Commande", inversedBy="quantites")
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
     * @var int
     *
     * @ORM\Column(name="qte", type="integer")
     */
    private $qte;

    /**
     * @var string
     *
     * @ORM\Column(name="total", type="decimal", precision=10, scale=2)
     */
    private $total;

    public function setCommande (Commande $commande)
    {
        $this->commande = $commande;
    }
    
    public function getCommande()
    {
        return $this->commande;
    }
        
    public function getTarifs()
    {
        return $this->tarifs;
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
     * Set total
     *
     * @param string $total
     *
     * @return Quantite
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return string
     */
    public function getTotal()
    {
        return $this->total;
    }
}


<?php

namespace Louvre\BilletterieBundle\tests\PreparationPaiementTest;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Louvre\BilletterieBundle\Entity\Billet;
use Louvre\BilletterieBundle\Entity\Commande;

class LouvrePreparationPaiementTest extends WebTestCase
{

    public function testPreparePayment()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $container = $kernel->getContainer();
        $prepPay = $container->get('louvre_paiement.prepare');
        
        $commande = $this->createMock(Commande::class);
        $commande->expects($this->once())
            ->method('getNumCommande')
            ->will($this->returnValue('numero_commande'));
        
        $billet1 = $this->createMock(Billet::class);
        $billet1->expects($this->once())
            ->method('getPrixBillet')
            ->will($this->returnValue(12));
        $billet1->expects($this->once())
            ->method('getCommande')
            ->will($this->returnValue($commande));
        
        $billet2 = $this->createMock(Billet::class);
        $billet2->expects($this->once())
            ->method('getPrixBillet')
            ->will($this->returnValue(16));
        
        $billets = array($billet1, $billet2);
    
        $demiJournee = true;
        
        $payment = $prepPay->preparePayment($demiJournee, $billets);
        
        $total = $payment["amount"];
        $currency = $payment["currency"];
        $description = $payment["description"];
        $meta = $payment["metadata"];
        
        $expectedTotal = 1400;
        
        $expectedCurrency = 'EUR';
        $expectedDescription = 'Louvre Billetterie';
        $expectedMeta = array('numero_commande' => 'numero_commande');
        
        $this->assertEquals($expectedTotal, $total);
        $this->assertEquals($expectedCurrency, $currency);
        $this->assertEquals($expectedDescription, $description);
        $this->assertEquals($expectedMeta, $meta);
        
    }
}

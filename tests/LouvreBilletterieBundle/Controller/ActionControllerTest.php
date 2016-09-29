<?php

namespace Louvre\BilletterieBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ActionControllerTest extends WebTestCase
{   
    /**
     * @dataProvider urlProvider
     */
    public function testRouteReussite($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function urlProvider()
    {
        return array(
            array('/fr/recover'),
            array('/fr/contact'),
            array('/fr/info-pratique'),
            array('/en/recover'),
            array('/en/contact'),
            array('/en/info-pratique')
        );
    }
    
    /**
     * @dataProvider urlProviderEchec
     */
    public function testRouteEchec($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertFalse($client->getResponse()->isSuccessful());
        $this->assertTrue(
            $client->getResponse()->isRedirect('/fr/' || '/en/'),
            'response is a redirect to /fr/ or /en/'
        );
    }

    public function urlProviderEchec()
    {
        return array(
            array('/fr/paiement'),
            array('/fr/remerciement'),
            array('/en/paiement'),
            array('/en/remerciement')
        );
    }
    
    public function testContactSubmit()
    {   
        $client = static::createClient();

        $crawler = $client->request('GET', '/fr/contact');
        
        $this->assertEquals(1, $crawler->filter('h2:contains("Contactez-nous")')->count());
        
        $form = $crawler->selectButton('Envoyer')->form();
        
        $form['contact[nom]'] = 'unnom';
        $form['contact[titre]'] = 'le titre';
        $form['contact[message]'] = 'le message';
        $form['contact[email]'] = 'adresse@mail.com';
        
        $data = $form->getPhpValues();
        
        $crawler = $client->submit($form);
        
        $expected = array('contact' => array('nom' => 'unnom',
                                             'titre' => 'le titre',
                                             'message' => 'le message',
                                             'email' => 'adresse@mail.com',
                                             'Envoyer' => ''
        ));
        
        $this->assertEquals($expected, $data);
    }
    
    public function testRecoverSubmit()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/fr/recover');
        
        $this->assertEquals(1, $crawler->filter('h2:contains("Récupération de billet")')->count());
        
        $form = $crawler->selectButton('Envoyer')->form(array(
            'recherche[courriel]' => 'adresse@mail.com' 
        ));
        
        $data = $form->getPhpValues();
        
        $crawler = $client->submit($form);
        
        $expected = array('recherche' => array('courriel' => 'adresse@mail.com',
                                               'recherche' => ''
        ));
        
        $this->assertEquals($expected, $data);
            
    }
}

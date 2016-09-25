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
            array('/fr/'),
            array('/fr/recover'),
            array('/en/'),
            array('/en/recover')
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
            'response is a redirect to /fr/ ou /en/'
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
    
    public function testRecover()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/fr/recover');

        $this->assertEquals(1, $crawler->filter('h2:contains("Récupération de billet")')->count());

        $form = $crawler->selectButton('Envoyer')->form();

        $form['recherche[courriel]'] = 'email@email.com';

        $crawler = $client->submit($form);

        $this->assertEquals(1, $crawler->filter('span:contains("Vous devez cocher la case "Je ne suis pas un robot"")')->count());
    }
    
}

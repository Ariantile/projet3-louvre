<?php
// src/Louvre/BilletterieBundle/DataFixtures/ORM/LoadTarifs.php

namespace Louvre\BilletterieBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Louvre\BilletterieBundle\Entity\Tarifs;

class LoadTarifs implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {           
        $arraytarifs = array(
            array('nom' => 'Normal', 'prix' => 16.00 , 'description' => 'De 12 à 59 ans.'),
            array('nom' => 'Réduit', 'prix' => 10.00 , 'description' => 'Tarif réservé au public suivant : étudiant, employé du 
                                                                         musée ou du ministère de la culture, militaire.'),
            array('nom' => 'Enfant', 'prix' => 8.00 , 'description' => '60 ans et plus.'),
            array('nom' => 'Senior', 'prix' => 12.00 , 'description' => 'De 4 à 11 ans.'),
            array('nom' => 'Famille', 'prix' => 35.00 , 'description' => '2 adultes et 2 enfants ayant le même nom de famille 
                                                                          qu\'un des deux parents.'),
            array('nom' => 'Normal demi-journée', 'prix' => 8.00 , 'description' => 'De 12 à 59 ans.'),
            array('nom' => 'Réduit demi-journée', 'prix' => 5.00 , 'description' => 'Tarif réservé au public suivant : étudiant, employé du 
                                                                                     musée ou du ministère de la culture, militaire.'),
            array('nom' => 'Enfant demi-journée', 'prix' => 4.00 , 'description' => '60 ans et plus.'),
            array('nom' => 'Senior demi-journée', 'prix' => 6.00 , 'description' => 'De 4 à 11 ans.'),
            array('nom' => 'Famille demi-journée', 'prix' => 17.50 , 'description' => '2 adultes et 2 enfants ayant le même nom de famille 
                                                                                       qu\'un des deux parents.')
        );
        
        foreach ($arraytarifs as $row) 
        {

            $tarifs = new Tarifs();
            $tarifs->setNomtarif($row['nom']);
            $tarifs->setPrix($row['prix']);
            $tarifs->setDescription($row['description']);

            $manager->persist($tarifs);
        }
 
        $manager->flush();
    }
}
<?php
// src/Louvre/UserBundle/Entity/User.php

namespace Louvre\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Table(name="louvre_user")
 * @ORM\Entity(repositoryClass="Louvre\UserBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(name="nom", type="string")
     */
    protected $nom;
    
    /**
     * @ORM\Column(name="prenom", type="string")
     */
    protected $prenom;
}
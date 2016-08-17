<?php

namespace Louvre\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('LouvreUserBundle:Default:index.html.twig');
    }
}

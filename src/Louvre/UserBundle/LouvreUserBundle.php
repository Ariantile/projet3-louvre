<?php

namespace Louvre\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class LouvreUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}

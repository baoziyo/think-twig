<?php

namespace baozi\twig;

use Twig\Extension\AbstractExtension;
use baozi\twig\nodevisitors\GetAttrAdjuster;

class Extension extends AbstractExtension
{
    public function getNodeVisitors()
    {
        return [
            new GetAttrAdjuster(),
        ];
    }
}

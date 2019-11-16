<?php

namespace baoziyo\twig;

use Twig\Extension\AbstractExtension;
use baoziyo\twig\nodevisitors\GetAttrAdjuster;

class Extension extends AbstractExtension
{
    public function getNodeVisitors()
    {
        return [
            new GetAttrAdjuster(),
        ];
    }
}

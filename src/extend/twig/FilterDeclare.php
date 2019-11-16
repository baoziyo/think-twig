<?php

namespace twig;

use Twig\Extension\AbstractExtension;

class FilterDeclare extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('aaa', array($this, 'aaa')),
        ];
    }

    public function aaa()
    {
        return \twig\Filter::aaa();
    }
}
<?php

namespace twig;

use think\facade\Url;
use Twig\Extension\AbstractExtension;

class FunctionsDeclare extends AbstractExtension
{
    public function getFunctions()
    {
        return array(
            new TwigFunction('captcha_src', array($this, 'captcha_src')),
            new TwigFunction('asset', array($this, 'asset')),
            new TwigFunction('think', array($this, 'think')),
            new TwigFunction('data', array($this, 'data')),
            new TwigFunction('url', array($this, 'url')),
        );
    }

    public function captcha_src($id = '')
    {
        return \twig\Functions::captcha_src($id);
    }

    public function asset($path)
    {
        return \twig\Functions::asset($path);
    }

    public function think($app, $type, $param = '')
    {
        return \twig\Functions::think($app, $type, $param);
    }

    public function data($name, $parameters = [])
    {
        return \twig\Functions::data($name, $parameters);
    }

    public function url($link, $vars = '')
    {
        return \twig\Functions::url($link, $vars);
    }
}

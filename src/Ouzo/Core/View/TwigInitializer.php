<?php
namespace Ouzo\View;

use Twig_Environment;

interface TwigInitializer
{
    public function initialize(Twig_Environment $environment);
}
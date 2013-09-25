<?php
use Ouzo\Routing\Route;
use Ouzo\Shell;

class RoutesShell extends Shell
{
    public function main()
    {
        print_r(Route::getRoutes());
    }
}
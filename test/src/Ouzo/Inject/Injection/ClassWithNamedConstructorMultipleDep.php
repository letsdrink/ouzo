<?php

use Ouzo\Injection\Annotation\Inject;
use Ouzo\Injection\Annotation\Named;

class ClassWithNamedConstructorMultipleDep
{
    public $myClass;
    public $secondClass;

    /**
     * @Inject
     * @Named("myClass=my_dep,secondClass=my_second_dep")
     */
    public function __construct(ClassWithNoDep $myClass, ClassWithPrivateDep $secondClass)
    {
        $this->myClass = $myClass;
        $this->secondClass = $secondClass;
    }
}

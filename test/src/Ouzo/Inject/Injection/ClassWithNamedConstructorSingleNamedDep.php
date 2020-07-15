<?php

use Ouzo\Injection\Annotation\Inject;
use Ouzo\Injection\Annotation\Named;

class ClassWithNamedConstructorSingleNamedDep
{
    public $myClass;
    public $secondClass;

    /**
     * @Inject
     * @Named("secondClass=my_second_dep")
     */
    public function __construct(ClassWithPrivateDep $myClass, ClassWithPrivateDep $secondClass)
    {
        $this->myClass = $myClass;
        $this->secondClass = $secondClass;
    }
}

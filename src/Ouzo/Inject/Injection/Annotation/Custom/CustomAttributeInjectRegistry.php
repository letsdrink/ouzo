<?php

namespace Ouzo\Injection\Annotation\Custom;

class CustomAttributeInjectRegistry
{
    private array $customAttributeInjects = [];

    public function register(CustomAttributeInject $customAttributeInject)
    {
        $this->customAttributeInjects[] = $customAttributeInject;
    }

    /** @return CustomAttributeInject[] */
    public function getCustomAttributeInjects(): array
    {
        return $this->customAttributeInjects;
    }
}

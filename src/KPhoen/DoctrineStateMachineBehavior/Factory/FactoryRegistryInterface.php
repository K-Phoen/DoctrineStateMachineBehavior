<?php

namespace KPhoen\DoctrineStateMachineBehavior\Factory;

use Finite\Factory\FactoryInterface;

interface FactoryRegistryInterface extends FactoryInterface
{
    public function registerFactory($class, FactoryInterface $factory);
}

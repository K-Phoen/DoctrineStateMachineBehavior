<?php

namespace KPhoen\DoctrineStateMachineBehavior\Factory;

class FactoryRegistry implements FactoryRegistryInterface
{
    protected $factories = array();

    public function registerFactory($class, FactoryInterface $factory)
    {
        $this->factories[$class] = $factory;

        return $this;
    }

    /**
     * Returns a StateMachine instance initialized on $object
     *
     * @param StatefulInterface $object
     *
     * @return \Finite\StateMachine\StateMachineInterface
     */
    public function get(StatefulInterface $object)
    {
        $class = get_class($object);

        if (!isset($this->factories[$class])) {
            throw new \RuntimeException(sprintf('No StateMachine factory for class "%s"', $class));
        }

        return $this->factories[$class]->get($object);
    }
}

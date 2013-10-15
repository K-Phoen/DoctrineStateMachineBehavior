<?php

namespace KPhoen\DoctrineStateMachineBehavior\Listener;

use Doctrine\Common\EventSubscriber;
use Finite\Factory\FactoryInterface;

abstract class AbstractListener implements EventSubscriber
{
    protected $factory;

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Checks if the given entity supports state machines
     *
     * @param  \ReflectionClass $reflClass
     * @return boolean
     */
    protected function isEntitySupported(\ReflectionClass $reflClass)
    {
        return $reflClass->implementsInterface('\KPhoen\DoctrineStateMachineBehavior\Entity\Stateful');
    }

    protected function injectStateMachine($entity)
    {
        if ($entity->getStateMachine() === null) {
            $state_machine = $this->factory->get($entity);
            $entity->setStateMachine($state_machine);
        }
    }
}

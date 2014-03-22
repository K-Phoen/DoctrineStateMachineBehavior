<?php

namespace KPhoen\DoctrineStateMachineBehavior\Listener;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

/**
 * Injects the state machines into stateful entities when they are loaded by
 * Doctrine.
 *
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
class InjectionListener extends AbstractListener
{
    public function getSubscribedEvents()
    {
        return [
            Events::postLoad,
        ];
    }

    public function postLoad(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
        $em = $eventArgs->getEntityManager();
        $classMetadata = $em->getClassMetadata(ClassUtils::getClass($entity));

        if (!$this->isEntitySupported($classMetadata->reflClass)) {
            return;
        }

        $this->injectStateMachine($entity);
    }
}

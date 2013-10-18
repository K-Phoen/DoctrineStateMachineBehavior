<?php

namespace KPhoen\DoctrineStateMachineBehavior\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class PersistenceListener extends AbstractListener
{
    protected $columnMapping = array();

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function registerClass($className, $stateProperty)
    {
        $this->columnMapping[ltrim($className, '\\')] = $stateProperty;
    }

    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $this->checkStateMachine($eventArgs);
    }

    public function preUpdate(LifecycleEventArgs $eventArgs)
    {
        $this->checkStateMachine($eventArgs);
    }

    protected function checkStateMachine(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();
        $classMetadata = $em->getClassMetadata(get_class($entity));

        if (!$this->isEntitySupported($classMetadata->reflClass)) {
            return;
        }

        $stateProperty = $this->columnMapping[get_class($entity)];

        // make sure the entity is initialized
        $this->injectStateMachine($entity);

        // check the changes and validate the transition
        $changes = $uow->getEntityChangeSet($entity);

        // the state hasn't changed
        if (empty($changes[$stateProperty])) {
            return;
        }

        // update the state machine if needed
        list($oldState, $newState) = $changes[$stateProperty];
        $stateMachine = $entity->getStateMachine();

        if ($stateMachine->getCurrentState()->getName() !== $newState) {
            $stateMachine->jumpToState($newState);
        }
    }
}

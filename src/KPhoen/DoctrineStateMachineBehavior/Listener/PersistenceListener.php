<?php

namespace KPhoen\DoctrineStateMachineBehavior\Listener;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

/**
 * Checks that changes made on stateful entities are valid.
 *
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
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
        $className = ClassUtils::getClass($entity);
        $classMetadata = $em->getClassMetadata($className);
        $reflClass = $classMetadata->getReflectionClass();
        $stateProperty = null;

        if (!$this->isEntitySupported($reflClass)) {
            return;
        }

        // find mapping for the entity class
        if (array_key_exists($className, $this->columnMapping)) {
            $stateProperty = $this->columnMapping[$className];
        } else {
            // check if there is a mapping for a parent class
            while ($parent = $reflClass->getParentClass()) {
                $parentClassName = $parent->getName();

                if (array_key_exists($parentClassName, $this->columnMapping)) {
                    $stateProperty = $this->columnMapping[$parentClassName];
                    break;
                }
            }
        }

        if ($stateProperty === null) {
            throw new \RuntimeException(sprintf('Could not find the state property for class "%s"', $className));
        }

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

<?php

namespace KPhoen\DoctrineStateMachineBehavior\StateMachine;

use Finite\StateMachine\ListenableStateMachine as BaseStateMachine;

use KPhoen\DoctrineStateMachineBehavior\Event\FiniteEvents;
use KPhoen\DoctrineStateMachineBehavior\Event\TransitionEvent;

class ListenableStateMachine extends BaseStateMachine
{
    /**
     * @{inheritDoc}
     */
    public function can($transition)
    {
        $event = new TransitionEvent($this->getTransition($transition), $this);
        $isAllowed = parent::can($transition);

        if ($isAllowed && method_exists($this->getObject(), 'onCan'.ucfirst($transition))) {
            $isAllowed = call_user_func(array($this->getObject(), 'onCan'.ucfirst($transition)));
        }

        if (!$isAllowed) {
            return false;
        }

        $this->dispatcher->dispatch(FiniteEvents::TEST_TRANSITION.'.'.$transition, $event);

        return $event->isTransitionAllowed();
    }
}

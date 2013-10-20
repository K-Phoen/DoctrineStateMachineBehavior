<?php

namespace KPhoen\DoctrineStateMachineBehavior\Event;

use Finite\Event\TransitionEvent as BaseEvent;

class TransitionEvent extends BaseEvent
{
    protected $transitionAllowed = true;

    public function isTransitionAllowed()
    {
        return $this->transitionAllowed;
    }

    public function setTransitionAllowed($allowed)
    {
        $this->transitionAllowed = (bool) $allowed;
    }
}

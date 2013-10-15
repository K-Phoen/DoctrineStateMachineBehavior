<?php

namespace KPhoen\DoctrineStateMachineBehavior\Entity;

use Finite\StateMachine\StateMachine;

trait StatefulTrait
{
    protected $stateMachine;

    public function setStateMachine(StateMachine $stateMachine)
    {
        $this->stateMachine = $stateMachine;
    }

    public function getStateMachine()
    {
        return $this->stateMachine;
    }

    public function can($transition)
    {
        return $this->stateMachine->can($transition);
    }

    public function __call($method, $arguments)
    {
        $this->stateMachine->apply($method);
    }
}

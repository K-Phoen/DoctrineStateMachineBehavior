<?php

namespace KPhoen\DoctrineStateMachineBehavior\Entity;

use Finite\StateMachine\StateMachine;

/**
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
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
        $transitions = array_flip($this->stateMachine->getTransitions());

        if (substr($method, 0, 3) === 'can' && isset($transitions[strtolower(substr($method, 3))])) {
            return $this->stateMachine->can(strtolower(substr($method, 3)));
        } else if (isset($transitions[$method])) {
            $this->stateMachine->apply($method);
        }
    }
}

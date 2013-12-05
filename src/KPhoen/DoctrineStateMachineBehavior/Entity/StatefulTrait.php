<?php

namespace KPhoen\DoctrineStateMachineBehavior\Entity;

use Finite\StateMachine\StateMachine;

/**
 * Trait implementing useful methods for stateful entities.
 *
 * Expose magic methods based on the transition allowed by te state-machine:
 *  * {TransitionName}(): apply the transition {TransitionName} ;
 *  * can{TransitionName}(): test if the transition {TransitionName} can be applied.
 *
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
trait StatefulTrait
{
    /**
     * @var StateMachine
     */
    protected $stateMachine;

    /**
     * Set the state machine to be used by the entity.
     *
     * @param StateMachine $stateMachine
     */
    public function setStateMachine(StateMachine $stateMachine)
    {
        $this->stateMachine = $stateMachine;
    }

    /**
     * Get the state machine used by the entity.
     *
     * @return StateMachine
     */
    public function getStateMachine()
    {
        return $this->stateMachine;
    }

    /**
     * Test if the given transition can be applied.
     *
     * @param string $transition The transition name.
     *
     * @return bool
     */
    public function can($transition)
    {
        return $this->stateMachine->can($transition);
    }

    public function __call($method, $arguments)
    {
        if (null === $this->stateMachine) {
            return;
        }

        $transitions = array_flip($this->stateMachine->getTransitions());
        $states = array_flip($this->stateMachine->getStates());

        if (substr($method, 0, 3) === 'can' && isset($transitions[strtolower(substr($method, 3))])) {
            return $this->stateMachine->can(strtolower(substr($method, 3)));
        } elseif (substr($method, 0, 2) === 'is' && isset($states[strtolower(substr($method, 2))])) {
            return $this->stateMachine->getCurrentState()->getName() === strtolower(substr($method, 2));
        } elseif (isset($transitions[$method])) {
            $this->stateMachine->apply($method);
        }
    }
}

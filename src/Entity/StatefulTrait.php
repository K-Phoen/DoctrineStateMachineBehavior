<?php

namespace KPhoen\DoctrineStateMachineBehavior\Entity;

use Doctrine\Common\Inflector\Inflector;
use Finite\StateMachine\StateMachine;

/**
 * Trait implementing useful methods for stateful entities.
 *
 * Expose magic methods based on the transition allowed by te state-machine:
 *  * {TransitionName}(): apply the transition {TransitionName} ;
 *  * can{TransitionName}(): test if the transition {TransitionName} can be applied.
 *  * is{StateName}(): test if the state {StateName} is the current one.
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

    /**
     * @param string $transition to be applied
     *
     * @return mixed
     */
    public function apply($transition)
    {
        return $this->stateMachine->apply($transition);
    }

    public function __call($method, $arguments)
    {
        if (null === $this->stateMachine) {
            return;
        }

        $transitions = array_flip($this->stateMachine->getTransitions());
        $states      = array_flip($this->stateMachine->getStates());

        $transition = Inflector::tableize(substr($method, 3));
        $state      = Inflector::tableize(substr($method, 2));

        if (substr($method, 0, 3) === 'can' && isset($transitions[$transition])) {
            return $this->stateMachine->can($transition);
        } elseif (substr($method, 0, 2) === 'is' && isset($states[$state])) {
            return $this->stateMachine->getCurrentState()->getName() === $state;
        } elseif (isset($transitions[$method])) {
            return $this->stateMachine->apply($method);
        }

        throw new \BadMethodCallException(sprintf('The method "::%s()" on class "%s" does not exist.', $method, get_class($this)));
    }
}

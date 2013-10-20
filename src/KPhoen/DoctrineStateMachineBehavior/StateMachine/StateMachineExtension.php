<?php

namespace KPhoen\DoctrineStateMachineBehavior\StateMachine;

use Finite\Exception\StateException;
use Finite\State\StateInterface;
use Finite\Transition\TransitionInterface;

/**
 * Extension of the base StateMachine implementation allowing to check if a
 * state can be reached (versus if a transition can be applied).
 *
 * NB: This is only useful when transitions carry no information.
 *
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
trait StateMachineExtension
{
    protected $statePrecedence = array();

    /**
     * @{inheritDoc}
     */
    public function addTransition($transition, $initialState = null, $finalState = null)
    {
        parent::addTransition($transition, $initialState, $finalState);

        $transitionName = $transition instanceof TransitionInterface ? $transition->getName() : $transition;
        $transitionObject = $this->getTransition($transitionName);

        if (!isset($this->statePrecedence[$transition->getState()])) {
            $this->statePrecedence[$transition->getState()] = array();
        }

        $this->statePrecedence[$transition->getState()] = array_merge(
            $this->statePrecedence[$transition->getState()],
            $transition->getInitialStates()
        );
    }

    /**
     * @{inheritDoc}
     */
    public function addState($state)
    {
        parent::addState($state);

        if ($state instanceof StateInterface) {
            $state = $state->getName();
        }

        if (!isset($this->statePrecedence[$state])) {
            $this->statePrecedence[$state] = array();
        }
    }

    /**
     * Tells if moving to the given state is allowed.
     *
     * @param string|StateInterface $state
     *
     * @return bool
     */
    public function canJumpToState($state)
    {
        if ($state instanceof StateInterface) {
            $state = $state->getName();
        }

        // assert that the given state exists
        $this->getState($state);

        return in_array($this->currentState->getName(), $this->statePrecedence[$state], true);
    }

    /**
     * Moves to the given state if allowed.
     *
     * @param string|StateInterface $state
     *
     * @return bool
     */
    public function jumpToState($state)
    {
        if (!$state instanceof StateInterface) {
            $state = $this->getState($state);
        }

        if (!$this->canJumpToState($state)) {
            throw new StateException(sprintf('Can not jump from state "%s" to "%s".',$this->currentState->getName(), $state->getName()));
        }

        $this->object->setFiniteState($state);
        $this->currentState = $state;
    }
}

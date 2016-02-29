<?php

namespace KPhoen\DoctrineStateMachineBehavior\Entity;

use Finite\StateMachine\StateMachine;
use Finite\StatefulInterface;

/**
 * Interface describing the methods expected in stateful entities.
 *
 * @see StatefulTrait
 *
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
interface Stateful extends StatefulInterface
{
    /**
     * Set the state machine to be used by the entity.
     *
     * @param StateMachine $stateMachine
     */
    public function setStateMachine(StateMachine $stateMachine);

    /**
     * Get the state machine used by the entity.
     *
     * @return StateMachine
     */
    public function getStateMachine();
}

<?php

namespace KPhoen\DoctrineStateMachineBehavior\StateMachine;

use Finite\StateMachine\ListenableStateMachine;

/**
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
class ExtendedStateMachine extends ListenableStateMachine
{
    use StateMachineExtension;
}

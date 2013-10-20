<?php

namespace KPhoen\DoctrineStateMachineBehavior\StateMachine;

use Finite\StateMachine\ListenableStateMachine;

class ExtendedStateMachine extends ListenableStateMachine
{
    use StateMachineExtension;
}

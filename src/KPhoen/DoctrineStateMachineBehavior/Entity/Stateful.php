<?php

namespace KPhoen\DoctrineStateMachineBehavior\Entity;

use Finite\StateMachine\StateMachine;
use Finite\StatefulInterface;

interface Stateful extends StatefulInterface
{
    public function setStateMachine(StateMachine $stateMachine);

    public function getStateMachine();
}

<?php

namespace spec\Fixtures\Entity;

use KPhoen\DoctrineStateMachineBehavior\Entity\Stateful;
use KPhoen\DoctrineStateMachineBehavior\Entity\StatefulTrait;

class Article implements Stateful
{
    use StatefulTrait;

    /**
     * @var string
     */
    protected $state = 's1';

    /**
     * Set state
     *
     * @param  string  $state
     * @return Article
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Sets the object state.
     * Used by the StateMachine behavior
     *
     * @return string
     */
    public function getFiniteState()
    {
        return $this->getState();
    }

    /**
     * Sets the object state.
     * Used by the StateMachine behavior
     *
     * @param string $state
     */
    public function setFiniteState($state)
    {
        return $this->setState($state);
    }
}

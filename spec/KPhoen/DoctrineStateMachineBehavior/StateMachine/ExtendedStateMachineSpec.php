<?php

namespace spec\KPhoen\DoctrineStateMachineBehavior\StateMachine;

use PhpSpec\ObjectBehavior;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Finite\State\State;
use Finite\State\StateInterface;

use spec\Fixtures\Entity\Article;

class ExtendedStateMachineSpec extends ObjectBehavior
{
    function let(Article $article, EventDispatcherInterface $dispatcher)
    {
        $this->beConstructedWith($article, $dispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldImplement('Finite\StateMachine\StateMachineInterface');
        $this->shouldHaveType('Finite\StateMachine\StateMachine');
    }

    function it_can_test_if_a_jump_is_valid(Article $article, EventDispatcherInterface $dispatcher)
    {
        $this->initializeStateMachine($article, $dispatcher);

        // tests
        $this->getCurrentState()->getName()->shouldBe('s1');
        $this->canJumpToState('s2')->shouldReturn(true);
        $this->canJumpToState('s4')->shouldReturn(false);
    }

    function it_can_test_if_a_jump_is_valid_given_a_state_object(Article $article, EventDispatcherInterface $dispatcher)
    {
        $this->initializeStateMachine($article, $dispatcher);

        // tests
        $this->getCurrentState()->getName()->shouldBe('s1');
        $this->canJumpToState($this->getState('s2'))->shouldReturn(true);
        $this->canJumpToState($this->getState('s4'))->shouldReturn(false);
    }

    function it_can_jump_to_a_specific_state(Article $article, EventDispatcherInterface $dispatcher)
    {
        $this->initializeStateMachine($article, $dispatcher);

        // tests
        $this->getCurrentState()->getName()->shouldBe('s1');
        $this->jumpToState('s2');
        $this->getCurrentState()->getName()->shouldBe('s2');
    }

    function it_can_jump_to_a_specific_state_given_a_state_object(Article $article, EventDispatcherInterface $dispatcher)
    {
        $this->initializeStateMachine($article, $dispatcher);

        // tests
        $this->getCurrentState()->getName()->shouldBe('s1');
        $this->jumpToState($this->getState('s2'));
        $this->getCurrentState()->getName()->shouldBe('s2');
    }

    protected function initializeStateMachine(Article $article, EventDispatcherInterface $dispatcher)
    {
        // Define states
        $this->addState(new State('s1', StateInterface::TYPE_INITIAL));
        $this->addState('s2');
        $this->addState('s3');
        $this->addState(new State('s4', StateInterface::TYPE_FINAL));

        // Define transitions
        $this->addTransition('t12', 's1', 's2');
        $this->addTransition('t23', 's2', 's3');
        $this->addTransition('t34', 's3', 's4');
        $this->addTransition('t42', 's4', 's2');

        $this->initialize();
    }
}

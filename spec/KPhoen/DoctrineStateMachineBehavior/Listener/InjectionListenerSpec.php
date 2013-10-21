<?php

namespace spec\KPhoen\DoctrineStateMachineBehavior\Listener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Finite\Factory\FactoryInterface;
use Finite\StateMachine\StateMachine;

use spec\Fixtures\Entity\Article;

class InjectionListenerSpec extends ObjectBehavior
{
    protected $factory, $stateMachine;

    function let(FactoryInterface $factory)
    {
        $this->beConstructedWith($factory);

        $this->factory = $factory;
        $this->stateMachine = new StateMachine();
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('KPhoen\DoctrineStateMachineBehavior\Listener\InjectionListener');
    }

    function it_injects_a_state_machine_for_supported_entities(EntityManager $em, LifecycleEventArgs $event, ClassMetadata $metadata, Article $article, \ReflectionClass $rClass)
    {
        $this->configureEvent($event, $em, $article, $metadata, $rClass);

        // configure the factory
        $this->factory->get($article)->willReturn($this->stateMachine);

        // which is used to check if the entity is supported
        $rClass->implementsInterface('\KPhoen\DoctrineStateMachineBehavior\Entity\Stateful')->willReturn(true);

        // the state machine is created by the factory and injected into the
        // entity
        $article->getStateMachine()->willReturn(null);
        $article->setStateMachine($this->stateMachine)->shouldBeCalled();

        $this->postLoad($event);
    }

    function it_doesnt_inject_anything_if_a_state_machine_is_registered(EntityManager $em, LifecycleEventArgs $event, ClassMetadata $metadata, Article $article, \ReflectionClass $rClass)
    {
        $this->configureEvent($event, $em, $article, $metadata, $rClass);

        // which is used to check if the entity is supported
        $rClass->implementsInterface('\KPhoen\DoctrineStateMachineBehavior\Entity\Stateful')->willReturn(true);

        // the state machine is injected into the entity
        $article->getStateMachine()->willReturn('something not null');
        $article->setStateMachine()->shouldNotBeCalled();

        $this->postLoad($event);
    }

    function it_doesnt_inject_a_state_machine_for_unsupported_entities(EntityManager $em, LifecycleEventArgs $event, ClassMetadata $metadata, Article $article, \ReflectionClass $rClass)
    {
        $this->configureEvent($event, $em, $article, $metadata, $rClass);

        // which is used to check if the entity is supported
        $rClass->implementsInterface('\KPhoen\DoctrineStateMachineBehavior\Entity\Stateful')->willReturn(false);

        // the state machine is not injected into the entity
        $article->getStateMachine()->willReturn(null);
        $article->setStateMachine()->shouldNotBeCalled();

        $this->postLoad($event);
    }

    protected function configureEvent(LifecycleEventArgs $event, EntityManager $em, Article $article, ClassMetadata $metadata, \ReflectionClass $rClass)
    {
        // the event provides an access to the entity and the manager
        $event->getEntity()->willReturn($article);
        $event->getEntityManager()->willReturn($em);

        // the manager provides access to the entity's metadata
        $metadata->reflClass = $rClass;
        $em->getClassMetadata(Argument::any())->willReturn($metadata);
    }
}

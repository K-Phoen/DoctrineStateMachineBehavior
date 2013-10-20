<?php

namespace KPhoen\DoctrineStateMachineBehavior\Listener;

use Finite\Event\FiniteEvents;
use Finite\Event\TransitionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StatefulEntitiesCallbacksListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            FiniteEvents::PRE_TRANSITION    => 'onPreTransition',
            FiniteEvents::POST_TRANSITION   => 'onPostTransition',
            FiniteEvents::TEST_TRANSITION   => 'onTestTransition',
        ];
    }

    public function onPostTransition(TransitionEvent $event)
    {
        $object = $event->getStateMachine()->getObject();
        $this->callCallback($object, 'onPost', $event->getTransition()->getName());
    }

    public function onPreTransition(TransitionEvent $event)
    {
        $object = $event->getStateMachine()->getObject();
        $this->callCallback($object, 'onPre', $event->getTransition()->getName());
    }

    public function onTestTransition(TransitionEvent $event)
    {
        $object = $event->getStateMachine()->getObject();
        $result = $this->callCallback($object, 'onCan', $event->getTransition()->getName());

        if (!is_bool($result)) {
            return;
        }

        $event->setTransitionAllowed($result);
    }

    protected function callCallback($object, $callbackPrefix, $transitionName)
    {
        $methodName = $callbackPrefix.ucfirst($transitionName);

        if (!method_exists($object, $methodName)) {
            return;
        }

        return call_user_func(array($object, $methodName));
    }
}

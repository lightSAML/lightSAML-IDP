<?php

namespace LightSaml\Idp\Action\Assertion\Outbound;

use LightSaml\Action\Assertion\AbstractAssertionAction;
use LightSaml\Action\Profile\Inbound\Response\AbstractSsoStateAction;
use LightSaml\Context\Profile\AssertionContext;
use LightSaml\Context\Profile\Helper\MessageContextHelper;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Resolver\Session\SessionProcessorInterface;
use Psr\Log\LoggerInterface;

class IdpSsoStateAction extends AbstractAssertionAction
{
    /** @var SessionProcessorInterface */
    private $sessionProcessor;

    /**
     * @param LoggerInterface           $logger
     * @param SessionProcessorInterface $sessionProcessor
     */
    public function __construct(LoggerInterface $logger, SessionProcessorInterface $sessionProcessor)
    {
        parent::__construct($logger);

        $this->sessionProcessor = $sessionProcessor;
    }

    /**
     * @param AssertionContext $context
     *
     * @return void
     */
    protected function doExecute(AssertionContext $context)
    {
        if ($context->getAssertion()) {
            $this->sessionProcessor->processAssertions(
                array($context->getAssertion()),
                $context->getProfileContext()->getOwnEntityDescriptor()->getEntityID(),
                $context->getProfileContext()->getPartyEntityDescriptor()->getEntityID()
            );
        }
    }
}

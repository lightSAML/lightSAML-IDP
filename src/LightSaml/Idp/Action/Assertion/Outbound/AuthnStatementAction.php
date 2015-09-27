<?php

namespace LightSaml\Idp\Action\Assertion\Outbound;

use LightSaml\Action\Assertion\AbstractAssertionAction;
use LightSaml\Context\Profile\AssertionContext;
use LightSaml\Model\Assertion\AuthnContext;
use LightSaml\Model\Assertion\AuthnStatement;
use LightSaml\Model\Assertion\SubjectLocality;
use LightSaml\Provider\Session\SessionInfoProviderInterface;
use Psr\Log\LoggerInterface;

/**
 * Creates AuthnStatement and sets values provided by session info provider
 */
class AuthnStatementAction extends AbstractAssertionAction
{
    /** @var  SessionInfoProviderInterface */
    protected $sessionInfoProvider;

    /**
     * @param LoggerInterface              $logger
     * @param SessionInfoProviderInterface $sessionInfoProvider
     */
    public function __construct(LoggerInterface $logger, SessionInfoProviderInterface $sessionInfoProvider)
    {
        parent::__construct($logger);

        $this->sessionInfoProvider = $sessionInfoProvider;
    }

    /**
     * @param AssertionContext $context
     *
     * @return void
     */
    protected function doExecute(AssertionContext $context)
    {
        $authnContext = new AuthnContext();
        $authnContext->setAuthnContextClassRef($this->sessionInfoProvider->getAuthnContextClassRef());

        $authnStatement = new AuthnStatement();
        $authnStatement->setAuthnContext($authnContext);
        $authnStatement->setSessionIndex($this->sessionInfoProvider->getSessionIndex());
        $authnStatement->setAuthnInstant($this->sessionInfoProvider->getAuthnInstant());

        $subjectLocality = new SubjectLocality();
        $subjectLocality->setAddress($context->getProfileContext()->getHttpRequest()->getClientIp());
        $authnStatement->setSubjectLocality($subjectLocality);

        $context->getAssertion()->addItem($authnStatement);
    }
}

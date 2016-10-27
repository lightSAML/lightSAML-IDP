<?php

/*
 * This file is part of the LightSAML-IDP package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the GPL-3 license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Idp\Action\Assertion\Outbound;

use LightSaml\Action\Assertion\AbstractAssertionAction;
use LightSaml\Context\Profile\AssertionContext;
use LightSaml\Model\Assertion\AuthnContext;
use LightSaml\Model\Assertion\AuthnStatement;
use LightSaml\Model\Assertion\SubjectLocality;
use LightSaml\Provider\Session\SessionInfoProviderInterface;
use LightSaml\SamlConstants;
use Psr\Log\LoggerInterface;

/**
 * Creates AuthnStatement and sets values provided by session info provider.
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
        $authnContextClassRef = $this->sessionInfoProvider->getAuthnContextClassRef() ?: SamlConstants::AUTHN_CONTEXT_UNSPECIFIED;
        $authnContext->setAuthnContextClassRef($authnContextClassRef);

        $authnStatement = new AuthnStatement();
        $authnStatement->setAuthnContext($authnContext);
        $sessionIndex = $this->sessionInfoProvider->getSessionIndex();
        if ($sessionIndex) {
            $authnStatement->setSessionIndex($sessionIndex);
        }
        $authnInstant = $this->sessionInfoProvider->getAuthnInstant() ?: new \DateTime();
        $authnStatement->setAuthnInstant($authnInstant);

        $subjectLocality = new SubjectLocality();
        $subjectLocality->setAddress($context->getProfileContext()->getHttpRequest()->getClientIp());
        $authnStatement->setSubjectLocality($subjectLocality);

        $context->getAssertion()->addItem($authnStatement);
    }
}

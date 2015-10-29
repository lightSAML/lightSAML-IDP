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
use LightSaml\Provider\TimeProvider\TimeProviderInterface;
use LightSaml\Model\Assertion\AudienceRestriction;
use LightSaml\Model\Assertion\Conditions;
use Psr\Log\LoggerInterface;

/**
 * Creates Conditions and AudienceRestriction.
 */
class ConditionsAction extends AbstractAssertionAction
{
    /** @var  TimeProviderInterface */
    protected $timeProvider;

    /** @var  int */
    protected $expirationSeconds;

    /**
     * @param LoggerInterface       $logger
     * @param TimeProviderInterface $timeProvider
     * @param int                   $expirationSeconds
     */
    public function __construct(LoggerInterface $logger, TimeProviderInterface $timeProvider, $expirationSeconds)
    {
        parent::__construct($logger);

        $this->expirationSeconds = $expirationSeconds;
        $this->timeProvider = $timeProvider;
    }

    /**
     * @param AssertionContext $context
     *
     * @return void
     */
    protected function doExecute(AssertionContext $context)
    {
        $partyEntityDescriptor = $context->getProfileContext()->getPartyEntityDescriptor();

        $conditions = new Conditions();
        $conditions->setNotBefore($this->timeProvider->getTimestamp());
        $conditions->setNotOnOrAfter($conditions->getNotBeforeTimestamp() + $this->expirationSeconds);

        $audienceRestriction = new AudienceRestriction(array(
            $partyEntityDescriptor->getEntityID(),
        ));
        $conditions->addItem($audienceRestriction);

        $context->getAssertion()->setConditions($conditions);
    }
}

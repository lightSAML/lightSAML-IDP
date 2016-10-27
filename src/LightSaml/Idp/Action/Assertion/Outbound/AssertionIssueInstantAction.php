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
use LightSaml\Context\Profile\Helper\LogHelper;
use LightSaml\Provider\TimeProvider\TimeProviderInterface;
use Psr\Log\LoggerInterface;

class AssertionIssueInstantAction extends AbstractAssertionAction
{
    /** @var TimeProviderInterface */
    protected $timeProvider;

    /**
     * @param LoggerInterface       $logger
     * @param TimeProviderInterface $timeProvider
     */
    public function __construct(LoggerInterface $logger, TimeProviderInterface $timeProvider)
    {
        parent::__construct($logger);

        $this->timeProvider = $timeProvider;
    }

    /**
     * @param AssertionContext $context
     *
     * @return void
     */
    protected function doExecute(AssertionContext $context)
    {
        $context->getAssertion()->setIssueInstant($this->timeProvider->getTimestamp());

        $this->logger->info(
            sprintf('Assertion IssueInstant set to "%s"', $context->getAssertion()->getIssueInstantString()),
            LogHelper::getActionContext($context, $this)
        );
    }
}

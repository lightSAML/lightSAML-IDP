<?php

namespace LightSaml\Idp\Action\Assertion\Outbound;

use LightSaml\Action\Assertion\AbstractAssertionAction;
use LightSaml\Context\Profile\AssertionContext;
use LightSaml\Context\Profile\Helper\LogHelper;
use Psr\Log\LoggerInterface;

class AssertionVersionAction extends AbstractAssertionAction
{
    /** @var  string */
    private $version;

    /**
     * @param LoggerInterface $logger
     * @param string          $version
     */
    public function __construct(LoggerInterface $logger, $version)
    {
        parent::__construct($logger);

        $this->version = $version;
    }

    /**
     * @param AssertionContext $context
     *
     * @return void
     */
    protected function doExecute(AssertionContext $context)
    {
        $context->getAssertion()->setVersion($this->version);

        $this->logger->debug(
            sprintf('Assertion Version set to "%s"', $this->version),
            LogHelper::getActionContext($context, $this)
        );
    }
}

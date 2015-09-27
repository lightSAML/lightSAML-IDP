<?php

namespace LightSaml\Idp\Action\Assertion\Outbound;

use LightSaml\Action\Assertion\AbstractAssertionAction;
use LightSaml\Context\Profile\AssertionContext;
use LightSaml\Model\Assertion\Subject;
use LightSaml\Provider\NameID\NameIdProviderInterface;
use Psr\Log\LoggerInterface;

/**
 * Creates Subject if not already created, and sets NameID to it with the value provided by name id provider
 */
class SubjectNameIdAction extends AbstractAssertionAction
{
    /** @var  NameIDProviderInterface */
    protected $nameIdProvider;

    /**
     * @param LoggerInterface         $logger
     * @param NameIDProviderInterface $nameIdProvider
     */
    public function __construct(LoggerInterface $logger, NameIdProviderInterface $nameIdProvider)
    {
        parent::__construct($logger);

        $this->nameIdProvider = $nameIdProvider;
    }

    /**
     * @param AssertionContext $context
     *
     * @return void
     */
    protected function doExecute(AssertionContext $context)
    {
        $nameId = $this->nameIdProvider->getNameID($context);
        if ($nameId) {
            if (null == $context->getAssertion()->getSubject()) {
                $context->getAssertion()->setSubject(new Subject());
            }
            $context->getAssertion()->getSubject()->setNameID($nameId);
        }
    }
}

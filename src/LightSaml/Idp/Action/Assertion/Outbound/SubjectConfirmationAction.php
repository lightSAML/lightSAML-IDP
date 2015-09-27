<?php

namespace LightSaml\Idp\Action\Assertion\Outbound;

use LightSaml\Action\Assertion\AbstractAssertionAction;
use LightSaml\Context\Profile\AssertionContext;
use LightSaml\Provider\TimeProvider\TimeProviderInterface;
use LightSaml\Model\Assertion\Subject;
use LightSaml\Model\Assertion\SubjectConfirmation;
use LightSaml\Model\Assertion\SubjectConfirmationData;
use LightSaml\SamlConstants;
use Psr\Log\LoggerInterface;

/**
 * Creates SubjectConfirmation and creates Subject if not already created
 */
class SubjectConfirmationAction extends AbstractAssertionAction
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
    public function __construct(
        LoggerInterface $logger,
        TimeProviderInterface $timeProvider,
        $expirationSeconds
    ) {
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
        $profileContext = $context->getProfileContext();
        $inboundMessage = $profileContext->getInboundMessage();
        $endpoint = $profileContext->getEndpoint();

        $data = new SubjectConfirmationData();
        if ($inboundMessage) {
            $data->setInResponseTo($inboundMessage->getID());
        }
        $data->setAddress($profileContext->getHttpRequest()->getClientIp());
        $data->setNotOnOrAfter($this->timeProvider->getTimestamp() + $this->expirationSeconds);
        $data->setRecipient($endpoint->getLocation());

        $subjectConfirmation = new SubjectConfirmation();
        $subjectConfirmation->setMethod(SamlConstants::CONFIRMATION_METHOD_BEARER);
        $subjectConfirmation->setSubjectConfirmationData($data);

        if (null === $context->getAssertion()->getSubject()) {
            $context->getAssertion()->setSubject(new Subject());
        }

        $context->getAssertion()->getSubject()->addSubjectConfirmation($subjectConfirmation);
    }
}

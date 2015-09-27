<?php

namespace LightSaml\Idp\Action\Profile\Outbound\StatusResponse;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\Helper\MessageContextHelper;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Protocol\Status;
use LightSaml\Model\Protocol\StatusCode;
use LightSaml\SamlConstants;
use Psr\Log\LoggerInterface;

class SetStatusAction extends AbstractProfileAction
{
    /** @var string */
    protected $statusCode;

    /** @var  string */
    protected $statusMessage;

    /**
     * @param LoggerInterface $logger
     * @param string          $statusCode
     * @param string          $statusMessage
     */
    public function __construct(LoggerInterface $logger, $statusCode = SamlConstants::STATUS_SUCCESS, $statusMessage = null)
    {
        parent::__construct($logger);

        $this->statusCode = $statusCode;
        $this->statusMessage = $statusMessage;
    }

    /**
     * @param ProfileContext $context
     */
    protected function doExecute(ProfileContext $context)
    {
        $statusResponse = MessageContextHelper::asStatusResponse($context->getOutboundContext());

        $statusResponse->setStatus(new Status(new StatusCode($this->statusCode), $this->statusCode));
    }
}

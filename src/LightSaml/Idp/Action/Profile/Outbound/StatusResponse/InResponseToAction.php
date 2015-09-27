<?php

namespace LightSaml\Idp\Action\Profile\Outbound\StatusResponse;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\Helper\MessageContextHelper;
use LightSaml\Context\Profile\ProfileContext;

/**
 * Sets the id of the outbound StatusResponse to the value of id of the inbound message
 */
class InResponseToAction extends AbstractProfileAction
{
    /**
     * @param ProfileContext $context
     *
     * @return void
     */
    protected function doExecute(ProfileContext $context)
    {
        if ($context->getInboundMessage()) {
            MessageContextHelper::asStatusResponse($context->getOutboundContext())->setInResponseTo(
                MessageContextHelper::asSamlMessage($context->getInboundContext())->getID()
            );
        }
    }
}

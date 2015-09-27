<?php

namespace LightSaml\Idp\Action\Profile\Inbound\AuthnRequest;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\Helper\LogHelper;
use LightSaml\Context\Profile\Helper\MessageContextHelper;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Error\LightSamlValidationException;

class ACSUrlValidatorAction extends AbstractProfileAction
{
    /**
     * @param ProfileContext $context
     *
     * @return void
     */
    protected function doExecute(ProfileContext $context)
    {
        $authnRequest = MessageContextHelper::asAuthnRequest($context->getInboundContext());

        if (false == $authnRequest->getAssertionConsumerServiceURL()) {
            return;
        }

        $spEntityDescriptor = $context->getPartyEntityDescriptor();

        foreach ($spEntityDescriptor->getAllSpSsoDescriptors() as $sp) {
            if ($sp->getAllAssertionConsumerServicesByUrl($authnRequest->getAssertionConsumerServiceURL())) {
                $this->logger->debug(
                    sprintf(
                        'AuthnRequest has assertion consumer url "%s" that belongs to entity "%s"',
                        $authnRequest->getAssertionConsumerServiceURL(),
                        $spEntityDescriptor->getEntityID()
                    ),
                    LogHelper::getActionContext($context, $this)
                );

                return;
            }
        }

        $message = sprintf(
            "Invalid ACS Url '%s' for '%s' entity",
            $authnRequest->getAssertionConsumerServiceURL(),
            $spEntityDescriptor->getEntityID()
        );
        $this->logger->emergency($message, LogHelper::getActionErrorContext($context, $this));
        throw new LightSamlValidationException($message);
    }
}

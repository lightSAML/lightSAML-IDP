<?php

namespace LightSaml\Idp\Builder\Action\Profile\SingleSignOn\Idp;

use LightSaml\Action\Profile\Inbound\Message\ResolvePartyEntityIdAction;
use LightSaml\Action\Profile\Outbound\Message\CreateMessageIssuerAction;
use LightSaml\Action\Profile\Outbound\Message\ResolveEndpointSpAcsAction;
use LightSaml\Action\Profile\Outbound\Message\SendMessageAction;
use LightSaml\Idp\Action\Profile\Outbound\Response\CreateResponseAction;
use LightSaml\Idp\Action\Profile\Outbound\StatusResponse\SetStatusAction;
use LightSaml\Builder\Action\Profile\AbstractProfileActionBuilder;
use LightSaml\SamlConstants;

class SsoIdpResponseErrorActionBuilder extends AbstractProfileActionBuilder
{
    /**
     * @return void
     */
    protected function doInitialize()
    {
        $this->add(new ResolvePartyEntityIdAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getPartyContainer()->getSpEntityDescriptorStore(),
            $this->buildContainer->getPartyContainer()->getIdpEntityDescriptorStore(),
            $this->buildContainer->getPartyContainer()->getTrustOptionsStore()
        ), 100);
        $this->add(new CreateResponseAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));
        $this->add(new CreateMessageIssuerAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));
        $this->add(new SetStatusAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            SamlConstants::STATUS_REQUESTER
        ));
        $this->add(new ResolveEndpointSpAcsAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getEndpointResolver()
        ));

        // Send
        $this->add(new SendMessageAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getBindingFactory()
        ), 400);
    }
}

<?php

/*
 * This file is part of the LightSAML-IDP package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the GPL-3 license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Idp\Builder\Action\Profile\SingleSignOn\Idp;

use LightSaml\Idp\Action\Profile\Inbound\AuthnRequest\ACSUrlValidatorAction;
use LightSaml\Action\Profile\Inbound\Message\DestinationValidatorAuthnRequestAction;
use LightSaml\Action\Profile\Inbound\Message\EntityIdFromMessageIssuerAction;
use LightSaml\Action\Profile\Inbound\Message\ResolvePartyEntityIdAction;
use LightSaml\Action\Profile\Inbound\Message\MessageSignatureValidatorAction;
use LightSaml\Action\Profile\Inbound\Message\ReceiveMessageAction;
use LightSaml\Action\Profile\Inbound\Message\IssuerValidatorAction;
use LightSaml\Action\Profile\Outbound\Message\ResolveEndpointSpAcsAction;
use LightSaml\Builder\Action\Profile\AbstractProfileActionBuilder;
use LightSaml\SamlConstants;

class SsoIdpReceiveRequestActionBuilder extends AbstractProfileActionBuilder
{
    /**
     * @return void
     */
    protected function doInitialize()
    {
        // Receive
        $this->add(new ReceiveMessageAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getBindingFactory()
        ), 100);

        // AuthnRequest validation
        $this->add(new EntityIdFromMessageIssuerAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ), 200);
        $this->add(new ResolvePartyEntityIdAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getPartyContainer()->getSpEntityDescriptorStore(),
            $this->buildContainer->getPartyContainer()->getIdpEntityDescriptorStore(),
            $this->buildContainer->getPartyContainer()->getTrustOptionsStore()
        ));
        $this->add(new DestinationValidatorAuthnRequestAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getEndpointResolver()
        ));
        $this->add(new IssuerValidatorAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getNameIdValidator(),
            SamlConstants::NAME_ID_FORMAT_ENTITY
        ));
        $this->add(new ACSUrlValidatorAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));
        $this->add(new MessageSignatureValidatorAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getSignatureValidator()
        ));

        $this->add(new ResolveEndpointSpAcsAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getEndpointResolver()
        ), 300);
    }
}

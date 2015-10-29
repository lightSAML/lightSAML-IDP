<?php

/*
 * This file is part of the LightSAML-IDP package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the GPL-3 license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Idp\Builder\Profile\WebBrowserSso\Idp;

use LightSaml\Build\Container\BuildContainerInterface;
use LightSaml\Builder\Action\ActionBuilderInterface;
use LightSaml\Idp\Builder\Action\Profile\SingleSignOn\Idp\SsoIdpSendResponseActionBuilder;
use LightSaml\Builder\Profile\AbstractProfileBuilder;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Meta\TrustOptions\TrustOptions;
use LightSaml\Model\Metadata\Endpoint;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Model\Protocol\SamlMessage;
use LightSaml\Profile\Profiles;

class SsoIdpSendResponseProfileBuilder extends AbstractProfileBuilder
{
    /** @var ActionBuilderInterface[] */
    private $assertionBuilders = array();

    /** @var string */
    private $entityId;

    /** @var  EntityDescriptor */
    private $partyEntityDescriptor;

    /** @var  TrustOptions */
    private $partyTrustOptions;

    /** @var  Endpoint */
    private $endpoint;

    /** @var  SamlMessage */
    private $message;

    /**
     * @param BuildContainerInterface  $buildContainer
     * @param ActionBuilderInterface[] $assertionBuilders
     * @param string                   $entityId
     */
    public function __construct(BuildContainerInterface $buildContainer, array $assertionBuilders, $entityId)
    {
        parent::__construct($buildContainer);

        $this->entityId = $entityId;
        foreach ($assertionBuilders as $builder) {
            $this->addAssertionBuilder($builder);
        }
    }

    /**
     * @param EntityDescriptor $entityDescriptor
     *
     * @return SsoIdpSendResponseProfileBuilder
     */
    public function setPartyEntityDescriptor(EntityDescriptor $entityDescriptor)
    {
        $this->partyEntityDescriptor = $entityDescriptor;

        return $this;
    }

    /**
     * @param TrustOptions $partyTrustOptions
     *
     * @return SsoIdpSendResponseProfileBuilder
     */
    public function setPartyTrustOptions(TrustOptions $partyTrustOptions)
    {
        $this->partyTrustOptions = $partyTrustOptions;

        return $this;
    }

    /**
     * @param Endpoint $endpoint
     *
     * @return SsoIdpSendResponseProfileBuilder
     */
    public function setEndpoint(Endpoint $endpoint)
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * @param SamlMessage $message
     *
     * @return SsoIdpSendResponseProfileBuilder
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @param ActionBuilderInterface $assertionBuilder
     */
    private function addAssertionBuilder(ActionBuilderInterface $assertionBuilder)
    {
        $this->assertionBuilders[] = $assertionBuilder;
    }

    /**
     * @return string
     */
    protected function getProfileId()
    {
        return Profiles::SSO_IDP_SEND_RESPONSE;
    }

    /**
     * @return string
     */
    protected function getProfileRole()
    {
        return ProfileContext::ROLE_IDP;
    }

    /**
     * @return SsoIdpSendResponseActionBuilder
     */
    protected function getActionBuilder()
    {
        $result = new SsoIdpSendResponseActionBuilder($this->container);

        foreach ($this->assertionBuilders as $assertionAction) {
            $result->addAssertionBuilder($assertionAction);
        }

        return $result;
    }

    /**
     * @return ProfileContext
     */
    public function buildContext()
    {
        $result = parent::buildContext();

        $result->getPartyEntityContext()->setEntityId($this->entityId);

        if ($this->partyEntityDescriptor) {
            $result->getPartyEntityContext()->setEntityDescriptor($this->partyEntityDescriptor);
        }

        if ($this->partyTrustOptions) {
            $result->getPartyEntityContext()->setTrustOptions($this->partyTrustOptions);
        }

        if ($this->endpoint) {
            $result->getEndpointContext()->setEndpoint($this->endpoint);
        }

        if ($this->message) {
            $result->getInboundContext()->setMessage($this->message);
        }

        return $result;
    }
}

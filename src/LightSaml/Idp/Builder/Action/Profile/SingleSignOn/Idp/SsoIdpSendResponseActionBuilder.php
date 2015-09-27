<?php

namespace LightSaml\Idp\Builder\Action\Profile\SingleSignOn\Idp;

use LightSaml\Action\Profile\Inbound\Message\ResolvePartyEntityIdAction;
use LightSaml\Action\Profile\Outbound\Message\CreateMessageIssuerAction;
use LightSaml\Action\Profile\Outbound\Message\DestinationAction;
use LightSaml\Action\Profile\Outbound\Message\ForwardRelayStateAction;
use LightSaml\Action\Profile\Outbound\Message\MessageIdAction;
use LightSaml\Action\Profile\Outbound\Message\MessageIssueInstantAction;
use LightSaml\Action\Profile\Outbound\Message\ResolveEndpointSpAcsAction;
use LightSaml\Action\Profile\Outbound\Message\SendMessageAction;
use LightSaml\Action\Profile\Outbound\Message\SignMessageAction;
use LightSaml\Action\Profile\Outbound\Message\MessageVersionAction;
use LightSaml\Idp\Action\Profile\Outbound\Response\HandleAssertionsAction;
use LightSaml\Idp\Action\Profile\Outbound\Response\CreateResponseAction;
use LightSaml\Idp\Action\Profile\Outbound\StatusResponse\InResponseToAction;
use LightSaml\Idp\Action\Profile\Outbound\StatusResponse\SetStatusAction;
use LightSaml\Builder\Action\ActionBuilderInterface;
use LightSaml\Builder\Action\Profile\AbstractProfileActionBuilder;
use LightSaml\Error\LightSamlException;
use LightSaml\SamlConstants;

class SsoIdpSendResponseActionBuilder extends AbstractProfileActionBuilder
{
    /** @var ActionBuilderInterface[] */
    private $assertionActions = array();

    /**
     * @param ActionBuilderInterface $assertionBuilder
     *
     * @return SsoIdpSendResponseActionBuilder
     */
    public function addAssertionBuilder(ActionBuilderInterface $assertionBuilder)
    {
        $this->assertionActions[] = $assertionBuilder;

        return $this;
    }

    /**
     * @return void
     */
    protected function doInitialize()
    {
        if (empty($this->assertionActions)) {
            throw new LightSamlException('No assertion builder set');
        }

        // Receive
        $this->add(new ResolvePartyEntityIdAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getPartyContainer()->getSpEntityDescriptorStore(),
            $this->buildContainer->getPartyContainer()->getIdpEntityDescriptorStore(),
            $this->buildContainer->getPartyContainer()->getTrustOptionsStore()
        ), 100);

        // Response building
        $this->add(new ResolveEndpointSpAcsAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getEndpointResolver()
        ), 300);
        $this->add(new CreateResponseAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));
        $this->add(new ForwardRelayStateAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));
        $this->add(new MessageIdAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));
        $this->add(new MessageVersionAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            SamlConstants::VERSION_20
        ));
        $this->add(new MessageIssueInstantAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getSystemContainer()->getTimeProvider()
        ));
        $this->add(new DestinationAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));
        $this->add(new CreateMessageIssuerAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));
        $this->add(new SignMessageAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getSignatureResolver()
        ));

        $assertionAction = new HandleAssertionsAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        );
        foreach ($this->assertionActions as $assertionActionBuilder) {
            $assertionAction->add($assertionActionBuilder->build());
        }
        $this->add($assertionAction);

        $this->add(new InResponseToAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));

        $this->add(new SetStatusAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));

        // Send
        $this->add(new SendMessageAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getBindingFactory()
        ), 400);
    }
}

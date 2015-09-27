<?php

namespace LightSaml\Idp\Builder\Action\Profile\SingleSignOn\Idp;

use LightSaml\Idp\Action\Assertion\Outbound\AssertionIdAction;
use LightSaml\Idp\Action\Assertion\Outbound\AssertionIssueInstantAction;
use LightSaml\Idp\Action\Assertion\Outbound\AssertionVersionAction;
use LightSaml\Idp\Action\Assertion\Outbound\AttributeAction;
use LightSaml\Idp\Action\Assertion\Outbound\AuthnStatementAction;
use LightSaml\Idp\Action\Assertion\Outbound\ConditionsAction;
use LightSaml\Idp\Action\Assertion\Outbound\CreateAssertionAction;
use LightSaml\Idp\Action\Assertion\Outbound\CreateAssertionIssuerAction;
use LightSaml\Idp\Action\Assertion\Outbound\EncryptAssertionAction;
use LightSaml\Idp\Action\Assertion\Outbound\IdpSsoStateAction;
use LightSaml\Idp\Action\Assertion\Outbound\SignAssertionAction;
use LightSaml\Idp\Action\Assertion\Outbound\SubjectConfirmationAction;
use LightSaml\Idp\Action\Assertion\Outbound\SubjectNameIdAction;
use LightSaml\Builder\Action\Profile\AbstractProfileActionBuilder;
use LightSaml\SamlConstants;

class SsoIdpAssertionActionBuilder extends AbstractProfileActionBuilder
{
    /**
     * @return void
     */
    protected function doInitialize()
    {
        $this->add(new CreateAssertionAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ), 100);
        $this->add(new AssertionIdAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));
        $this->add(new AssertionIssueInstantAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getSystemContainer()->getTimeProvider()
        ));
        $this->add(new AssertionVersionAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            SamlConstants::VERSION_20
        ));

        $this->add(new CreateAssertionIssuerAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));

        $this->add(new SubjectNameIdAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getProviderContainer()->getNameIdProvider()
        ));
        $this->add(new SubjectConfirmationAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getSystemContainer()->getTimeProvider(),
            120
        ));

        $this->add(new ConditionsAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getSystemContainer()->getTimeProvider(),
            120
        ));

        $this->add(new AttributeAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getProviderContainer()->getAttributeValueProvider(),
            120
        ));

        $this->add(new AuthnStatementAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getProviderContainer()->getSessionInfoProvider()
        ));

        $this->add(new SignAssertionAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getSignatureResolver()
        ));
        $this->add(new IdpSsoStateAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getSessionProcessor()
        ));

        $this->add(new EncryptAssertionAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getCredentialResolver()
        ));
    }
}

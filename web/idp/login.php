<?php

require_once __DIR__.'/_config.php';

$buildContext = IdpConfig::current()->getBuildContainer();
$receiveBuilder = new \LightSaml\Idp\Builder\Profile\WebBrowserSso\Idp\SsoIdpReceiveAuthnRequestProfileBuilder($buildContext);

$context = $receiveBuilder->buildContext();
$action = $receiveBuilder->buildAction();

$action->execute($context);

$partyContext = $context->getPartyEntityContext();
$endpoint = $context->getEndpoint();
$message = $context->getInboundMessage();

$sendBuilder = new \LightSaml\Idp\Builder\Profile\WebBrowserSso\Idp\SsoIdpSendResponseProfileBuilder(
    $buildContext,
    array(new \LightSaml\Idp\Builder\Action\Profile\SingleSignOn\Idp\SsoIdpAssertionActionBuilder($buildContext)),
    $partyContext->getEntityDescriptor()->getEntityID()
);
$sendBuilder->setPartyEntityDescriptor($partyContext->getEntityDescriptor());
$sendBuilder->setPartyTrustOptions($partyContext->getTrustOptions());
$sendBuilder->setEndpoint($endpoint);
$sendBuilder->setMessage($message);

$context = $sendBuilder->buildContext();
$action = $sendBuilder->buildAction();

$action->execute($context);

$context->getHttpResponseContext()->getResponse()->send();

<?php

require_once __DIR__.'/_config.php';

$spEntityId = @$_GET['sp'];
if (null == $spEntityId) {
    header('Location: discovery.php');
    exit;
}
$spEntityDescriptor = IdpConfig::current()->getBuildContainer()->getPartyContainer()->getSpEntityDescriptorStore()->get($spEntityId);
if (null == $spEntityDescriptor) {
    header('Location: discovery.php');
    exit;
}

$buildContainer = IdpConfig::current()->getBuildContainer();

$criteriaSet = new \LightSaml\Criteria\CriteriaSet([
    new \LightSaml\Resolver\Endpoint\Criteria\BindingCriteria([\LightSaml\SamlConstants::BINDING_SAML2_HTTP_POST]),
    new \LightSaml\Resolver\Endpoint\Criteria\DescriptorTypeCriteria(\LightSaml\Model\Metadata\SpSsoDescriptor::class),
    new \LightSaml\Resolver\Endpoint\Criteria\ServiceTypeCriteria(\LightSaml\Model\Metadata\AssertionConsumerService::class)
]);
$arrEndpoints = IdpConfig::current()->getBuildContainer()->getServiceContainer()->getEndpointResolver()->resolve($criteriaSet, $spEntityDescriptor->getAllEndpoints());
if (empty($arrEndpoints)) {
    throw new \RuntimeException(sprintf('SP party "%s" does not have any SP ACS endpoint defined', $spEntityId));
}

$endpoint = $arrEndpoints[0]->getEndpoint();
$trustOptions = IdpConfig::current()->getBuildContainer()->getPartyContainer()->getTrustOptionsStore()->get($spEntityId);

$sendBuilder = new \LightSaml\Idp\Builder\Profile\WebBrowserSso\Idp\SsoIdpSendResponseProfileBuilder(
    $buildContainer,
    array(new \LightSaml\Idp\Builder\Action\Profile\SingleSignOn\Idp\SsoIdpAssertionActionBuilder($buildContainer)),
    $spEntityId
);
$sendBuilder->setPartyEntityDescriptor($spEntityDescriptor);
$sendBuilder->setPartyTrustOptions($trustOptions);
$sendBuilder->setEndpoint($endpoint);

$context = $sendBuilder->buildContext();
$action = $sendBuilder->buildAction();

$action->execute($context);

$context->getHttpResponseContext()->getResponse()->send();

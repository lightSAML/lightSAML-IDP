<?php

namespace LightSaml\Idp\Builder\Profile\WebBrowserSso\Idp;

use LightSaml\Idp\Builder\Action\Profile\SingleSignOn\Idp\SsoIdpReceiveRequestActionBuilder;
use LightSaml\Builder\Profile\AbstractProfileBuilder;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Profile\Profiles;

class SsoIdpReceiveAuthnRequestProfileBuilder extends AbstractProfileBuilder
{
    /**
     * @return string
     */
    protected function getProfileId()
    {
        return Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST;
    }

    /**
     * @return string
     */
    protected function getProfileRole()
    {
        return ProfileContext::ROLE_IDP;
    }

    /**
     * @return \LightSaml\Builder\Action\ActionBuilderInterface
     */
    protected function getActionBuilder()
    {
        return new SsoIdpReceiveRequestActionBuilder($this->container);
    }
}

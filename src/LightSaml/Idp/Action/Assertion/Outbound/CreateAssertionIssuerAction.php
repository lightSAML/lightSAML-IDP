<?php

/*
 * This file is part of the LightSAML-IDP package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the GPL-3 license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Idp\Action\Assertion\Outbound;

use LightSaml\Action\Assertion\AbstractAssertionAction;
use LightSaml\Context\Profile\AssertionContext;
use LightSaml\Context\Profile\Helper\LogHelper;
use LightSaml\Model\Assertion\Issuer;
use LightSaml\SamlConstants;

/**
 * Creates Issuer with value of the own entityID.
 */
class CreateAssertionIssuerAction extends AbstractAssertionAction
{
    /**
     * @param AssertionContext $context
     *
     * @return void
     */
    protected function doExecute(AssertionContext $context)
    {
        $ownEntityDescriptor = $context->getProfileContext()->getOwnEntityDescriptor();

        $issuer = new Issuer($ownEntityDescriptor->getEntityID());
        $issuer->setFormat(SamlConstants::NAME_ID_FORMAT_ENTITY);

        $context->getAssertion()->setIssuer($issuer);

        $this->logger->debug(
            sprintf('Assertion Issuer set to "%s"', $ownEntityDescriptor->getEntityID()),
            LogHelper::getActionContext($context, $this)
        );
    }
}

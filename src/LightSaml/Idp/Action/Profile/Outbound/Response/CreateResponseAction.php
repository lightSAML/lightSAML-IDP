<?php

namespace LightSaml\Idp\Action\Profile\Outbound\Response;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Protocol\Response;

class CreateResponseAction extends AbstractProfileAction
{
    protected function doExecute(ProfileContext $context)
    {
        $context->getOutboundContext()->setMessage(new Response());
    }
}

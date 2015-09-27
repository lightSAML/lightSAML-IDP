<?php

namespace LightSaml\Idp\Action\Assertion\Outbound;

use LightSaml\Action\Assertion\AbstractAssertionAction;
use LightSaml\Context\Profile\AssertionContext;
use LightSaml\Context\Profile\Helper\LogHelper;
use LightSaml\Helper;

class AssertionIdAction extends AbstractAssertionAction
{
    /**
     * @param AssertionContext $context
     *
     * @return void
     */
    protected function doExecute(AssertionContext $context)
    {
        $id = Helper::generateID();
        $context->getAssertion()->setId($id);

        $this->logger->info(
            sprintf('Assertion ID set to "%s"', $id),
            LogHelper::getActionContext($context, $this, array('message_id' => $id))
        );
    }
}

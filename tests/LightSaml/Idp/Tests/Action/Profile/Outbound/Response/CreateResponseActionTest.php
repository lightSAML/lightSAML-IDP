<?php

namespace LightSaml\Idp\Tests\Action\Profile\Outbound\Response;

use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Idp\Action\Profile\Outbound\Response\CreateResponseAction;
use LightSaml\Model\Protocol\Response;
use LightSaml\Profile\Profiles;
use Psr\Log\LoggerInterface;

class CreateResponseActionTest extends \PHPUnit_Framework_TestCase
{
    public function testCreatesResponse()
    {
        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $action = new CreateResponseAction($this->getLoggerMock());
        $action->execute($context);

        $this->assertInstanceOf(Response::class, $context->getOutboundMessage());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Psr\Log\LoggerInterface
     */
    private function getLoggerMock()
    {
        return $this->getMock(LoggerInterface::class);
    }
}

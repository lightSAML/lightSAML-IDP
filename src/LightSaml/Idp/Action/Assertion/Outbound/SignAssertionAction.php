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
use LightSaml\Resolver\Signature\SignatureResolverInterface;
use Psr\Log\LoggerInterface;

/**
 * If TrustOptions::getSignAssertions is true, sets.
 */
class SignAssertionAction extends AbstractAssertionAction
{
    /** @var  SignatureResolverInterface */
    protected $signatureResolver;

    /**
     * @param LoggerInterface            $logger
     * @param SignatureResolverInterface $signatureResolver
     */
    public function __construct(LoggerInterface $logger, SignatureResolverInterface $signatureResolver)
    {
        parent::__construct($logger);

        $this->signatureResolver = $signatureResolver;
    }

    /**
     * @param AssertionContext $context
     *
     * @return void
     */
    protected function doExecute(AssertionContext $context)
    {
        $profileContext = $context->getProfileContext();
        $trustOptions = $profileContext->getTrustOptions();

        if ($trustOptions->getSignAssertions()) {
            $signature = $this->signatureResolver->getSignature($profileContext);
            if ($signature) {
                $this->logger->debug(
                    sprintf(
                        'Signing assertion with fingerprint %s',
                        $signature->getCertificate()->getFingerprint()
                    ),
                    LogHelper::getActionContext($context, $this, array(
                        'certificate' => $signature->getCertificate()->getInfo(),
                    ))
                );
                $context->getAssertion()->setSignature($signature);
            } else {
                $this->logger->critical(
                    'Unable to resolve assertion signature, though signing enabled',
                    LogHelper::getActionErrorContext($context, $this)
                );
            }
        } else {
            $this->logger->debug('Assertion signing disabled', LogHelper::getActionContext($context, $this));
        }
    }
}

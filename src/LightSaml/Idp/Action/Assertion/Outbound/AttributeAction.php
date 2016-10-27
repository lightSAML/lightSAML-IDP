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
use LightSaml\Model\Assertion\AttributeStatement;
use LightSaml\Provider\Attribute\AttributeValueProviderInterface;
use Psr\Log\LoggerInterface;

/**
 * Creates AttributeStatement and sets attribute values provided by given attribute value provider.
 */
class AttributeAction extends AbstractAssertionAction
{
    /** @var AttributeValueProviderInterface */
    protected $attributeValueProvider;

    /**
     * @param LoggerInterface                 $logger
     * @param AttributeValueProviderInterface $attributeValueProvider
     */
    public function __construct(LoggerInterface $logger, AttributeValueProviderInterface $attributeValueProvider)
    {
        parent::__construct($logger);

        $this->attributeValueProvider = $attributeValueProvider;
    }

    /**
     * @param AssertionContext $context
     *
     * @return void
     */
    protected function doExecute(AssertionContext $context)
    {
        $attributes = $this->attributeValueProvider->getValues($context);
        if ($attributes) {
            $attributeStatement = new AttributeStatement();
            $context->getAssertion()->addItem($attributeStatement);
            foreach ($attributes as $attribute) {
                $attributeStatement->addAttribute($attribute);
            }
        }
    }
}

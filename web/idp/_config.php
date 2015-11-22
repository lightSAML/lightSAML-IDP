<?php

require_once __DIR__.'/../../vendor/autoload.php';

class IdpConfig
{
    const OWN_ENTITY_ID = 'https://localhost/lightSAML/lightSAML-IDP';

    /** @var  \SpConfig */
    private static $instance;

    public $debug = true;

    /**
     * @return \IdpConfig
     */
    public static function current()
    {
        if (null == self::$instance) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * @return \LightSaml\Build\Container\BuildContainerInterface
     */
    public function getBuildContainer()
    {
        $pimple = new \Pimple\Container();
        $result = new \LightSaml\Bridge\Pimple\Container\BuildContainer($pimple);
        $this->buildOwnContext($result);
        $this->buildSystemContext($result);
        $this->buildPartyContext($result);
        $this->buildStoreContext($result);
        $this->buildProviderContext($result);
        $this->buildCredentialContext($result);
        $this->buildServiceContext($result);

        return $result;
    }

    /**
     * @param \LightSaml\Bridge\Pimple\Container\BuildContainer $buildContainer
     */
    private function buildOwnContext(\LightSaml\Bridge\Pimple\Container\BuildContainer $buildContainer)
    {
        $ownCredential = $this->buildOwnCredential();
        $ownEntityDescriptorProvider = $this->buildOwnEntityDescriptorProvider($ownCredential->getCertificate());

        $buildContainer->getPimple()->register(
            new \LightSaml\Bridge\Pimple\Container\Factory\OwnContainerProvider(
                $ownEntityDescriptorProvider,
                [$ownCredential]
            )
        );
    }

    /**
     * @param \LightSaml\Bridge\Pimple\Container\BuildContainer $buildContainer
     */
    private function buildSystemContext(\LightSaml\Bridge\Pimple\Container\BuildContainer $buildContainer)
    {
        $buildContainer->getPimple()->register(new \LightSaml\Bridge\Pimple\Container\Factory\SystemContainerProvider());

        $pimple = $buildContainer->getPimple();
        $pimple[\LightSaml\Bridge\Pimple\Container\SystemContainer::LOGGER] = function () {
            return $this->buildLogger();

        };
        $pimple[\LightSaml\Bridge\Pimple\Container\SystemContainer::SESSION] = function () {
            return $this->buildSession();

        };
    }

    /**
     * @param \LightSaml\Bridge\Pimple\Container\BuildContainer $buildContainer
     */
    private function buildPartyContext(\LightSaml\Bridge\Pimple\Container\BuildContainer $buildContainer)
    {
        $buildContainer->getPimple()->register(new \LightSaml\Bridge\Pimple\Container\Factory\PartyContainerProvider());

        $pimple = $buildContainer->getPimple();
        $pimple[\LightSaml\Bridge\Pimple\Container\PartyContainer::SP_ENTITY_DESCRIPTOR] = function () {
            return $this->buildSpEntityStore();
        };
        $pimple[\LightSaml\Bridge\Pimple\Container\PartyContainer::TRUST_OPTIONS_STORE] = function () {
            $trustOptions = new \LightSaml\Meta\TrustOptions\TrustOptions();

            return new \LightSaml\Store\TrustOptions\FixedTrustOptionsStore($trustOptions);
        };
    }

    /**
     * @param \LightSaml\Bridge\Pimple\Container\BuildContainer $buildContainer
     */
    private function buildStoreContext(\LightSaml\Bridge\Pimple\Container\BuildContainer $buildContainer)
    {
        $buildContainer->getPimple()->register(
            new \LightSaml\Bridge\Pimple\Container\Factory\StoreContainerProvider(
                $buildContainer->getSystemContainer()
            )
        );
    }

    /**
     * @param \LightSaml\Bridge\Pimple\Container\BuildContainer $buildContainer
     */
    private function buildProviderContext(\LightSaml\Bridge\Pimple\Container\BuildContainer $buildContainer)
    {
        $buildContainer->getPimple()->register(
            new \LightSaml\Bridge\Pimple\Container\Factory\ProviderContainerProvider()
        );

        $pimple = $buildContainer->getPimple();
        $pimple[\LightSaml\Bridge\Pimple\Container\ProviderContainer::ATTRIBUTE_VALUE_PROVIDER] = function () {
            return (new \LightSaml\Provider\Attribute\FixedAttributeValueProvider())
                ->add(new \LightSaml\Model\Assertion\Attribute(
                    \LightSaml\ClaimTypes::COMMON_NAME,
                    'common-name'
                ))
                ->add(new \LightSaml\Model\Assertion\Attribute(
                    \LightSaml\ClaimTypes::GIVEN_NAME,
                    'first'
                ))
                ->add(new \LightSaml\Model\Assertion\Attribute(
                    \LightSaml\ClaimTypes::SURNAME,
                    'last'
                ))
                ->add(new \LightSaml\Model\Assertion\Attribute(
                    \LightSaml\ClaimTypes::EMAIL_ADDRESS,
                    'somebody@example.com'
                ));

        };

        $pimple[\LightSaml\Bridge\Pimple\Container\ProviderContainer::SESSION_INFO_PROVIDER] = function () {
            return new \LightSaml\Provider\Session\FixedSessionInfoProvider(
                time() - 600,
                'session-index',
                \LightSaml\SamlConstants::AUTHN_CONTEXT_PASSWORD_PROTECTED_TRANSPORT
            );
        };

        $pimple[\LightSaml\Bridge\Pimple\Container\ProviderContainer::NAME_ID_PROVIDER] = function () use ($buildContainer) {
            $nameId = new \LightSaml\Model\Assertion\NameID('name@id.com');
            $nameId
                ->setFormat(\LightSaml\SamlConstants::NAME_ID_FORMAT_EMAIL)
                ->setNameQualifier($buildContainer->getOwnContainer()->getOwnEntityDescriptorProvider()->get()->getEntityID())
            ;

            return new \LightSaml\Provider\NameID\FixedNameIdProvider($nameId);
        };
    }

    /**
     * @param \LightSaml\Bridge\Pimple\Container\BuildContainer $buildContainer
     */
    private function buildCredentialContext(\LightSaml\Bridge\Pimple\Container\BuildContainer $buildContainer)
    {
        $buildContainer->getPimple()->register(
            new \LightSaml\Bridge\Pimple\Container\Factory\CredentialContainerProvider(
                $buildContainer->getPartyContainer(),
                $buildContainer->getOwnContainer()
            )
        );
    }

    /**
     * @param \LightSaml\Bridge\Pimple\Container\BuildContainer $buildContainer
     */
    private function buildServiceContext(\LightSaml\Bridge\Pimple\Container\BuildContainer $buildContainer)
    {
        $buildContainer->getPimple()->register(
            new \LightSaml\Bridge\Pimple\Container\Factory\ServiceContainerProvider(
                $buildContainer->getCredentialContainer(),
                $buildContainer->getStoreContainer(),
                $buildContainer->getSystemContainer()
            )
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Session\Session
     */
    private function buildSession()
    {
        $session = new \Symfony\Component\HttpFoundation\Session\Session();
        $session->setName('PHPSIDIDP');
        $session->start();

        return $session;
    }

    /**
     * @return \LightSaml\Credential\X509Credential
     */
    private function buildOwnCredential()
    {
        $ownCredential = new \LightSaml\Credential\X509Credential(
            (new \LightSaml\Credential\X509Certificate())
                ->loadPem(file_get_contents(__DIR__.'/saml.crt')),
            \LightSaml\Credential\KeyHelper::createPrivateKey(__DIR__.'/saml.key', null, true)
        );
        $ownCredential
            ->setEntityId(self::OWN_ENTITY_ID)
        ;

        return $ownCredential;
    }

    /**
     * @param \LightSaml\Credential\X509Certificate $certificate
     *
     * @return \LightSaml\Provider\EntityDescriptor\EntityDescriptorProviderInterface
     */
    private function buildOwnEntityDescriptorProvider(\LightSaml\Credential\X509Certificate $certificate)
    {
        return new \LightSaml\Builder\EntityDescriptor\SimpleEntityDescriptorBuilder(
            self::OWN_ENTITY_ID,
            null,
            'https://localhost/lightsaml/lightSAML-IDP/web/idp/login.php',
            $certificate
        );
    }

    /**
     * @return \LightSaml\Store\EntityDescriptor\FixedEntityDescriptorStore
     */
    private function buildSpEntityStore()
    {
        $idpProvider = new \LightSaml\Store\EntityDescriptor\FixedEntityDescriptorStore();
        $idpProvider->add(
            \LightSaml\Model\Metadata\EntityDescriptor::load(__DIR__.'/localhost-lightsaml-demosp.xml')
        );
        $idpProvider->add(
            \LightSaml\Model\Metadata\EntityDescriptor::load(__DIR__.'/localhost-lightsaml-lightsaml.xml')
        );

        return $idpProvider;
    }

    /**
     * @return \Monolog\Logger
     */
    private function buildLogger()
    {
        $logger = new \Monolog\Logger('lightsaml', array(new \Monolog\Handler\StreamHandler(__DIR__.'/idp.log')));

        return $logger;
    }
}

<?php

declare(strict_types=1);

use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ApiPlatformClient;
use Sylius\Behat\Client\Request;
use Sylius\Behat\Client\RequestInterface;
use Sylius\Bundle\CoreBundle\Application\Kernel;
use SyliusLabs\Sylius111ShimBundle\Behat\ApiClient\LegacyApiClientInterface;
use SyliusLabs\Sylius111ShimBundle\Behat\ApiClient\LegacyApiPlatformClient;
use SyliusLabs\Sylius111ShimBundle\Behat\ApiClient\LegacyRequest;
use SyliusLabs\Sylius111ShimBundle\Behat\ApiClient\LegacyRequestInterface;

if (Kernel::VERSION_ID < 11200) {
    class_alias(ApiClientInterface::class, '\\Sylius1_11\\Behat\\Client\\ApiClientInterface');
    class_alias(ApiPlatformClient::class, '\\Sylius1_11\\Behat\\Client\\ApiPlatformClient');
    class_alias(Request::class, '\\Sylius1_11\\Behat\\Client\\Request');
    class_alias(RequestInterface::class, '\\Sylius1_11\\Behat\\Client\\RequestInterface');
} else {
    class_alias(LegacyApiClientInterface::class, 'Sylius1_11\\Behat\\Client\\ApiClientInterface');
    class_alias(LegacyApiPlatformClient::class, '\\Sylius1_11\\Behat\\Client\\ApiPlatformClient');
    class_alias(LegacyRequest::class, '\\Sylius1_11\\Behat\\Client\\Request');
    class_alias(LegacyRequestInterface::class, '\\Sylius1_11\\Behat\\Client\\RequestInterface');
}

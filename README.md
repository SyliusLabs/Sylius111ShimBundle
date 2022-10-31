# Sylius 1.11 Shim Bundle

> ⚠️ This package is considered unstable and under active development until 1.x release.

## ⚙️ Installation

1. Require the bundle with composer:

    ```bash
    $ composer require sylius-labs/sylius-1-11-shim-bundle --dev
    ```

2. Add the bundle to your `config/bundles.php` file:

    ```php
    <?php
    
    return [
        // ...
        SyliusLabs\Sylius111ShimBundle\SyliusLabsSylius111ShimBundle::class => ['test' => true, 'test_cached' => true],
    ];
    ```
   
## 📖 Usage

### Legacy Behat API Platform client

Sylius 1.12 came with a refactored API Platform client for Behat tests. This may lead to harder supporting of Sylius 1.11 and 1.12 in the same time.
To make it easier, we've created a legacy client that can be used in plugins supporting Sylius 1.11 and 1.12.

> ℹ️ `Sylius1_11\Behat\Client\*` classes are suitable for both 1.12 and earlier versions of Sylius.

- For `1.11` and earlier compatibility, use `sylius.behat.api_platform_client.legacy` instead `sylius.behat.api_platform_client`
- All `1.11` and earlier `sylius.behat.api_platform_client.*` services are again available to use
- `Sylius\Behat\Client\ApiClientInterface` from Sylius 1.12 became incompatible with `Sylius\Behat\Client\ApiClientInterface` from Sylius 1.11 and earlier. To make it compatible, use `Sylius1_11\Behat\Client\ApiClientInterface` instead
- `Sylius\Behat\Client\ApiPlatformClient` from Sylius 1.12 became incompatible with `Sylius\Behat\Client\ApiPlatformClient` from Sylius 1.11 and earlier. To make it compatible, use `Sylius1_11\Behat\Client\ApiPlatformClient` instead
- `Sylius\Behat\Client\RequestInterface` from Sylius 1.12 became incompatible with `Sylius\Behat\Client\RequestInterface` from Sylius 1.11 and earlier. To make it compatible, use `Sylius1_11\Behat\Client\RequestInterface` instead
- `Sylius\Behat\Client\Request` from Sylius 1.12 became incompatible with `Sylius\Behat\Client\Request` from Sylius 1.11 and earlier. To make it compatible, use `Sylius1_11\Behat\Client\Request` instead

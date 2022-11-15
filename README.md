# Sylius 1.11 Shim Bundle

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

### Restore `sylius.behat.context.hook.email_spool` service
`sylius.behat.context.hook.email_spool` has been replace by `sylius.behat.context.hook.mailer`. This shim adds an alias pointing the old name to the new one.

## 📝 Examples

### Already using one of services with changed signatures.

Just replace `Sylius` with `Sylius1_11`. All services with changed signatures has been aliased to this scheme of naming.

```diff
use Behat\Behat\Context\Context;
-use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\LoyaltyPlugin\Domain\Model\CustomerInterface;
+use Sylius1_11\Behat\Client\ApiClientInterface;
use Webmozart\Assert\Assert;

final class ManagingLoyaltyContext implements Context
{
    public function __construct(
        private ApiClientInterface $apiAdminClient,
        private SharedStorageInterface $sharedStorage,
        private ResponseCheckerInterface $responseChecker,
    ) {
    }
```

### Having an API Client for a custom resource

Replace `sylius.behat.api_platform_client` parent declaration with `sylius.behat.api_platform_client.legacy` and `Sylius\Behat\Client\ApiPlatformClient` class with `Sylius1_11\Behat\Client\ApiPlatformClient`.

```diff
-<service id="app.behat.api_platform_client.admin.points" class="Sylius\Behat\Client\ApiPlatformClient" parent="sylius.behat.api_platform_client">
+<service id="app.behat.api_platform_client.admin.points" class="Sylius1_11\Behat\Client\ApiPlatformClient" parent="sylius.behat.api_platform_client.legacy">
    <argument>loyalty-points-accounts</argument>
    <argument>admin</argument>
</service>
```

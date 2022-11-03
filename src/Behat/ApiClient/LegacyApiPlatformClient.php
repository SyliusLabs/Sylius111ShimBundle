<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SyliusLabs\Sylius111ShimBundle\Behat\ApiClient;

use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\RequestFactoryInterface;
use Sylius\Behat\Client\RequestInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use SyliusLabs\Sylius111ShimBundle\Behat\ApiClient\LegacyRequest as Request;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response;

final class LegacyApiPlatformClient implements LegacyApiClientInterface
{
    private LegacyRequestInterface|RequestInterface|null $request = null;

    public function __construct(
        private AbstractBrowser $client,
        private SharedStorageInterface $sharedStorage,
        private string $authorizationHeader,
        private string $resource,
        private ?string $section = null,
        private ?ApiClientInterface $newShopApiClient = null,
        private ?ApiClientInterface $newAdminApiClient = null,
        private ?RequestFactoryInterface $requestFactory = null,
    ) {
    }

    public function index(): Response
    {
        if ($this->useNewApiClient()) {
            return $this->getNewApiClient()->index($this->resource);
        }

        $this->request = Request::index($this->section, $this->resource, $this->authorizationHeader, $this->getToken());

        return $this->request($this->request);
    }

    public function showByIri(string $iri): Response
    {
        if ($this->useNewApiClient()) {
            return $this->getNewApiClient()->showByIri($iri);
        }

        $request = Request::custom($iri, HttpRequest::METHOD_GET);
        $request->authorize($this->getToken(), $this->authorizationHeader);

        return $this->request($request);
    }

    public function subResourceIndex(string $subResource, string $id): Response
    {
        if ($this->useNewApiClient()) {
            $this->getNewApiClient()->subResourceIndex($this->resource, $subResource, $id);
        }

        $request = Request::subResourceIndex($this->section, $this->resource, $id, $subResource);
        $request->authorize($this->getToken(), $this->authorizationHeader);

        return $this->request($request);
    }

    public function show(string $id): Response
    {
        if ($this->useNewApiClient()) {
            $this->getNewApiClient()->show($this->resource, $id);
        }

        return $this->request(Request::show(
            $this->section,
            $this->resource,
            $id,
            $this->authorizationHeader,
            $this->getToken(),
        ));
    }

    public function create(LegacyRequestInterface|RequestInterface|null $request = null): Response
    {
        if ($this->useNewApiClient()) {
            return $this->getNewApiClient()->create($request);
        }

        return $this->request($request ?? $this->request);
    }

    public function update(): Response
    {
        if ($this->useNewApiClient()) {
            return $this->getNewApiClient()->update();
        }

        return $this->request($this->request);
    }

    public function delete(string $id): Response
    {
        if ($this->useNewApiClient()) {
            return $this->getNewApiClient()->delete($this->resource, $id);
        }

        return $this->request(Request::delete(
            $this->section,
            $this->resource,
            $id,
            $this->authorizationHeader,
            $this->getToken(),
        ));
    }

    public function filter(): Response
    {
        if ($this->useNewApiClient()) {
            return $this->getNewApiClient()->filter();
        }

        return $this->request($this->request);
    }

    public function sort(array $sorting): Response
    {
        if ($this->useNewApiClient()) {
            return $this->getNewApiClient()->sort($sorting);
        }

        $this->request->updateParameters(['order' => $sorting]);

        return $this->request($this->request);
    }

    public function applyTransition(string $id, string $transition, array $content = []): Response
    {
        if ($this->useNewApiClient()) {
            return $this->getNewApiClient()->applyTransition($this->resource, $id, $transition, $content);
        }

        $request = Request::transition($this->section, $this->resource, $id, $transition);
        $request->authorize($this->getToken(), $this->authorizationHeader);
        $request->setContent($content);

        return $this->request($request);
    }

    public function customItemAction(string $id, string $type, string $action): Response
    {
        if ($this->useNewApiClient()) {
            return $this->getNewApiClient()->customItemAction($this->resource, $id, $type, $action);
        }

        $request = Request::customItemAction($this->section, $this->resource, $id, $type, $action);
        $request->authorize($this->getToken(), $this->authorizationHeader);

        return $this->request($request);
    }

    public function customAction(string $url, string $method): Response
    {
        if ($this->useNewApiClient()) {
            return $this->getNewApiClient()->customAction($url, $method);
        }

        $request = Request::custom($url, $method);

        $request->authorize($this->getToken(), $this->authorizationHeader);

        return $this->request($request);
    }

    public function upload(): Response
    {
        if ($this->useNewApiClient()) {
            return $this->getNewApiClient()->update();
        }

        return $this->request($this->request);
    }

    public function executeCustomRequest(LegacyRequestInterface|RequestInterface $request): Response
    {
        if ($this->useNewApiClient()) {
            return $this->getNewApiClient()->executeCustomRequest($request);
        }

        $request->authorize($this->getToken(), $this->authorizationHeader);

        return $this->request($request);
    }

    public function buildCreateRequest(): void
    {
        if ($this->useNewApiClient()) {
            $this->getNewApiClient()->buildCreateRequest($this->resource);
            return;
        }

        $this->request = Request::create($this->section, $this->resource, $this->authorizationHeader);
        $this->request->authorize($this->getToken(), $this->authorizationHeader);
    }

    public function buildUpdateRequest(string $id): void
    {
        if ($this->useNewApiClient()) {
            $this->getNewApiClient()->buildUpdateRequest($this->resource, $id);

            return;
        }

        $this->show($id);

        $this->request = Request::update(
            $this->section,
            $this->resource,
            $id,
            $this->authorizationHeader,
            $this->getToken(),
        );
        $this->request->setContent(json_decode($this->client->getResponse()->getContent(), true));
    }

    public function buildCustomUpdateRequest(string $id, string $customSuffix): void
    {
        if ($this->useNewApiClient()) {
            $this->getNewApiClient()->buildCustomUpdateRequest($this->resource, $id, $customSuffix);

            return;
        }

        $this->request = Request::update(
            $this->section,
            $this->resource,
            sprintf('%s/%s', $id, $customSuffix),
            $this->authorizationHeader,
            $this->getToken(),
        );
    }

    public function buildUploadRequest(): void
    {
        if ($this->useNewApiClient()) {
            $this->getNewApiClient()->buildCreateRequest($this->resource);

            return;
        }

        $this->request = Request::upload($this->section, $this->resource, $this->authorizationHeader, $this->getToken());
    }

    /** @param int|string $value */
    public function addParameter(string $key, $value): void
    {
        if ($this->useNewApiClient()) {
            $this->getNewApiClient()->addParameter($key, $value);

            return;
        }

        $this->request->updateParameters([$key => $value]);
    }

    public function setRequestData(array $data): void
    {
        if ($this->useNewApiClient()) {
            $this->getNewApiClient()->setRequestData($data);

            return;
        }

        $this->request->setContent($data);
    }

    /** @param int|string $value */
    public function addFilter(string $key, $value): void
    {
        if ($this->useNewApiClient()) {
            $this->getNewApiClient()->addFilter($key, $value);

            return;
        }

        $this->addParameter($key, $value);
    }

    public function clearParameters(): void
    {
        if ($this->useNewApiClient()) {
            $this->getNewApiClient()->clearParameters();

            return;
        }

        $this->request->clearParameters();
    }

    public function addFile(string $key, UploadedFile $file): void
    {
        if ($this->useNewApiClient()) {
            $this->getNewApiClient()->addFile($key, $file);

            return;
        }

        $this->request->updateFiles([$key => $file]);
    }

    /**
     * @param array|int|string $value
     */
    public function addRequestData(string $key, $value): void
    {
        if ($this->useNewApiClient()) {
            $this->getNewApiClient()->addRequestData($key, $value);

            return;
        }

        $this->request->updateContent([$key => $value]);
    }

    public function updateRequestData(array $data): void
    {
        if ($this->useNewApiClient()) {
            $this->getNewApiClient()->updateRequestData($data);

            return;
        }

        $this->request->updateContent($data);
    }

    public function setSubResourceData(string $key, array $data): void
    {
        if ($this->useNewApiClient()) {
            $this->getNewApiClient()->setSubResourceData($key, $data);

            return;
        }

        $this->request->setSubResource($key, $data);
    }

    public function addSubResourceData(string $key, array $data): void
    {
        if ($this->useNewApiClient()) {
            $this->getNewApiClient()->addSubResourceData($key, $data);

            return;
        }

        $this->request->addSubResource($key, $data);
    }

    public function removeSubResource(string $subResource, string $id): void
    {
        if ($this->useNewApiClient()) {
            $this->getNewApiClient()->removeSubResource($subResource, $id);

            return;
        }

        $this->request->removeSubResource($subResource, $id);
    }

    public function getContent(): array
    {
        if ($this->useNewApiClient()) {
            return $this->getNewApiClient()->getContent();
        }

        return $this->request->getContent();
    }

    public function getLastResponse(): Response
    {
        if ($this->useNewApiClient()) {
            return $this->getNewApiClient()->getLastResponse();
        }

        /** @var Response $response */
        $response = $this->client->getResponse();

        return $response;
    }

    public function getToken(): ?string
    {
        if ($this->useNewApiClient()) {
            return $this->getNewApiClient()->getToken();
        }

        return $this->sharedStorage->has('token') ? $this->sharedStorage->get('token') : null;
    }

    private function request(LegacyRequestInterface|RequestInterface $request): Response
    {
        if ($this->sharedStorage->has('hostname')) {
            $this->client->setServerParameter('HTTP_HOST', $this->sharedStorage->get('hostname'));
        }

        $this->client->request(
            $request->method(),
            $request->url(),
            $request->parameters(),
            $request->files(),
            $request->headers(),
            $request->content() ?? null,
        );

        return $this->getLastResponse();
    }

    private function useNewApiClient(): bool
    {
        return null !== $this->newShopApiClient && null !== $this->newAdminApiClient && null !== $this->requestFactory;
    }

    private function getNewApiClient(): ApiClientInterface
    {
        if ($this->section === 'admin') {
            return $this->newAdminApiClient;
        }

        return $this->newShopApiClient;
    }
}

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

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

interface LegacyApiClientInterface
{
    public function index(): Response;

    public function showByIri(string $iri): Response;

    public function subResourceIndex(string $subResource, string $id): Response;

    public function show(string $id): Response;

    public function create(?LegacyRequestInterface $request = null): Response;

    public function update(): Response;

    public function delete(string $id): Response;

    public function filter(): Response;

    public function sort(array $sorting): Response;

    public function applyTransition(string $id, string $transition, array $content = []): Response;

    public function customItemAction(string $id, string $type, string $action): Response;

    public function customAction(string $url, string $method): Response;

    public function upload(): Response;

    public function executeCustomRequest(LegacyRequestInterface $request): Response;

    public function buildCreateRequest(): void;

    public function buildUpdateRequest(string $id): void;

    public function buildUploadRequest(): void;

    public function setRequestData(array $data): void;

    /**
     * @param int|string $value
     */
    public function addParameter(string $key, $value): void;

    /**
     * @param int|string $value
     */
    public function addFilter(string $key, $value): void;

    public function clearParameters(): void;

    public function addFile(string $key, UploadedFile $file): void;

    /**
     * @param array|int|string $value
     */
    public function addRequestData(string $key, $value): void;

    public function setSubResourceData(string $key, array $data): void;

    public function addSubResourceData(string $key, array $data): void;

    public function removeSubResource(string $subResource, string $id): void;

    public function updateRequestData(array $data): void;

    public function getContent(): array;

    public function getLastResponse(): Response;

    public function getToken(): ?string;
}

class_alias(LegacyApiClientInterface::class, 'Sylius1_11\\Behat\\Client\\ApiClientInterface');

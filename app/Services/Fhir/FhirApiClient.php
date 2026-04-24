<?php

namespace App\Services\Fhir;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class FhirApiClient
{
    private string $baseUrl;
    private int $timeoutSeconds;

    public function __construct()
    {
        $baseUrl = (string) config('services.fhir.base_url');
        $this->baseUrl = rtrim($this->resolveBaseUrlForDocker($baseUrl), '/');
        $this->timeoutSeconds = (int) config('services.fhir.timeout', 10);
    }

    /**
     * @param array<string, scalar> $params
     * @return array<string, mixed>
     */
    public function search(string $resourceType, array $params = []): array
    {
        $response = $this->send(fn () => $this->request()->get("{$this->baseUrl}/{$resourceType}", $params));
        $this->throwIfFailed($response);

        return $response->json();
    }

    /**
     * @return array<string, mixed>
     */
    public function read(string $resourceType, string $id): array
    {
        $response = $this->send(fn () => $this->request()->get("{$this->baseUrl}/{$resourceType}/{$id}"));
        $this->throwIfFailed($response);

        return $response->json();
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function create(string $resourceType, array $payload): array
    {
        $response = $this->send(fn () => $this->request()->post("{$this->baseUrl}/{$resourceType}", $payload));
        $this->throwIfFailed($response);

        return $response->json();
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function update(string $resourceType, string $id, array $payload): array
    {
        $response = $this->send(fn () => $this->request()->put("{$this->baseUrl}/{$resourceType}/{$id}", $payload));
        $this->throwIfFailed($response);

        return $response->json();
    }

    private function request(): PendingRequest
    {
        return Http::acceptJson()
            ->asJson()
            ->timeout($this->timeoutSeconds);
    }

    private function resolveBaseUrlForDocker(string $baseUrl): string
    {
        if ($baseUrl === '') {
            return $baseUrl;
        }

        if (!is_file('/.dockerenv')) {
            return $baseUrl;
        }

        $host = (string) parse_url($baseUrl, PHP_URL_HOST);
        if (!in_array($host, ['localhost', '127.0.0.1'], true)) {
            return $baseUrl;
        }

        return str_replace($host, 'host.docker.internal', $baseUrl);
    }

    private function throwIfFailed(Response $response): void
    {
        if ($response->successful()) {
            return;
        }

        $json = $response->json();
        if (is_array($json) && ($json['resourceType'] ?? null) === 'OperationOutcome') {
            throw FhirApiException::fromOperationOutcome($json, $response->status());
        }

        $response->throw();
    }

    /**
     * @param callable(): Response $callback
     */
    private function send(callable $callback): Response
    {
        try {
            return $callback();
        } catch (ConnectionException $exception) {
            throw FhirApiException::fromConnectionException($exception);
        }
    }
}

<?php

namespace App\Services\Fhir;

use Illuminate\Http\Client\ConnectionException;
use RuntimeException;

class FhirApiException extends RuntimeException
{
    public function __construct(
        string $message,
        int $code = 0,
        private readonly string $errorKey = 'UNKNOWN_ERROR'
    ) {
        parent::__construct($message, $code);
    }

    /**
     * @param array<string, mixed> $operationOutcome
     */
    public static function fromOperationOutcome(array $operationOutcome, int $statusCode): self
    {
        $issue = $operationOutcome['issue'][0] ?? [];
        $diagnostics = $issue['diagnostics'] ?? null;
        $detailsText = $issue['details']['text'] ?? null;
        $issueCode = (string) ($issue['code'] ?? 'unknown');
        $message = self::normalizeMessage($diagnostics, $detailsText, $issueCode);
        $errorKey = self::normalizeErrorKey($diagnostics, $issueCode, $statusCode);

        return new self($message, $statusCode, $errorKey);
    }

    public static function fromConnectionException(ConnectionException $exception): self
    {
        $message = $exception->getMessage();

        if (str_contains(strtolower($message), 'timed out') || str_contains($message, 'cURL error 28')) {
            return new self(
                'The request timed out. Please try again.',
                408,
                'TIMEOUT'
            );
        }

        return new self(
            'Unable to reach the FHIR service right now. Please check the network connection and try again.',
            503,
            'NETWORK_ERROR'
        );
    }

    public function errorKey(): string
    {
        return $this->errorKey;
    }

    private static function normalizeMessage(mixed $diagnostics, mixed $detailsText, string $issueCode): string
    {
        $diagnosticsText = is_string($diagnostics) ? $diagnostics : '';
        $details = is_string($detailsText) ? $detailsText : '';
        $combined = strtolower(trim($diagnosticsText . ' ' . $details));

        if (str_contains($combined, 'patient/') && str_contains($combined, 'not known')) {
            return 'Patient not found.';
        }

        if (str_contains($combined, 'observation/') && str_contains($combined, 'not known')) {
            return 'Temperature observation not found.';
        }

        if ($issueCode === 'invalid' || str_contains($combined, 'validation')) {
            return $details !== '' ? $details : 'Please review the highlighted fields and try again.';
        }

        if ($details !== '') {
            return $details;
        }

        if ($diagnosticsText !== '') {
            return $diagnosticsText;
        }

        return sprintf('FHIR request failed with issue code: %s', $issueCode);
    }

    private static function normalizeErrorKey(mixed $diagnostics, string $issueCode, int $statusCode): string
    {
        $diagnosticsText = strtolower(is_string($diagnostics) ? $diagnostics : '');

        if (str_contains($diagnosticsText, 'patient/') && str_contains($diagnosticsText, 'not known')) {
            return 'PATIENT_NOT_FOUND';
        }

        if (str_contains($diagnosticsText, 'observation/') && str_contains($diagnosticsText, 'not known')) {
            return 'OBSERVATION_NOT_FOUND';
        }

        if ($issueCode === 'invalid') {
            return 'VALIDATION_ERROR';
        }

        if ($statusCode >= 500) {
            return 'UNKNOWN_ERROR';
        }

        return strtoupper($issueCode ?: 'UNKNOWN_ERROR');
    }
}

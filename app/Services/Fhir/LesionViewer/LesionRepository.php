<?php

namespace App\Services\Fhir\LesionViewer;

interface LesionRepository
{
    /**
     * Return all lesion viewer records as read-only view models.
     *
     * @return array<int, array<string, mixed>>
     */
    public function all(): array;

    /**
     * Find one lesion viewer record by lesion id.
     *
     * @return array<string, mixed>|null
     */
    public function find(string $lesionId): ?array;

    /**
     * Return metadata about the repository source.
     *
     * @return array<string, mixed>
     */
    public function meta(): array;
}

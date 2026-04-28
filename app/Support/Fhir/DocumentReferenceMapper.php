<?php

namespace App\Support\Fhir;

use App\ViewModels\DocumentReferenceVM;
use Carbon\CarbonImmutable;

class DocumentReferenceMapper
{
    /**
     * Phase 3 field-sync baseline note:
     * current baseline is URL-based attachment mapping.
     * Binary upload and secure retrieval policy remain Phase 3 follow-up items.
     */
    /**
     * @param array<string, mixed> $resource
     */
    public static function fromFhirDocumentReference(array $resource): DocumentReferenceVM
    {
        $subjectReference = (string) data_get($resource, 'subject.reference', '');
        $patientId = self::extractIdFromReference($subjectReference);

        $title = data_get($resource, 'content.0.attachment.title');
        if (!is_string($title) || $title === '') {
            $title = data_get($resource, 'description');
        }
        $title = is_string($title) && $title !== '' ? $title : null;

        $url = data_get($resource, 'content.0.attachment.url');
        $url = is_string($url) && $url !== '' ? $url : null;

        $contentType = data_get($resource, 'content.0.attachment.contentType');
        $contentType = is_string($contentType) && $contentType !== '' ? $contentType : null;

        $date = data_get($resource, 'date');
        if (!is_string($date) || $date === '') {
            $date = data_get($resource, 'meta.lastUpdated');
        }
        $date = is_string($date) && $date !== '' ? $date : null;

        return new DocumentReferenceVM(
            id: (string) ($resource['id'] ?? ''),
            patientId: $patientId,
            title: $title,
            url: $url,
            contentType: $contentType,
            date: $date,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function toFhirDocumentReference(DocumentReferenceVM $vm): array
    {
        $title = trim((string) ($vm->title ?? ''));
        $url = trim((string) ($vm->url ?? ''));

        $resource = [
            'resourceType' => 'DocumentReference',
            'status' => 'current',
            'subject' => [
                'reference' => "Patient/{$vm->patientId}",
            ],
            'date' => CarbonImmutable::now()->toIso8601String(),
            'content' => [
                [
                    'attachment' => [
                        'url' => $url,
                        'contentType' => $vm->contentType ?: 'text/uri-list',
                    ],
                ],
            ],
        ];

        if ($title !== '') {
            $resource['description'] = $title;
            $resource['content'][0]['attachment']['title'] = $title;
        }

        if ($vm->id !== '') {
            $resource['id'] = $vm->id;
        }

        return $resource;
    }

    private static function extractIdFromReference(string $reference): string
    {
        if ($reference === '') {
            return '';
        }

        $parts = explode('/', $reference);
        return (string) end($parts);
    }
}

<?php

namespace App\ViewModels;

class DocumentReferenceVM
{
    public function __construct(
        public string $id,
        public string $patientId,
        public ?string $title = null,
        public ?string $url = null,
        public ?string $contentType = null,
        public ?string $date = null,
    ) {
    }
}

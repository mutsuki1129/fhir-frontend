<?php

namespace App\ViewModels;

class ConditionVM
{
    public function __construct(
        public string $id,
        public string $patientId,
        public ?string $code = null,
        public ?string $text = null,
        public ?string $recordedDate = null,
        public ?string $note = null,
    ) {
    }
}

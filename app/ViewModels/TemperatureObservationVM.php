<?php

namespace App\ViewModels;

class TemperatureObservationVM
{
    public function __construct(
        public string $id,
        public string $patientId,
        public ?string $patientDisplay = null,
        public ?string $performerId = null,
        public ?string $performerDisplay = null,
        public float $valueCelsius = 0.0,
        public ?string $effectiveDateTime = null,
        public ?string $note = null,
    ) {
    }
}

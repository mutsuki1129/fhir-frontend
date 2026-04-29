<?php

namespace App\ViewModels;

class PatientVM
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $photoUrl = null,
        public ?string $birthDate = null,
        public ?string $gender = null,
        public ?string $education = null,
        public ?string $occupation = null,
        public ?string $income = null,
        public ?string $expense = null,
        public ?string $interests = null,
        public ?string $psychologicalTraits = null,
        public ?string $behaviorPatterns = null,
        public ?string $biomarkers = null,
        public ?string $nationalId = null,
        public ?string $nhiCardNumber = null,
        public ?string $generalPractitionerId = null,
        public ?string $generalPractitionerDisplay = null,
    ) {
    }
}

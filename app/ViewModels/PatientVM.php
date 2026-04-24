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
    ) {
    }
}

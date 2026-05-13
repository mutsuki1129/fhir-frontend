<?php

namespace Tests\Feature\Fhir;

use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ControlledIngestionPrototypeUiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('fhir.controlled_ingestion_prototype', [
            'enabled' => true,
            'mode' => 'mock',
            'allow_fhir_write' => false,
        ]);
    }

    public function test_dev_only_page_displays_mock_no_write_safety_status(): void
    {
        $this->get('/dev/fhir/mock-ingestion')
            ->assertOk()
            ->assertSee('Dev-only Mock Ingestion Prototype')
            ->assertSee('Dev-only')
            ->assertSee('Mock-only')
            ->assertSee('No real PHI')
            ->assertSee('No production FHIR Server')
            ->assertSee('No direct FHIR write')
            ->assertSee('No AI Agent runtime')
            ->assertSee('No CDS runtime')
            ->assertSee('No SMART production')
            ->assertSee('Not formal ingestion')
            ->assertSee('Not lesion CRUD')
            ->assertSee('Not FHIR persistence')
            ->assertSee('noFHIRWrite=true')
            ->assertSee('persisted=false')
            ->assertSee('signedOff=false')
            ->assertSee('pending-review')
            ->assertSee('Mock Validation Result')
            ->assertSee('Mock Manual Review Queue Item')
            ->assertSee('Candidate Resource Preview')
            ->assertDontSee('production ingestion enabled')
            ->assertDontSee('clinical advice')
            ->assertDontSee('automatic diagnosis')
            ->assertDontSee('treatment recommendation')
            ->assertDontSee('signed-off')
            ->assertDontSee('written-to-fhir');
    }
}

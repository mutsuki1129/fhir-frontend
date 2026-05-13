<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Dev\FhirMockIngestionController;
use App\Http\Controllers\EncounterController;
use App\Http\Controllers\FhirPortalController;
use App\Http\Controllers\FhirMetadataController;
use App\Http\Controllers\LesionViewerController;
use App\Http\Controllers\RekamController;
use App\Http\Controllers\SmartFhirController;
use App\Http\Controllers\DokterController;
use App\Http\Controllers\PasienController;
use Illuminate\Support\Facades\Route;
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::view('/', 'home')->name('home');

Route::get('/.well-known/smart-configuration', [SmartFhirController::class, 'configuration'])
    ->name('smart.configuration');

Route::prefix('smart')->name('smart.')->group(function () {
    Route::get('/launch', [SmartFhirController::class, 'launch'])->name('launch');
    Route::get('/callback', [SmartFhirController::class, 'callback'])->name('callback');
    Route::get('/status', [SmartFhirController::class, 'status'])->name('status');
    Route::post('/logout', [SmartFhirController::class, 'logout'])->name('logout');
});

Route::get('/locale/{locale}', function (string $locale) {
    if (! in_array($locale, ['en', 'zh_TW'], true)) {
        abort(404);
    }

    session(['locale' => $locale]);

    return redirect()->back();
})->name('locale.switch');

Route::middleware('fhir.controlled_ingestion_prototype')
    ->prefix('dev/fhir/mock-ingestion')
    ->name('dev.fhir.mock-ingestion.')
    ->group(function () {
        Route::get('/', [FhirMockIngestionController::class, 'index'])->name('index');
        Route::post('/preview', [FhirMockIngestionController::class, 'preview'])
            ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->name('preview');
    });

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.admin');
    })->name('dashboard.admin');

    Route::get('/lesions', [LesionViewerController::class, 'index'])->name('lesions.index');
    Route::get('/lesions/{lesion}', [LesionViewerController::class, 'show'])->name('lesions.show');

    Route::get('/fhir', [FhirPortalController::class, 'index'])->name('fhir.index');
    Route::get('/fhir/ig', [FhirPortalController::class, 'ig'])->name('fhir.ig');
    Route::get('/fhir/terminology', [FhirPortalController::class, 'terminology'])->name('fhir.terminology');
    Route::get('/fhir/gateway', [FhirPortalController::class, 'gateway'])->name('fhir.gateway');
    Route::get('/fhir/smart', [FhirPortalController::class, 'smart'])->name('fhir.smart');
    Route::get('/fhir/cds/completeness', [FhirPortalController::class, 'cdsCompleteness'])->name('fhir.cds-completeness');
    Route::get('/fhir/cds/completeness/rules', [FhirPortalController::class, 'cdsCompletenessRules'])->name('fhir.cds-completeness.rules');
    Route::get('/fhir/cds/governance/permissions', [FhirPortalController::class, 'cdsGovernancePermissions'])->name('fhir.cds-governance.permissions');
    Route::get('/fhir/cds/governance/approvals', [FhirPortalController::class, 'cdsGovernanceApprovals'])->name('fhir.cds-governance.approvals');
    Route::get('/fhir/cds/governance/events', [FhirPortalController::class, 'cdsGovernanceEvents'])->name('fhir.cds-governance.events');
    Route::get('/fhir/cds/governance/execution-gate', [FhirPortalController::class, 'cdsExecutionGate'])->name('fhir.cds-governance.execution-gate');
    Route::get('/fhir/cds/governance/runtime-actions', [FhirPortalController::class, 'cdsRuntimeActions'])->name('fhir.cds-governance.runtime-actions');
    Route::get('/fhir/cds/completeness/audits', [FhirPortalController::class, 'cdsCompletenessAudits'])->name('fhir.cds-completeness.audits');
    Route::get('/fhir/cds/completeness/audits/{audit}', [FhirPortalController::class, 'cdsCompletenessAuditDetail'])->name('fhir.cds-completeness.audits.show');
    Route::post('/fhir/cds/completeness/audits/{audit}/review', [FhirPortalController::class, 'reviewCdsCompletenessAudit'])->name('fhir.cds-completeness.audits.review');
    Route::post('/fhir/cds/completeness/audits/{audit}/ignore', [FhirPortalController::class, 'ignoreCdsCompletenessAudit'])->name('fhir.cds-completeness.audits.ignore');
    Route::redirect('/fhir/documents', '/document-references')->name('fhir.documents');
    Route::redirect('/fhir/medication-requests', '/medication-requests')->name('fhir.medication-requests');

    // add rekam medis
    Route::get('/rekam/create', [RekamController::class, 'create'])->name('admin.rekam.create');
    Route::post('/rekam', [RekamController::class, 'store'])->middleware('fhir.frontend_read_only')->name('admin.rekam.store');
    Route::get('/rekam', [RekamController::class, 'show'])->name('admin.rekam.list');
    Route::get('/rekam/pasien', [RekamController::class, 'pasien'])->name('admin.rekam.pasien');
    Route::get('/rekam/dokter', [RekamController::class, 'dokter'])->name('admin.rekam.dokter');
    Route::get('/encounters', [EncounterController::class, 'index'])->name('admin.encounters.index');
    Route::get('/encounters/{encounter}', [EncounterController::class, 'show'])->name('admin.encounters.show');
    Route::get('/diagnostic-reports', [FhirMetadataController::class, 'diagnosticReports'])->name('admin.diagnostic-reports.index');
    Route::get('/diagnostic-reports/{diagnosticReport}', [FhirMetadataController::class, 'diagnosticReport'])->name('admin.diagnostic-reports.show');
    Route::get('/document-references', [FhirMetadataController::class, 'documentReferences'])->name('admin.document-references.index');
    Route::get('/document-references/{documentReference}/download', [FhirMetadataController::class, 'downloadDocumentReference'])->name('admin.document-references.download');
    Route::post('/document-references/{documentReference}/binary', [FhirMetadataController::class, 'uploadDocumentReferenceBinary'])->middleware('fhir.frontend_read_only')->name('admin.document-references.binary.store');
    Route::get('/document-references/{documentReference}', [FhirMetadataController::class, 'documentReference'])->name('admin.document-references.show');
    Route::get('/medication-requests', [FhirMetadataController::class, 'medicationRequests'])->name('admin.medication-requests.index');
    Route::post('/medication-requests', [FhirMetadataController::class, 'storeMedicationRequest'])->middleware('fhir.frontend_read_only')->name('admin.medication-requests.store');
    Route::get('/medication-requests/{medicationRequest}', [FhirMetadataController::class, 'medicationRequest'])->name('admin.medication-requests.show');
    Route::get('/rekam/{rekam}/edit', [RekamController::class, 'edit'])->name('admin.rekam.edit');
    Route::patch('/rekam/{rekam}', [RekamController::class, 'update'])->middleware('fhir.frontend_read_only')->name('admin.rekam.update');
    Route::delete('/rekam/{rekam}', [RekamController::class, 'destroy'])->middleware('fhir.frontend_read_only')->name('admin.rekam.destroy');

    // dokter control
    Route::get('/dokters', [DokterController::class, 'getDokterList'])->name('dokters.list');
    Route::get('/edit-dokter/{id}', [DokterController::class, 'editDokter'])->name('dokters.edit');
    Route::patch('/edit-dokter/{id}', [DokterController::class, 'updateDokter'])->middleware('fhir.frontend_read_only')->name('dokters.update');
    Route::patch('/photo-dokter/{id}', [DokterController::class, 'photoUpload'])->middleware('fhir.frontend_read_only')->name('pictureDokter.update');
    Route::get('/delete-dokter/{id}', [DokterController::class, 'legacyDeleteDokter'])->name('dokters.delete');
    Route::delete('/dokters/{id}', [DokterController::class, 'deleteDokter'])->middleware('fhir.frontend_read_only')->name('dokters.destroy');
    // 顯示表單
	Route::get('/dokters/create', [DokterController::class, 'create'])->name('dokters.create');
	// 接收表單
	Route::post('/dokters', [DokterController::class, 'createDokter'])->middleware('fhir.frontend_read_only')->name('dokters.store');

    // pasien control
    Route::get('/pasiens', [PasienController::class, 'getPasienList'])->name('pasiens.list');
    Route::get('/edit-pasien/{id}', [PasienController::class, 'editPasien'])->name('pasiens.edit');
    Route::patch('/edit-pasien/{id}', [PasienController::class, 'updatePasien'])->middleware('fhir.frontend_read_only')->name('pasiens.update');
    Route::patch('/photo-pasien/{id}', [PasienController::class, 'photoUpload'])->middleware('fhir.frontend_read_only')->name('picturePasien.update');
    Route::get('/delete-pasien/{id}', fn () => redirect()
        ->route('pasiens.list')
        ->withErrors(['fhir' => __('ui.patients.delete_unavailable')]))
        ->name('pasiens.delete.legacy');
    Route::delete('/pasiens/{id}', [PasienController::class, 'deletePasien'])->middleware('fhir.frontend_read_only')->name('pasiens.destroy');
    Route::get('/pasiens/create', [PasienController::class, 'create'])->name('pasiens.create');
	Route::post('/pasiens', [PasienController::class, 'createPasien'])->middleware('fhir.frontend_read_only')->name('pasiens.store');

});

// profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

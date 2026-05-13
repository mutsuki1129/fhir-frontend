<?php

namespace Tests\Feature\Fhir;

use Tests\TestCase;

class FhirGitCheckpointPreparationDocumentationTest extends TestCase
{
    private const PACKAGE_DIR = 'docs/fhir/evidence/fhir-lesion-viewer-phase-10a6-git-checkpoint-preparation-20260512';

    public function test_phase_10a6_evidence_package_exists(): void
    {
        foreach ($this->packageFiles() as $file) {
            $this->assertFileExists(base_path(self::PACKAGE_DIR . '/' . $file));
        }
    }

    public function test_phase_10a6_readme_declares_checkpoint_only_boundary(): void
    {
        $content = file_get_contents(base_path(self::PACKAGE_DIR . '/README.md'));

        foreach ([
            'Phase 10A.6 是 Git checkpoint preparation / selective commit plan',
            '這不是 Phase 10B',
            '這不是 runtime implementation',
            '這不是 Git cleanup',
            '這不是 automatic commit',
            '這不是 automatic branch checkout',
        ] as $statement) {
            $this->assertStringContainsString($statement, $content);
        }
    }

    public function test_phase_10a6_safety_boundary_keeps_runtime_and_git_actions_absent(): void
    {
        $content = file_get_contents(base_path(self::PACKAGE_DIR . '/safety-boundary.md'));

        foreach ([
            '沒有 Gateway runtime',
            '沒有 ingestion runtime',
            '沒有 AI Agent runtime',
            '沒有 validation runtime',
            '沒有 queue worker',
            '沒有 webhook receiver',
            '沒有 FHIR writer service',
            '沒有新增 `POST` / `PATCH` / `DELETE` lesion route',
            '沒有新增 FHIR write route',
            '沒有 live HAPI `$validate`',
            '沒有 SMART production activation',
            '沒有 CDS runtime activation',
            '沒有 clinical advice',
            '沒有 automatic diagnosis',
            '沒有 treatment recommendation',
            '沒有 real PHI',
            '沒有清理 unrelated dirty / untracked changes',
            '沒有自動 git add',
            '沒有自動 git commit',
            '沒有自動 git reset',
            '沒有自動 git checkout',
            '沒有自動 git clean',
            'Phase 10B 尚未開始',
            'Phase 10B 尚未授權',
        ] as $statement) {
            $this->assertStringContainsString($statement, $content);
        }
    }

    public function test_selective_staging_plan_avoids_broad_git_add(): void
    {
        $content = file_get_contents(base_path(self::PACKAGE_DIR . '/selective-staging-plan.md'));

        $this->assertStringContainsString('不要使用：', $content);
        $this->assertStringContainsString('`git add .`', $content);
        $this->assertStringContainsString('`git add -A`', $content);
        $this->assertStringContainsString('實際 commit 前需人工確認 staging', $content);
    }

    private function packageFiles(): array
    {
        return [
            'README.md',
            'working-tree-snapshot.md',
            'fhir-mainline-file-inventory.md',
            'unrelated-dirty-files.md',
            'selective-staging-plan.md',
            'commit-split-recommendation.md',
            'branch-recommendation.md',
            'patch-backup-plan.md',
            'pre-commit-verification-checklist.md',
            'post-commit-verification-checklist.md',
            'route-safety-results.md',
            'runtime-safety-results.md',
            'regression-test-results.md',
            'documentation-results.md',
            'deferred-issues.md',
            'safety-boundary.md',
            'next-phase-plan.md',
        ];
    }
}

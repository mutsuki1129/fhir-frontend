<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dev-only Mock Ingestion Prototype</title>
    <style>
        body { margin: 0; font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; background: #f6f8fb; color: #152033; }
        main { max-width: 1180px; margin: 0 auto; padding: 32px 20px 48px; }
        h1 { margin: 0 0 8px; font-size: 28px; }
        h2 { margin: 28px 0 12px; font-size: 18px; }
        .notice, .panel { background: #ffffff; border: 1px solid #d9e2ee; border-radius: 8px; padding: 18px; }
        .notice { border-left: 6px solid #2764c5; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px; margin-top: 16px; }
        .badge { display: flex; align-items: center; min-height: 44px; border: 1px solid #cbd7e6; border-radius: 6px; padding: 8px 10px; background: #fbfdff; font-weight: 650; }
        textarea { width: 100%; min-height: 360px; box-sizing: border-box; border: 1px solid #b8c7d9; border-radius: 6px; padding: 12px; font-family: ui-monospace, SFMono-Regular, Consolas, monospace; font-size: 13px; line-height: 1.5; }
        button { border: 0; border-radius: 6px; padding: 10px 16px; background: #2764c5; color: #fff; font-weight: 700; cursor: pointer; }
        button:disabled { background: #8292aa; cursor: wait; }
        pre { overflow: auto; min-height: 120px; background: #101827; color: #e9f0fb; border-radius: 6px; padding: 14px; font-size: 13px; line-height: 1.5; }
        .columns { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 18px; }
        @media (max-width: 860px) { .columns { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<main>
    <h1>Dev-only Mock Ingestion Prototype</h1>
    <div class="notice">
        <strong>Safety boundary:</strong> Dev-only, Mock-only, No real PHI, No production FHIR Server,
        No direct FHIR write, No AI Agent runtime, No CDS runtime, No SMART production,
        Feature flag guarded, Candidate preview only.
    </div>

    <div class="grid" aria-label="Feature flag status">
        <div class="badge">Flag: {{ $prototypeConfig['enabled'] ? 'enabled for this environment' : 'disabled' }}</div>
        <div class="badge">Mode: {{ $prototypeConfig['mode'] }}</div>
        <div class="badge">FHIR write: {{ $prototypeConfig['allow_fhir_write'] ? 'blocked by guard' : 'false' }}</div>
        <div class="badge">Runtime: dev-mock-only</div>
        <div class="badge">Feature flag guarded</div>
        <div class="badge">Candidate preview only</div>
    </div>

    <h2>Mock Payload</h2>
    <form id="preview-form">
        @csrf
        <textarea id="payload" name="payload" spellcheck="false">{{ $samplePayload }}</textarea>
        <p><button type="submit" id="preview-button">Preview</button></p>
    </form>

    <div class="columns">
        <section class="panel">
            <h2>Mock Validation Result</h2>
            <pre id="validation-result">{}</pre>
        </section>
        <section class="panel">
            <h2>Mock Manual Review Queue Item</h2>
            <pre id="queue-item">{}</pre>
        </section>
    </div>

    <section class="panel" style="margin-top:18px">
        <h2>Candidate Resource Preview</h2>
        <pre id="candidate-preview">[]</pre>
    </section>
</main>

<script>
    const form = document.getElementById('preview-form');
    const button = document.getElementById('preview-button');
    const token = document.querySelector('input[name="_token"]').value;

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        button.disabled = true;

        const response = await fetch('{{ route('dev.fhir.mock-ingestion.preview') }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({ payload: document.getElementById('payload').value })
        });
        const json = await response.json();

        document.getElementById('validation-result').textContent = JSON.stringify(json.data.validationResult, null, 2);
        document.getElementById('queue-item').textContent = JSON.stringify(json.data.manualReviewQueueItem, null, 2);
        document.getElementById('candidate-preview').textContent = JSON.stringify(json.data.candidateResources, null, 2);
        button.disabled = false;
    });
</script>
</body>
</html>

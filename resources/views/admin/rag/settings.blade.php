@extends('admin.template')

@section('title')
    EDC | PTPN VI
@endsection

@section('page-name')
    AI Prompt Settings
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>AI Prompt Settings</h1>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>Template Prompt RAG</h4>
            </div>
            <div class="card-body">
                <p class="text-muted mb-2">
                    Pengaturan prompt kini dibagi menjadi dua lapisan agar lebih jelas dan fleksibel.
                </p>
                <p class="text-muted mb-3">
                    `Prompt Template` mengatur susunan prompt utama dan placeholder dokumen/pertanyaan.
                    `Prompt Rules` mengatur aturan sistem, format jawaban, dan follow-up questions.
                </p>
                <p class="text-muted mb-3">
                    Placeholder wajib pada template: <code>@{{CONTEXT_BLOCK}}</code> dan <code>@{{QUESTION}}</code>.
                </p>

                <form method="post" action="{{ route('admin.rag.settings.update') }}">
                    @csrf
                    <div class="form-group">
                        <label for="prompt_template">Prompt Template</label>
                        <textarea
                            id="prompt_template"
                            name="prompt_template"
                            class="form-control @error('prompt_template') is-invalid @enderror"
                            rows="18"
                            required
                        >{{ old('prompt_template', $promptTemplate ?? '') }}</textarea>
                        @error('prompt_template')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="prompt_rules">Prompt Rules / System Rules</label>
                        <textarea
                            id="prompt_rules"
                            name="prompt_rules"
                            class="form-control @error('prompt_rules') is-invalid @enderror"
                            rows="12"
                            required
                        >{{ old('prompt_rules', $promptRules ?? '') }}</textarea>
                        <small class="form-text text-muted">
                            Gunakan bagian ini untuk aturan wajib seperti struktur ringkasan, format follow-up questions,
                            gaya jawaban, dan guardrail lainnya.
                        </small>
                        @error('prompt_rules')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan Pengaturan Prompt
                    </button>
                    <a href="{{ route('admin.rag.chat') }}" class="btn btn-light">
                        Kembali ke N4R4 AI Assistance
                    </a>
                </form>
            </div>
        </div>
    </section>
@endsection

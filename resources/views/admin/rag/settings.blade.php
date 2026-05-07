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
                <p class="text-muted mb-3">
                    Di bawah ini juga tersedia pengaturan routing intent agar pertanyaan seperti
                    <code>dokumen SOP apa saja</code> bisa dibedakan dengan follow-up seperti
                    <code>apa saja tanggal cuti bersama berdasarkan dokumen ini</code>.
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

                    <hr>

                    <h5 class="mb-3">Intent Routing Settings</h5>
                    <p class="text-muted mb-3">
                        Setiap baris dapat berisi regex penuh seperti <code>/pola/u</code> atau frase biasa.
                        Jika frase biasa, sistem akan mencocokkan sebagai teks biasa tanpa perlu regex.
                    </p>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input
                                type="checkbox"
                                class="custom-control-input"
                                id="intent_enable_active_document_context"
                                name="intent_enable_active_document_context"
                                value="1"
                                {{ old('intent_enable_active_document_context', $intentActiveDocumentContextEnabled ?? true) ? 'checked' : '' }}
                            >
                            <label class="custom-control-label" for="intent_enable_active_document_context">
                                Aktifkan konteks dokumen aktif untuk follow-up question
                            </label>
                        </div>
                        <small class="form-text text-muted">
                            Jika aktif, pertanyaan seperti <code>dokumen ini</code>, <code>surat ini</code>,
                            atau <code>berdasarkan dokumen ini</code> akan diarahkan ke dokumen utama
                            dari jawaban sebelumnya.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="intent_catalog_patterns">Catalog Intent Patterns</label>
                        <textarea
                            id="intent_catalog_patterns"
                            name="intent_catalog_patterns"
                            class="form-control @error('intent_catalog_patterns') is-invalid @enderror"
                            rows="10"
                        >{{ old('intent_catalog_patterns', $intentCatalogPatterns ?? '') }}</textarea>
                        <small class="form-text text-muted">
                            Gunakan untuk pola yang memang berarti user meminta daftar dokumen yang tersedia,
                            misalnya <code>dokumen apa saja</code>, <code>daftar dokumen</code>, atau <code>ada dokumen</code>.
                        </small>
                        @error('intent_catalog_patterns')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="intent_active_document_reference_patterns">Active Document Reference Patterns</label>
                        <textarea
                            id="intent_active_document_reference_patterns"
                            name="intent_active_document_reference_patterns"
                            class="form-control @error('intent_active_document_reference_patterns') is-invalid @enderror"
                            rows="10"
                        >{{ old('intent_active_document_reference_patterns', $intentActiveDocumentReferencePatterns ?? '') }}</textarea>
                        <small class="form-text text-muted">
                            Gunakan untuk pola follow-up yang merujuk ke dokumen aktif, misalnya
                            <code>dokumen ini</code>, <code>surat ini</code>, atau <code>berdasarkan dokumen ini</code>.
                        </small>
                        @error('intent_active_document_reference_patterns')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    <h5 class="mb-3">Response Mode Settings</h5>
                    <p class="text-muted mb-3">
                        Pengaturan ini membantu backend memilih format jawaban utama yang lebih konsisten
                        sebelum prompt dikirim ke model. Mode yang tersedia:
                        <code>paragraph</code>, <code>bullet_list</code>, <code>numbered_list</code>, dan <code>table</code>.
                    </p>

                    <div class="form-group">
                        <label for="response_mode_default">Default Response Mode</label>
                        <select
                            id="response_mode_default"
                            name="response_mode_default"
                            class="form-control @error('response_mode_default') is-invalid @enderror"
                        >
                            @php($selectedResponseMode = old('response_mode_default', $responseModeDefault ?? 'paragraph'))
                            <option value="paragraph" {{ $selectedResponseMode === 'paragraph' ? 'selected' : '' }}>paragraph</option>
                            <option value="bullet_list" {{ $selectedResponseMode === 'bullet_list' ? 'selected' : '' }}>bullet_list</option>
                            <option value="numbered_list" {{ $selectedResponseMode === 'numbered_list' ? 'selected' : '' }}>numbered_list</option>
                            <option value="table" {{ $selectedResponseMode === 'table' ? 'selected' : '' }}>table</option>
                        </select>
                        @error('response_mode_default')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="response_mode_table_patterns">Table Response Patterns</label>
                        <textarea
                            id="response_mode_table_patterns"
                            name="response_mode_table_patterns"
                            class="form-control @error('response_mode_table_patterns') is-invalid @enderror"
                            rows="8"
                        >{{ old('response_mode_table_patterns', $responseModeTablePatterns ?? '') }}</textarea>
                        <small class="form-text text-muted">
                            Gunakan untuk pertanyaan yang paling nyaman dijawab dalam tabel, seperti
                            <code>perbandingan</code>, <code>jadwal</code>, atau permintaan tabel eksplisit.
                        </small>
                        @error('response_mode_table_patterns')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="response_mode_numbered_list_patterns">Numbered List Response Patterns</label>
                        <textarea
                            id="response_mode_numbered_list_patterns"
                            name="response_mode_numbered_list_patterns"
                            class="form-control @error('response_mode_numbered_list_patterns') is-invalid @enderror"
                            rows="8"
                        >{{ old('response_mode_numbered_list_patterns', $responseModeNumberedListPatterns ?? '') }}</textarea>
                        <small class="form-text text-muted">
                            Gunakan untuk pertanyaan yang meminta urutan, daftar tanggal, langkah, atau rincian yang lebih nyaman dibaca dengan nomor.
                        </small>
                        @error('response_mode_numbered_list_patterns')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="response_mode_bullet_list_patterns">Bullet List Response Patterns</label>
                        <textarea
                            id="response_mode_bullet_list_patterns"
                            name="response_mode_bullet_list_patterns"
                            class="form-control @error('response_mode_bullet_list_patterns') is-invalid @enderror"
                            rows="8"
                        >{{ old('response_mode_bullet_list_patterns', $responseModeBulletListPatterns ?? '') }}</textarea>
                        <small class="form-text text-muted">
                            Gunakan untuk pertanyaan yang meminta ringkasan poin utama, ketentuan, atau highlight dokumen.
                        </small>
                        @error('response_mode_bullet_list_patterns')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan Pengaturan AI
                    </button>
                    <a href="{{ route('admin.rag.chat') }}" class="btn btn-light">
                        Kembali ke N4R4 AI Assistance
                    </a>
                </form>
            </div>
        </div>
    </section>
@endsection

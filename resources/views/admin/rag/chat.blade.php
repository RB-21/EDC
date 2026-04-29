@extends('admin.template')

@section('css_libraries')
@endsection

@section('additional_style')
    <style>
        /* ===== Chat Container ===== */
        .chat-container {
            height: 520px;
            overflow-y: auto;
            padding: 20px;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            border-radius: 0;
            scroll-behavior: smooth;
        }

        .chat-container::-webkit-scrollbar {
            width: 6px;
        }

        .chat-container::-webkit-scrollbar-track {
            background: transparent;
        }

        .chat-container::-webkit-scrollbar-thumb {
            background: #c1c9d4;
            border-radius: 3px;
        }

        /* ===== Chat Messages ===== */
        .chat-message {
            display: flex;
            margin-bottom: 16px;
            animation: fadeInUp 0.3s ease-out;
        }

        .chat-message.user {
            flex-direction: row-reverse;
        }

        .chat-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .chat-message.ai .chat-avatar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            margin-right: 12px;
        }

        .chat-message.user .chat-avatar {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            margin-left: 12px;
        }

        .chat-bubble {
            max-width: 75%;
            padding: 12px 16px;
            border-radius: 16px;
            font-size: 13.5px;
            line-height: 1.6;
            word-wrap: break-word;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .chat-message.ai .chat-bubble {
            background: white;
            color: #333;
            border-bottom-left-radius: 4px;
        }

        .chat-message.user .chat-bubble {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom-right-radius: 4px;
        }

        /* ===== Answer Formatting ===== */
        .chat-bubble .answer-text strong,
        .chat-bubble .answer-text b {
            font-weight: 600;
            color: #2d3748;
        }

        .chat-bubble .answer-text ul,
        .chat-bubble .answer-text ol {
            margin: 6px 0 6px 16px;
            padding: 0;
        }

        .chat-bubble .answer-text li {
            margin-bottom: 3px;
        }

        .chat-bubble .answer-text p {
            margin: 0 0 8px;
        }

        .chat-bubble .answer-text p:last-child {
            margin-bottom: 0;
        }

        .chat-bubble .answer-text h1,
        .chat-bubble .answer-text h2,
        .chat-bubble .answer-text h3,
        .chat-bubble .answer-text h4 {
            font-size: 13.5px;
            font-weight: 700;
            margin: 10px 0 4px;
            color: #1a202c;
        }

        .chat-bubble .answer-text code {
            background: #f1f3f5;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 12px;
            color: #e53e3e;
        }

        /* ===== Sources Section ===== */
        .chat-bubble .sources-section {
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px solid #e9ecef;
        }

        .chat-bubble .sources-label {
            font-size: 11.5px;
            font-weight: 600;
            color: #718096;
            margin-bottom: 6px;
        }

        .chat-bubble .source-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 7px 10px;
            margin: 5px 0;
            background: linear-gradient(135deg, #f8f9ff 0%, #f1f3fa 100%);
            border-radius: 8px;
            border-left: 3px solid #667eea;
            transition: transform 0.15s, box-shadow 0.15s;
        }

        .chat-bubble .source-card:hover {
            transform: translateX(2px);
            box-shadow: 0 2px 6px rgba(102, 126, 234, 0.15);
        }

        .chat-bubble .source-info {
            flex: 1;
            min-width: 0;
        }

        .chat-bubble .source-info .source-title {
            font-size: 11.5px;
            font-weight: 600;
            color: #2d3748;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chat-bubble .source-info .source-meta {
            font-size: 10.5px;
            color: #a0aec0;
        }

        .chat-bubble .source-info .source-meta .badge {
            font-size: 9.5px;
            padding: 1px 6px;
            border-radius: 4px;
            font-weight: 600;
        }

        .chat-bubble .btn-view-source {
            flex-shrink: 0;
            margin-left: 8px;
            padding: 3px 10px;
            font-size: 10.5px;
            border-radius: 6px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s;
            white-space: nowrap;
        }

        .chat-bubble .btn-view-source:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.4);
        }

        /* ===== Input Area ===== */
        .chat-input-area {
            padding: 16px 20px;
            background: white;
            border-top: 1px solid #e9ecef;
        }

        .chat-input-area .form-control {
            border-radius: 24px;
            padding: 10px 20px;
            border: 2px solid #e9ecef;
            transition: border-color 0.2s;
            font-size: 13.5px;
        }

        .chat-input-area .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.15rem rgba(102, 126, 234, 0.15);
        }

        .chat-input-area .btn-send {
            border-radius: 50%;
            width: 42px;
            height: 42px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .chat-input-area .btn-send:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .chat-input-area .btn-send:disabled {
            opacity: 0.6;
            transform: none;
        }

        /* ===== Filter Area ===== */
        .chat-filters {
            padding: 10px 20px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }

        .chat-filters .form-control {
            font-size: 12px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        /* ===== Status Badge ===== */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-dot.online {
            background: #2ecc71;
            animation: pulse 2s infinite;
        }

        .status-dot.offline {
            background: #e74c3c;
        }

        /* ===== Typing Indicator ===== */
        .typing-indicator {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 8px 12px;
        }

        .typing-indicator span {
            width: 7px;
            height: 7px;
            background: #adb5bd;
            border-radius: 50%;
            animation: bounce 1.4s infinite ease-in-out;
        }

        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }

        /* ===== Animations ===== */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        @keyframes bounce {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1); }
        }

        /* ===== Card Styling ===== */
        .card-chat {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }

        .card-chat .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 16px 20px;
        }

        .card-chat .card-header h4 {
            color: white;
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }

        /* ===== Model Badge ===== */
        .model-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            background: rgba(255,255,255,0.2);
            padding: 3px 10px;
            border-radius: 12px;
            margin-left: 8px;
        }
    </style>
@endsection

@section('title')
    EDC | PTPN VI
@endsection

@section('page-name')
    AI Assistant
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>AI Document Assistant</h1>
        </div>
        <div class="row">
            <div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2">
                <div class="card card-chat">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="d-inline"><i class="fas fa-robot mr-2"></i> EDC AI Assistant</h4>
                            <span class="model-badge" id="modelBadge" title="AI Model">
                                <i class="fas fa-microchip"></i>
                                <span id="modelName">{{ $model ?? 'gemini-2.0-flash' }}</span>
                            </span>
                        </div>
                        <div class="status-badge">
                            @if(($health['status'] ?? '') === 'healthy')
                                <span class="status-dot online"></span>
                                <span>Online</span>
                            @else
                                <span class="status-dot offline"></span>
                                <span>Offline</span>
                            @endif
                        </div>
                    </div>

                    {{-- Chat Messages --}}
                    <div class="chat-container" id="chatContainer">
                        <div class="chat-message ai">
                            <div class="chat-avatar">
                                <i class="fas fa-robot"></i>
                            </div>
                            <div class="chat-bubble">
                                Halo! Saya <strong>AI Assistant EDC</strong>. Saya bisa menjawab pertanyaan berdasarkan
                                dokumen yang sudah di-index seperti <strong>SOP, SE, SK, IK</strong>, dan lainnya.
                                <br><br>
                                Silakan ketik pertanyaan Anda di bawah. 👇
                            </div>
                        </div>
                    </div>

                    {{-- Filter Area --}}
                    <div class="chat-filters">
                        <div class="row">
                            <div class="col-md-4">
                                <select class="form-control form-control-sm" id="filterJenis">
                                    <option value="">Semua Jenis Dokumen</option>
                                    <option value="sop">SOP — Standar Operasional Procedure</option>
                                    <option value="ik">IK — Instruksi Kerja</option>
                                    <option value="sk">SK — Surat Keputusan</option>
                                    <option value="se">SE — Surat Edaran</option>
                                    <option value="si">SI — Surat Instruksi</option>
                                    <option value="ph">PH — Pedoman Holding</option>
                                    <option value="sp">SP — Sertifikasi Perusahaan</option>
                                    <option value="ll">LL — Dokumen Eksternal</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control form-control-sm"
                                       id="filterBagian" placeholder="Filter bagian (opsional)">
                            </div>
                            <div class="col-md-3">
                                <select class="form-control form-control-sm" id="modelSelect" title="Pilih Model AI">
                                    @foreach($availableModels as $modelOption)
                                        <option value="{{ $modelOption }}" {{ $modelOption === ($model ?? 'gemini-2.0-flash') ? 'selected' : '' }}>
                                            {{ $modelOption }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 text-right">
                                <small class="text-muted" id="filterInfo">
                                    <i class="fas fa-filter"></i> Filter
                                </small>
                            </div>
                        </div>
                    </div>

                    {{-- Input Area --}}
                    <div class="chat-input-area">
                        <form id="chatForm" autocomplete="off">
                            <div class="d-flex align-items-center">
                                <input type="text" class="form-control mr-2" id="questionInput"
                                       placeholder="Ketik pertanyaan tentang dokumen..."
                                       autocomplete="off">
                                <button type="submit" class="btn btn-send" id="sendBtn" title="Kirim">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Hidden form for viewing document --}}
    <form id="ragTampilForm" method="post" action="{{ route('admin.dokumen.tampil') }}" target="ragdoc">
        @csrf
        <input type="hidden" name="id" id="ragTampilId">
    </form>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            const chatContainer = $('#chatContainer');
            const questionInput = $('#questionInput');
            const sendBtn = $('#sendBtn');
            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            // ===== Conversation History =====
            // Menyimpan riwayat percakapan agar pertanyaan lanjutan punya konteks
            const chatHistory = [];
            const MAX_HISTORY = 5; // Maksimal 5 pasang Q&A terakhir

            // Bangun pertanyaan dengan konteks history
            function buildContextualQuestion(currentQuestion) {
                if (chatHistory.length === 0) {
                    return currentQuestion;
                }

                // Ambil N history terakhir
                const recentHistory = chatHistory.slice(-MAX_HISTORY);

                let context = '[Konteks percakapan sebelumnya]\n';
                recentHistory.forEach(function(h) {
                    context += 'User: ' + h.question + '\n';
                    // Batasi panjang jawaban di konteks agar tidak terlalu besar
                    const shortAnswer = h.answer.length > 500
                        ? h.answer.substring(0, 500) + '...'
                        : h.answer;
                    context += 'AI: ' + shortAnswer + '\n\n';
                });

                context += '[Pertanyaan saat ini]\n' + currentQuestion;
                return context;
            }

            // Update model badge when select changes
            $('#modelSelect').on('change', function() {
                $('#modelName').text($(this).val());
            });

            // ===== Markdown-like formatter =====
            function formatAnswer(text) {
                if (!text) return '';

                // Escape HTML first
                let html = $('<div/>').text(text).html();

                // Bold: **text** or __text__
                html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
                html = html.replace(/__(.+?)__/g, '<strong>$1</strong>');

                // Italic: *text* or _text_ (but not inside bold)
                html = html.replace(/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/g, '<em>$1</em>');

                // Inline code: `text`
                html = html.replace(/`(.+?)`/g, '<code>$1</code>');

                // Headers: ### text → <h4>
                html = html.replace(/^#{1,4}\s+(.+)$/gm, '<h4>$1</h4>');

                // Split into lines for list processing
                const lines = html.split('\n');
                let result = [];
                let inList = false;
                let listType = null; // 'ul' or 'ol'

                for (let i = 0; i < lines.length; i++) {
                    const line = lines[i].trim();

                    // Unordered list: - item or * item
                    const ulMatch = line.match(/^[-*]\s+(.+)$/);
                    // Ordered list: 1. item, 2. item
                    const olMatch = line.match(/^\d+\.\s+(.+)$/);

                    if (ulMatch) {
                        if (!inList || listType !== 'ul') {
                            if (inList) result.push('</' + listType + '>');
                            result.push('<ul>');
                            inList = true;
                            listType = 'ul';
                        }
                        result.push('<li>' + ulMatch[1] + '</li>');
                    } else if (olMatch) {
                        if (!inList || listType !== 'ol') {
                            if (inList) result.push('</' + listType + '>');
                            result.push('<ol>');
                            inList = true;
                            listType = 'ol';
                        }
                        result.push('<li>' + olMatch[1] + '</li>');
                    } else {
                        if (inList) {
                            result.push('</' + listType + '>');
                            inList = false;
                            listType = null;
                        }
                        // Empty line → paragraph break
                        if (line === '') {
                            result.push('<br>');
                        } else if (line.startsWith('<h4>')) {
                            result.push(line);
                        } else {
                            result.push('<p>' + line + '</p>');
                        }
                    }
                }
                if (inList) {
                    result.push('</' + listType + '>');
                }

                return '<div class="answer-text">' + result.join('') + '</div>';
            }

            // ===== Scroll to bottom =====
            function scrollToBottom() {
                chatContainer.stop().animate({
                    scrollTop: chatContainer[0].scrollHeight
                }, 300);
            }

            // ===== Append message =====
            function appendMessage(role, content, id) {
                const isAi = role === 'ai';
                const avatarIcon = isAi ? 'fa-robot' : 'fa-user';
                const idAttr = id ? 'id="' + id + '"' : '';

                const html = '<div class="chat-message ' + role + '" ' + idAttr + '>' +
                    '<div class="chat-avatar"><i class="fas ' + avatarIcon + '"></i></div>' +
                    '<div class="chat-bubble">' + content + '</div>' +
                    '</div>';

                chatContainer.append(html);
                scrollToBottom();
            }

            // ===== Typing indicator =====
            function showTyping() {
                const id = 'typing-' + Date.now();
                const html = '<div class="chat-message ai" id="' + id + '">' +
                    '<div class="chat-avatar"><i class="fas fa-robot"></i></div>' +
                    '<div class="chat-bubble">' +
                    '<div class="typing-indicator"><span></span><span></span><span></span></div>' +
                    '<small class="text-muted">Sedang mencari jawaban...</small>' +
                    '</div></div>';

                chatContainer.append(html);
                scrollToBottom();
                return id;
            }

            // ===== Format sources with view button =====
            function formatSources(sources) {
                if (!sources || sources.length === 0) return '';

                // Deduplicate by doc_id (show unique documents only)
                const seen = {};
                const uniqueSources = [];
                sources.forEach(function(s) {
                    const key = s.doc_id + '-' + s.page;
                    if (!seen[key]) {
                        seen[key] = true;
                        uniqueSources.push(s);
                    }
                });

                let html = '<div class="sources-section">';
                html += '<div class="sources-label"><i class="fas fa-book-open mr-1"></i> Sumber Dokumen (' + uniqueSources.length + ')</div>';

                uniqueSources.forEach(function(s) {
                    const jenis = (s.jenis_file_kode || '').toUpperCase();
                    const nomor = s.nomor || '-';
                    const judul = s.judul || '';
                    const judulShort = judul.length > 50 ? judul.substring(0, 50) + '...' : judul;
                    const page = s.page || '-';
                    const docId = s.doc_id || '';

                    html += '<div class="source-card">' +
                        '<div class="source-info">' +
                        '  <div class="source-title" title="' + $('<div/>').text(judul).html() + '">' +
                        '    <span class="badge badge-primary mr-1">' + jenis + '</span>' + $('<div/>').text(nomor).html() +
                        '  </div>' +
                        '  <div class="source-meta">' +
                        '    <i class="fas fa-file-alt mr-1"></i>' + $('<div/>').text(judulShort).html() +
                        '    <span class="ml-2"><i class="fas fa-bookmark mr-1"></i>Hal. ' + page + '</span>' +
                        '  </div>' +
                        '</div>';

                    if (docId) {
                        html += '<button class="btn-view-source" onclick="viewSourceDoc(' + docId + ')" title="Lihat Dokumen">' +
                            '<i class="fas fa-external-link-alt mr-1"></i>Lihat' +
                            '</button>';
                    }

                    html += '</div>';
                });

                html += '</div>';
                return html;
            }

            // ===== Handle submit =====
            $('#chatForm').on('submit', function(e) {
                e.preventDefault();

                const question = questionInput.val().trim();
                if (!question) return;

                // Show user message (plain text, escaped)
                appendMessage('user', $('<div/>').text(question).html());
                questionInput.val('');

                // Show typing indicator
                const typingId = showTyping();

                // Disable input
                sendBtn.prop('disabled', true);
                questionInput.prop('disabled', true);

                // Build contextual question with history
                const contextualQuestion = buildContextualQuestion(question);

                // Build request data
                const requestData = {
                    _token: csrfToken,
                    question: contextualQuestion,
                    model: $('#modelSelect').val()
                };

                const jenisFilter = $('#filterJenis').val();
                const bagianFilter = $('#filterBagian').val().trim();

                if (jenisFilter) requestData.jenis_file = jenisFilter;
                if (bagianFilter) requestData.bagian = bagianFilter;

                // Send to API
                $.ajax({
                    url: "{{ route('admin.rag.query') }}",
                    method: 'POST',
                    data: requestData,
                    dataType: 'json',
                    success: function(data) {
                        $('#' + typingId).remove();

                        if (data.error) {
                            appendMessage('ai', '<span class="text-danger"><i class="fas fa-exclamation-triangle mr-1"></i> ' + (data.answer || data.message || 'Terjadi error') + '</span>');
                            return;
                        }

                        const rawAnswer = data.answer || 'Tidak ada jawaban ditemukan.';
                        const answerHtml = formatAnswer(rawAnswer);
                        const sourcesHtml = formatSources(data.sources);
                        appendMessage('ai', answerHtml + sourcesHtml);

                        // Simpan ke history untuk konteks percakapan berikutnya
                        chatHistory.push({
                            question: question,
                            answer: rawAnswer
                        });
                    },
                    error: function(xhr, status, error) {
                        $('#' + typingId).remove();
                        console.error('RAG Query Error:', status, error, xhr.responseText);

                        let errMsg = 'Gagal mendapatkan jawaban.';
                        if (xhr.responseJSON) {
                            errMsg = xhr.responseJSON.detail || xhr.responseJSON.message || xhr.responseJSON.error || errMsg;
                        } else if (xhr.status === 0) {
                            errMsg = 'Tidak dapat terhubung ke server.';
                        } else if (xhr.status === 419) {
                            errMsg = 'Sesi telah berakhir. Silakan refresh halaman.';
                        } else if (xhr.status === 422) {
                            errMsg = 'Pertanyaan tidak valid. Minimal 3 karakter.';
                        } else if (xhr.status >= 500) {
                            errMsg = 'Terjadi error pada server. Silakan coba lagi.';
                        }

                        appendMessage('ai', '<span class="text-danger"><i class="fas fa-exclamation-triangle mr-1"></i> ' + errMsg + '</span>');
                    },
                    complete: function() {
                        sendBtn.prop('disabled', false);
                        questionInput.prop('disabled', false);
                        questionInput.focus();
                    }
                });
            });

            // Enter to submit
            questionInput.on('keypress', function(e) {
                if (e.which === 13 && !e.shiftKey) {
                    e.preventDefault();
                    $('#chatForm').submit();
                }
            });

            // Focus input on load
            questionInput.focus();
        });

        // ===== View source document (global function for onclick) =====
        function viewSourceDoc(docId) {
            $('#ragTampilId').val(docId);
            window.open('{{ route("admin.dokumen.tampil") }}', 'ragdoc', 'width=900,height=700');
            $('#ragTampilForm').submit();
        }
    </script>
@endsection

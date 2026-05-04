@extends('admin.template')

@section('css_libraries')
@endsection

@section('additional_style')
    <style>
        /* ===== Chat Container ===== */
        .chat-container {
            height: auto;
            min-height: 380px;
            overflow-y: auto;
            padding: 20px;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            border-radius: 0;
            scroll-behavior: smooth;
            flex: 1 1 auto;
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

        .chat-toolbar {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
            padding: 10px 20px;
            background: #eef2f7;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
        }

        .chat-toolbar .form-control {
            max-width: 320px;
            font-size: 12px;
            border-radius: 8px;
        }

        .chat-toolbar .btn {
            font-size: 12px;
            border-radius: 8px;
        }

        .token-balance {
            margin-left: auto;
            font-size: 12px;
            background: #1f2937;
            color: #fff;
            border-radius: 999px;
            padding: 4px 10px;
        }

        .token-usage {
            margin-top: 8px;
            font-size: 10px;
            color: #6b7280;
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

        .status-dot.degraded {
            background: #f39c12;
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
            height: calc(100vh - 150px);
            display: flex;
            flex-direction: column;
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
            <div class="col-12">
                <div class="card card-chat">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="d-inline"><i class="fas fa-robot mr-2"></i> EDC AI Assistant</h4>
                            <span class="model-badge" id="modelBadge" title="AI Model">
                                <i class="fas fa-microchip"></i>
                                <span id="modelName">{{ $model ?? 'gemini-2.5-flash' }}</span>
                            </span>
                        </div>
                        <div class="status-badge">
                            @if(($health['status'] ?? '') === 'healthy')
                                <span class="status-dot online"></span>
                                <span>Online</span>
                            @elseif(($health['status'] ?? '') === 'degraded')
                                <span class="status-dot degraded"></span>
                                <span>Degraded</span>
                            @else
                                <span class="status-dot offline"></span>
                                <span>Offline</span>
                            @endif
                        </div>
                    </div>

                    <div class="chat-toolbar">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="newChatBtn">
                            <i class="fas fa-plus-circle mr-1"></i> Chat Baru
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="refreshSessionBtn">
                            <i class="fas fa-sync-alt mr-1"></i> Refresh
                        </button>
                        <select class="form-control form-control-sm" id="sessionSelect" title="Pilih Riwayat Chat">
                            <option value="">-- Riwayat Percakapan --</option>
                        </select>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="deleteSessionBtn" disabled>
                            <i class="fas fa-trash mr-1"></i> Hapus
                        </button>
                        <select class="form-control form-control-sm" id="modelSelect" title="Pilih Model AI">
                            @foreach($availableModels as $modelOption)
                                <option value="{{ $modelOption }}" {{ $modelOption === ($model ?? 'gemini-2.5-flash') ? 'selected' : '' }}>
                                    {{ $modelOption }}
                                </option>
                            @endforeach
                        </select>
                        <span class="token-balance" id="tokenBalanceBadge">
                            Token: {{ number_format($tokenBalance ?? 0, 0, ',', '.') }}
                        </span>
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
        const sessionsUrl = "{{ route('admin.rag.sessions') }}";
        const queryUrl = "{{ route('admin.rag.query') }}";
        const sessionBaseUrl = "{{ url('/admin/rag/sessions') }}";
        const sessionSelect = $('#sessionSelect');
        const tokenBalanceBadge = $('#tokenBalanceBadge');
        const deleteSessionBtn = $('#deleteSessionBtn');
        const chatHistory = [];
        const MAX_HISTORY = 5;
        let currentSessionId = null;
        let pendingQuestion = null;

        function buildContextualQuestion(currentQuestion) {
            if (chatHistory.length === 0) return currentQuestion;

            const recentHistory = chatHistory.slice(-MAX_HISTORY);
            let context = '[Konteks percakapan sebelumnya]\n';
            recentHistory.forEach(function(h) {
                context += 'User: ' + h.question + '\n';
                const shortAnswer = h.answer.length > 500 ? h.answer.substring(0, 500) + '...' : h.answer;
                context += 'AI: ' + shortAnswer + '\n\n';
            });
            context += '[Pertanyaan saat ini]\n' + currentQuestion;
            return context;
        }

        function resetWelcomeMessage() {
            chatContainer.html(
                '<div class="chat-message ai">' +
                '  <div class="chat-avatar"><i class="fas fa-robot"></i></div>' +
                '  <div class="chat-bubble">Halo! Saya <strong>AI Assistant EDC</strong>. Saya siap membantu pertanyaan dokumen Anda.</div>' +
                '</div>'
            );
            chatHistory.length = 0;
            pendingQuestion = null;
        }

        function updateTokenBalance(balance) {
            if (typeof balance === 'number') {
                tokenBalanceBadge.text('Token: ' + balance.toLocaleString('id-ID'));
            }
        }

        function setActiveSession(sessionId) {
            currentSessionId = sessionId || null;
            deleteSessionBtn.prop('disabled', !currentSessionId);
        }

        $('#modelSelect').on('change', function() {
            $('#modelName').text($(this).val());
        });

        function formatAnswer(text) {
            if (!text) return '';
            let html = $('<div/>').text(text).html();
            html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
            html = html.replace(/__(.+?)__/g, '<strong>$1</strong>');
            html = html.replace(/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/g, '<em>$1</em>');
            html = html.replace(/`(.+?)`/g, '<code>$1</code>');
            html = html.replace(/^#{1,4}\s+(.+)$/gm, '<h4>$1</h4>');

            const lines = html.split('\n');
            let result = [];
            let inList = false;
            let listType = null;

            for (let i = 0; i < lines.length; i++) {
                const line = lines[i].trim();
                const ulMatch = line.match(/^[-*]\s+(.+)$/);
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
                    if (line === '') {
                        result.push('<br>');
                    } else if (line.startsWith('<h4>')) {
                        result.push(line);
                    } else {
                        result.push('<p>' + line + '</p>');
                    }
                }
            }
            if (inList) result.push('</' + listType + '>');
            return '<div class="answer-text">' + result.join('') + '</div>';
        }

        function scrollToBottom() {
            chatContainer.stop().animate({ scrollTop: chatContainer[0].scrollHeight }, 300);
        }

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

        function formatSources(sources) {
            if (!sources || sources.length === 0) return '';
            const groupedSources = {};
            sources.forEach(function(s) {
                const key = s.doc_id || s.filename;
                if (!groupedSources[key]) {
                    groupedSources[key] = {
                        doc_id: s.doc_id,
                        filename: s.filename,
                        jenis_file_kode: s.jenis_file_kode,
                        nomor: s.nomor,
                        judul: s.judul,
                        pages: new Set()
                    };
                }
                if (s.page) groupedSources[key].pages.add(s.page);
            });

            const uniqueDocs = Object.values(groupedSources);
            let html = '<div class="sources-section">';
            html += '<div class="sources-label"><i class="fas fa-book-open mr-1"></i> Sumber Dokumen (' + uniqueDocs.length + ')</div>';

            uniqueDocs.forEach(function(doc) {
                const jenis = (doc.jenis_file_kode || '').toUpperCase();
                const nomor = doc.nomor || '-';
                const judul = doc.judul || doc.filename || '';
                const judulShort = judul.length > 50 ? judul.substring(0, 50) + '...' : judul;
                const pageArr = Array.from(doc.pages).sort(function(a, b) { return parseInt(a) - parseInt(b); });
                const pageStr = pageArr.length > 0 ? pageArr.join(', ') : '-';
                const docId = doc.doc_id || '';

                html += '<div class="source-card">' +
                    '<div class="source-info">' +
                    '  <div class="source-title" title="' + $('<div/>').text(judul).html() + '">' +
                    '    <span class="badge badge-primary mr-1">' + jenis + '</span>' + $('<div/>').text(nomor).html() +
                    '  </div>' +
                    '  <div class="source-meta">' +
                    '    <i class="fas fa-file-alt mr-1"></i>' + $('<div/>').text(judulShort).html() +
                    '    <span class="ml-2"><i class="fas fa-bookmark mr-1"></i>Hal. ' + pageStr + '</span>' +
                    '  </div>' +
                    '</div>';

                if (docId) {
                    html += '<button class="btn-view-source" type="button" onclick="viewSourceDoc(' + docId + ')" title="Lihat Dokumen">' +
                        '<i class="fas fa-external-link-alt mr-1"></i>Lihat' +
                        '</button>';
                }
                html += '</div>';
            });

            html += '</div>';
            return html;
        }

        function formatUsage(usage) {
            if (!usage || !usage.total_tokens) return '';
            return '<div class="token-usage">Prompt: ' + (usage.prompt_tokens || 0) +
                ' | Completion: ' + (usage.completion_tokens || 0) +
                ' | Total: ' + (usage.total_tokens || 0) + '</div>';
        }

        function rebuildHistoryFromMessages(messages) {
            chatHistory.length = 0;
            pendingQuestion = null;
            messages.forEach(function(item) {
                if (item.role === 'user') {
                    pendingQuestion = item.content;
                } else if (item.role === 'ai' && pendingQuestion) {
                    chatHistory.push({ question: pendingQuestion, answer: item.content || '' });
                    pendingQuestion = null;
                }
            });
        }

        function loadSessions(selectSessionId) {
            $.getJSON(sessionsUrl, function(resp) {
                const sessions = resp.sessions || [];
                const keepId = selectSessionId || currentSessionId;
                sessionSelect.empty().append('<option value="">-- Riwayat Percakapan --</option>');
                sessions.forEach(function(s) {
                    sessionSelect.append(
                        $('<option/>')
                            .val(s.id)
                            .text((s.title || 'Percakapan') + (s.model ? ' [' + s.model + ']' : ''))
                    );
                });

                updateTokenBalance(resp.token_balance);
                if (keepId) {
                    sessionSelect.val(String(keepId));
                    setActiveSession(parseInt(keepId, 10));
                } else {
                    setActiveSession(null);
                }
            });
        }

        function loadSessionMessages(sessionId) {
            if (!sessionId) {
                setActiveSession(null);
                resetWelcomeMessage();
                return;
            }

            $.getJSON(sessionBaseUrl + '/' + sessionId + '/messages', function(resp) {
                resetWelcomeMessage();
                chatContainer.empty();
                const messages = resp.messages || [];
                messages.forEach(function(m) {
                    if (m.role === 'user') {
                        appendMessage('user', $('<div/>').text(m.content).html());
                    } else {
                        appendMessage('ai', formatAnswer(m.content || '') + formatSources(m.sources || []) + formatUsage(m.usage || {}));
                    }
                });

                rebuildHistoryFromMessages(messages);
                setActiveSession(parseInt(sessionId, 10));
                updateTokenBalance(resp.token_balance);
            }).fail(function() {
                setActiveSession(null);
                resetWelcomeMessage();
            });
        }

        $('#chatForm').on('submit', function(e) {
            e.preventDefault();
            const question = questionInput.val().trim();
            if (!question) return;

            appendMessage('user', $('<div/>').text(question).html());
            questionInput.val('');
            const typingId = showTyping();
            sendBtn.prop('disabled', true);
            questionInput.prop('disabled', true);

            const requestData = {
                _token: csrfToken,
                question: buildContextualQuestion(question),
                model: $('#modelSelect').val()
            };

            if (currentSessionId) requestData.session_id = currentSessionId;

            $.ajax({
                url: queryUrl,
                method: 'POST',
                data: requestData,
                dataType: 'json',
                success: function(data) {
                    $('#' + typingId).remove();

                    if (data.error) {
                        appendMessage('ai', '<span class="text-danger"><i class="fas fa-exclamation-triangle mr-1"></i> ' + (data.answer || data.message || 'Terjadi error') + '</span>');
                        return;
                    }

                    if (data.session_id) {
                        setActiveSession(data.session_id);
                        loadSessions(data.session_id);
                    }
                    updateTokenBalance(data.token_balance);

                    const rawAnswer = (typeof data.answer === 'string' && data.answer.trim() !== '')
                        ? data.answer
                        : 'Tidak ada jawaban ditemukan.';
                    appendMessage('ai', formatAnswer(rawAnswer) + formatSources(data.sources || []) + formatUsage(data.usage || {}));
                    chatHistory.push({ question: question, answer: rawAnswer });
                },
                error: function(xhr) {
                    $('#' + typingId).remove();
                    let errMsg = 'Gagal mendapatkan jawaban.';

                    if (xhr.responseJSON) {
                        const payload = xhr.responseJSON;
                        if (typeof payload.answer === 'string' && payload.answer.trim() !== '') {
                            errMsg = payload.answer;
                        } else if (typeof payload.detail === 'string' && payload.detail.trim() !== '') {
                            errMsg = payload.detail;
                        } else if (typeof payload.message === 'string' && payload.message.trim() !== '') {
                            errMsg = payload.message;
                        }
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

        questionInput.on('keypress', function(e) {
            if (e.which === 13 && !e.shiftKey) {
                e.preventDefault();
                $('#chatForm').submit();
            }
        });

        $('#newChatBtn').on('click', function() {
            sessionSelect.val('');
            setActiveSession(null);
            resetWelcomeMessage();
            questionInput.focus();
        });

        $('#refreshSessionBtn').on('click', function() {
            loadSessions();
            if (currentSessionId) loadSessionMessages(currentSessionId);
        });

        sessionSelect.on('change', function() {
            const selected = $(this).val();
            if (selected) {
                loadSessionMessages(selected);
            } else {
                setActiveSession(null);
                resetWelcomeMessage();
            }
        });

        deleteSessionBtn.on('click', function() {
            if (!currentSessionId) return;
            if (!confirm('Hapus riwayat percakapan ini?')) return;

            $.ajax({
                url: sessionBaseUrl + '/' + currentSessionId,
                method: 'POST',
                data: {
                    _token: csrfToken,
                    _method: 'DELETE'
                },
                success: function() {
                    sessionSelect.val('');
                    setActiveSession(null);
                    resetWelcomeMessage();
                    loadSessions();
                }
            });
        });

        resetWelcomeMessage();
        loadSessions();
        questionInput.focus();
    });

    function viewSourceDoc(docId) {
        $('#ragTampilId').val(docId);
        window.open('{{ route("admin.dokumen.tampil") }}', 'ragdoc', 'width=900,height=700');
        $('#ragTampilForm').submit();
    }
</script>
@endsection

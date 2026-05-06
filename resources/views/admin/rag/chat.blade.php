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
            margin-bottom: 12px;
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
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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
            padding: 10px 14px;
            border-radius: 16px;
            font-size: 13.5px;
            line-height: 1.42;
            word-wrap: break-word;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
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
            margin: 4px 0 4px 16px;
            padding: 0;
        }

        .chat-bubble .answer-text li {
            margin-bottom: 2px;
        }

        .chat-bubble .answer-text p {
            margin: 0 0 4px;
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
            margin: 6px 0 3px;
            color: #1a202c;
        }

        .chat-bubble .answer-text code {
            background: #f1f3f5;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 12px;
            color: #e53e3e;
        }

        .chat-bubble .answer-text .table-wrap {
            margin: 8px 0;
            overflow-x: auto;
        }

        .chat-bubble .answer-text table {
            width: 100%;
            min-width: 420px;
            border-collapse: collapse;
            font-size: 12.5px;
        }

        .chat-bubble .answer-text th,
        .chat-bubble .answer-text td {
            border: 1px solid #d9e2ec;
            padding: 6px 8px;
            vertical-align: top;
            text-align: left;
        }

        .chat-bubble .answer-text th {
            background: #f8fafc;
            font-weight: 700;
            color: #1a202c;
        }

        .chat-bubble .answer-text tr:nth-child(even) td {
            background: #fcfdff;
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

        .chat-bubble .source-info .source-title .badge {
            font-size: 8.5px;
            padding: 1px 5px;
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
            padding: 4px 12px;
            font-weight: 600;
        }

        .token-usage {
            margin-top: 6px;
            font-size: 10px;
            color: #6b7280;
            line-height: 1.35;
        }

        .followup-section {
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px dashed #d7dce5;
        }

        .followup-label {
            font-size: 11px;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 6px;
        }

        .followup-btn {
            border: 1px solid #c7d2fe;
            color: #4338ca;
            background: #eef2ff;
            border-radius: 999px;
            font-size: 11px;
            padding: 4px 10px;
            margin: 0 6px 6px 0;
            cursor: pointer;
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

        .loading-spinner {
            width: 16px;
            height: 16px;
            border: 2px solid #d1d5db;
            border-top-color: #667eea;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
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

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        @keyframes bounce {

            0%,
            80%,
            100% {
                transform: scale(0);
            }

            40% {
                transform: scale(1);
            }
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* ===== Card Styling ===== */
        .card-chat {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
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
            background: rgba(255, 255, 255, 0.2);
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
    N4R4 AI Assistance
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
                            <h4 class="d-inline"><i class="fas fa-robot mr-2"></i> N4R4 AI Assistance</h4>
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
                            Token Balance: {{ number_format($tokenBalance ?? 0, 0, ',', '.') }}
                        </span>
                    </div>
                    {{-- Chat Messages --}}
                    <div class="chat-container" id="chatContainer">
                        <div class="chat-message ai">
                            <div class="chat-avatar">
                                <i class="fas fa-robot"></i>
                            </div>
                            <div class="chat-bubble">
                                Halo! Saya <strong>N4R4 AI Assistance</strong>. Saya bisa menjawab pertanyaan berdasarkan
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
                                    placeholder="Ketik pertanyaan tentang dokumen..." autocomplete="off">
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
        $(document).ready(function () {
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
            const SOURCE_MIN_SCORE_RATIO = 0.70;
            const SOURCE_MIN_SCORE_ABSOLUTE = 0.0;
            const SOURCE_RELATED_DOC_RATIO = 0.87;
            const SOURCE_MAX_DOCS_DISPLAY = 3;
            let currentSessionId = null;
            let pendingQuestion = null;

            function buildContextualQuestion(currentQuestion) {
                if (chatHistory.length === 0) return currentQuestion;

                const recentHistory = chatHistory.slice(-MAX_HISTORY);
                let context = '[Konteks percakapan sebelumnya]\n';
                recentHistory.forEach(function (h) {
                    context += 'User: ' + h.question + '\n';
                    const shortAnswer = h.answer.length > 500 ? h.answer.substring(0, 500) + '...' : h.answer;
                    context += 'AI: ' + shortAnswer + '\n\n';
                });
                context += '[Pertanyaan saat ini]\n' + currentQuestion;
                return context;
            }

            function extractDisplayedUserQuestion(text) {
                const raw = String(text || '');
                const marker = '[Pertanyaan saat ini]';
                const markerIndex = raw.indexOf(marker);
                if (markerIndex === -1) {
                    return raw;
                }
                const extracted = raw.substring(markerIndex + marker.length).trim();
                return extracted || raw;
            }

            function resetWelcomeMessage() {
                chatContainer.html(
                    '<div class="chat-message ai">' +
                    '  <div class="chat-avatar"><i class="fas fa-robot"></i></div>' +
                    '  <div class="chat-bubble">Halo! Saya <strong>N4R4 AI Assistance</strong>. Saya siap membantu pertanyaan dokumen Anda.</div>' +
                    '</div>'
                );
                chatHistory.length = 0;
                pendingQuestion = null;
            }

            function updateTokenBalance(balance) {
                if (typeof balance === 'number') {
                    tokenBalanceBadge.text('Token Balance: ' + balance.toLocaleString('id-ID'));
                }
            }

            function setActiveSession(sessionId) {
                currentSessionId = sessionId || null;
                deleteSessionBtn.prop('disabled', !currentSessionId);
            }

            $('#modelSelect').on('change', function () {
                $('#modelName').text($(this).val());
            });

            function formatAnswer(text) {
                if (!text) return '';
                const normalizedText = String(text).replace(
                    /^(\d+)\.\s+(\d{1,2}\s+(Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember)\b.*)$/gim,
                    '- $2'
                );
                let html = $('<div/>').text(normalizedText).html();
                html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
                html = html.replace(/__(.+?)__/g, '<strong>$1</strong>');
                html = html.replace(/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/g, '<em>$1</em>');
                html = html.replace(/`(.+?)`/g, '<code>$1</code>');
                html = html.replace(/^#{1,4}\s+(.+)$/gm, '<h4>$1</h4>');

                const lines = html.split('\n');
                let result = [];
                let inList = false;
                let listType = null;
                
                function closeListIfOpen() {
                    if (inList) {
                        result.push('</' + listType + '>');
                        inList = false;
                        listType = null;
                    }
                }

                function isMarkdownTableRow(line) {
                    if (!line || line.indexOf('|') === -1) return false;
                    const trimmed = line.trim();
                    return trimmed.startsWith('|') && trimmed.endsWith('|');
                }

                function isMarkdownTableSeparator(line) {
                    if (!isMarkdownTableRow(line)) return false;
                    const content = line.trim().slice(1, -1).trim();
                    return /^[:\-\s|]+$/.test(content) && content.indexOf('-') !== -1;
                }

                function splitMarkdownTableRow(line) {
                    return line.trim()
                        .replace(/^\|/, '')
                        .replace(/\|$/, '')
                        .split('|')
                        .map(function (cell) {
                            return cell.trim();
                        });
                }

                for (let i = 0; i < lines.length; i++) {
                    const line = lines[i].trim();
                    const ulMatch = line.match(/^[-*]\s+(.+)$/);
                    const olMatch = line.match(/^\d+\.\s+(.+)$/);

                    if (
                        isMarkdownTableRow(line) &&
                        i + 1 < lines.length &&
                        isMarkdownTableSeparator(lines[i + 1].trim())
                    ) {
                        closeListIfOpen();
                        const headerCells = splitMarkdownTableRow(line);
                        const tableRows = [];
                        i += 2;

                        while (i < lines.length) {
                            const rowLine = lines[i].trim();
                            if (!isMarkdownTableRow(rowLine) || isMarkdownTableSeparator(rowLine)) {
                                i -= 1;
                                break;
                            }
                            tableRows.push(splitMarkdownTableRow(rowLine));
                            i += 1;
                        }

                        let tableHtml = '<div class="table-wrap"><table><thead><tr>';
                        headerCells.forEach(function (cell) {
                            tableHtml += '<th>' + cell + '</th>';
                        });
                        tableHtml += '</tr></thead><tbody>';

                        tableRows.forEach(function (rowCells) {
                            tableHtml += '<tr>';
                            headerCells.forEach(function (_, cellIndex) {
                                tableHtml += '<td>' + (rowCells[cellIndex] || '') + '</td>';
                            });
                            tableHtml += '</tr>';
                        });

                        tableHtml += '</tbody></table></div>';
                        result.push(tableHtml);
                        continue;
                    }

                    if (ulMatch) {
                        if (!inList || listType !== 'ul') {
                            closeListIfOpen();
                            result.push('<ul>');
                            inList = true;
                            listType = 'ul';
                        }
                        result.push('<li>' + ulMatch[1] + '</li>');
                    } else if (olMatch) {
                        if (!inList || listType !== 'ol') {
                            closeListIfOpen();
                            result.push('<ol>');
                            inList = true;
                            listType = 'ol';
                        }
                        result.push('<li>' + olMatch[1] + '</li>');
                    } else {
                        closeListIfOpen();
                        if (line === '') {
                            result.push('<br>');
                        } else if (line.startsWith('<h4>')) {
                            result.push(line);
                        } else {
                            result.push('<p>' + line + '</p>');
                        }
                    }
                }
                closeListIfOpen();
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

            function showHistoryLoading() {
                chatContainer.html(
                    '<div class="chat-message ai" id="history-loading">' +
                    '  <div class="chat-avatar"><i class="fas fa-robot"></i></div>' +
                    '  <div class="chat-bubble d-flex align-items-center">' +
                    '    <span class="loading-spinner mr-2"></span>' +
                    '    <span class="text-muted">Memuat riwayat percakapan...</span>' +
                    '  </div>' +
                    '</div>'
                );
                scrollToBottom();
            }

            function filterRelevantSources(sources) {
                if (!Array.isArray(sources) || sources.length === 0) return [];

                const scored = sources
                    .map(function (source) {
                        const scoreNumber = Number(source.score);
                        return {
                            source: source,
                            score: Number.isFinite(scoreNumber) ? scoreNumber : null
                        };
                    });

                const withScore = scored.filter(function (item) {
                    return item.score !== null;
                });

                if (withScore.length === 0) {
                    return sources;
                }

                const maxScore = Math.max.apply(null, withScore.map(function (item) { return item.score; }));
                const cutoff = Math.max(SOURCE_MIN_SCORE_ABSOLUTE, maxScore * SOURCE_MIN_SCORE_RATIO);
                const filtered = withScore
                    .filter(function (item) { return item.score >= cutoff; })
                    .map(function (item) { return item.source; });

                const base = filtered.length > 0 ? filtered : withScore.sort(function (a, b) { return b.score - a.score; }).map(function (item) { return item.source; });
                if (base.length === 0) return [];

                const docBest = {};
                base.forEach(function (source) {
                    const key = source.doc_id || source.filename || 'unknown';
                    const scoreNumber = Number(source.score);
                    const score = Number.isFinite(scoreNumber) ? scoreNumber : 0;
                    if (typeof docBest[key] === 'undefined' || score > docBest[key]) {
                        docBest[key] = score;
                    }
                });

                const rankedDocs = Object.entries(docBest).sort(function (a, b) { return b[1] - a[1]; });
                const topDocScore = rankedDocs.length > 0 ? rankedDocs[0][1] : 0;
                const docCutoff = topDocScore * SOURCE_RELATED_DOC_RATIO;
                const selectedDocKeys = rankedDocs
                    .filter(function (entry, idx) {
                        if (idx === 0) return true;
                        return entry[1] >= docCutoff;
                    })
                    .slice(0, SOURCE_MAX_DOCS_DISPLAY)
                    .map(function (entry) { return entry[0]; });

                return base.filter(function (source) {
                    const key = source.doc_id || source.filename || 'unknown';
                    return selectedDocKeys.indexOf(String(key)) !== -1;
                });
            }

        function formatSources(sources) {
            const relevantSources = filterRelevantSources(sources);
            if (!relevantSources || relevantSources.length === 0) return '';
                const groupedSources = {};
                relevantSources.forEach(function (s) {
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

                uniqueDocs.forEach(function (doc) {
                    const jenis = (doc.jenis_file_kode || '').toUpperCase();
                    const nomor = doc.nomor || '-';
                    const judul = doc.judul || doc.filename || '';
                    const judulShort = judul.length > 50 ? judul.substring(0, 50) + '...' : judul;
                    const pageArr = Array.from(doc.pages).sort(function (a, b) { return parseInt(a) - parseInt(b); });
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

        function shouldHideSources(answerText) {
            const text = String(answerText || '').toLowerCase();
            if (!text) return false;
            return (
                text.indexOf('tidak ditemukan dalam dokumen yang tersedia') !== -1 ||
                text.indexOf('tidak ada dokumen yang relevan ditemukan') !== -1 ||
                text.indexOf('informasi tersebut tidak ditemukan') !== -1
            );
        }

            function formatUsage(usage) {
                if (!usage || !usage.total_tokens) return '';
                return '<div class="token-usage">Prompt: ' + (usage.prompt_tokens || 0) +
                    ' | Completion: ' + (usage.completion_tokens || 0) +
                    ' | Total: ' + (usage.total_tokens || 0) + '</div>';
            }

            function formatFollowUpQuestions(questions) {
                if (!Array.isArray(questions) || questions.length === 0) return '';
                const uniqueQuestions = [];
                questions.forEach(function (q) {
                    const qq = String(q || '').trim();
                    if (qq && uniqueQuestions.indexOf(qq) === -1) uniqueQuestions.push(qq);
                });
                if (uniqueQuestions.length === 0) return '';

                let html = '<div class="followup-section">';
                html += '<div class="followup-label"><i class="fas fa-lightbulb mr-1"></i>Saran pertanyaan lanjutan</div>';
                uniqueQuestions.slice(0, 3).forEach(function (q) {
                    html += '<button type="button" class="followup-btn" data-question="' + $('<div/>').text(q).html() + '">' +
                        $('<div/>').text(q).html() +
                        '</button>';
                });
                html += '</div>';
                return html;
            }

            function rebuildHistoryFromMessages(messages) {
                chatHistory.length = 0;
                pendingQuestion = null;
                messages.forEach(function (item) {
                    if (item.role === 'user') {
                        pendingQuestion = extractDisplayedUserQuestion(item.content);
                    } else if (item.role === 'ai' && pendingQuestion) {
                        chatHistory.push({ question: pendingQuestion, answer: item.content || '' });
                        pendingQuestion = null;
                    }
                });
            }

            function loadSessions(selectSessionId) {
                sessionSelect.prop('disabled', true);
                sessionSelect.empty().append('<option value="">Memuat riwayat...</option>');
                $.getJSON(sessionsUrl, function (resp) {
                    const sessions = resp.sessions || [];
                    const keepId = selectSessionId || currentSessionId;
                    sessionSelect.empty().append('<option value="">-- Riwayat Percakapan --</option>');
                    sessions.forEach(function (s) {
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
                }).always(function () {
                    sessionSelect.prop('disabled', false);
                });
            }

            function loadSessionMessages(sessionId) {
                if (!sessionId) {
                    setActiveSession(null);
                    resetWelcomeMessage();
                    return;
                }

                showHistoryLoading();
                sessionSelect.prop('disabled', true);
                sendBtn.prop('disabled', true);
                questionInput.prop('disabled', true);
                deleteSessionBtn.prop('disabled', true);

                $.getJSON(sessionBaseUrl + '/' + sessionId + '/messages', function (resp) {
                    resetWelcomeMessage();
                    chatContainer.empty();
                    const messages = resp.messages || [];
                    messages.forEach(function (m) {
                        if (m.role === 'user') {
                            appendMessage('user', $('<div/>').text(extractDisplayedUserQuestion(m.content)).html());
                        } else {
                            const answerText = m.content || '';
                            appendMessage('ai',
                                formatAnswer(answerText) +
                                formatFollowUpQuestions(m.follow_up_questions || []) +
                                (shouldHideSources(answerText) ? '' : formatSources(m.sources || [])) +
                                formatUsage(m.usage || {})
                            );
                        }
                    });

                    rebuildHistoryFromMessages(messages);
                    setActiveSession(parseInt(sessionId, 10));
                    updateTokenBalance(resp.token_balance);
                }).fail(function () {
                    setActiveSession(null);
                    resetWelcomeMessage();
                }).always(function () {
                    sessionSelect.prop('disabled', false);
                    sendBtn.prop('disabled', false);
                    questionInput.prop('disabled', false);
                    deleteSessionBtn.prop('disabled', !currentSessionId);
                });
            }

            $('#chatForm').on('submit', function (e) {
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
                    question: question,
                    question_context: buildContextualQuestion(question),
                    model: $('#modelSelect').val()
                };

                if (currentSessionId) requestData.session_id = currentSessionId;

                $.ajax({
                    url: queryUrl,
                    method: 'POST',
                    data: requestData,
                    dataType: 'json',
                    success: function (data) {
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
                        appendMessage('ai',
                            formatAnswer(rawAnswer) +
                            formatFollowUpQuestions(data.follow_up_questions || []) +
                            (shouldHideSources(rawAnswer) ? '' : formatSources(data.sources || [])) +
                            formatUsage(data.usage || {})
                        );
                        chatHistory.push({ question: question, answer: rawAnswer });
                    },
                    error: function (xhr) {
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
                    complete: function () {
                        sendBtn.prop('disabled', false);
                        questionInput.prop('disabled', false);
                        questionInput.focus();
                    }
                });
            });

            questionInput.on('keypress', function (e) {
                if (e.which === 13 && !e.shiftKey) {
                    e.preventDefault();
                    $('#chatForm').submit();
                }
            });

            $('#newChatBtn').on('click', function () {
                sessionSelect.val('');
                setActiveSession(null);
                resetWelcomeMessage();
                questionInput.focus();
            });

            $('#refreshSessionBtn').on('click', function () {
                loadSessions();
                if (currentSessionId) loadSessionMessages(currentSessionId);
            });

            sessionSelect.on('change', function () {
                const selected = $(this).val();
                if (selected) {
                    loadSessionMessages(selected);
                } else {
                    setActiveSession(null);
                    resetWelcomeMessage();
                }
            });

            deleteSessionBtn.on('click', function () {
                if (!currentSessionId) return;
                if (!confirm('Hapus riwayat percakapan ini?')) return;

                $.ajax({
                    url: sessionBaseUrl + '/' + currentSessionId,
                    method: 'POST',
                    data: {
                        _token: csrfToken,
                        _method: 'DELETE'
                    },
                    success: function () {
                        sessionSelect.val('');
                        setActiveSession(null);
                        resetWelcomeMessage();
                        loadSessions();
                    }
                });
            });

            chatContainer.on('click', '.followup-btn', function () {
                const q = $(this).data('question');
                if (!q) return;
                questionInput.val(String(q));
                questionInput.focus();
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

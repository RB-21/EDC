<style>
  .n4ra-widget {
    position: fixed;
    right: 24px;
    bottom: 24px;
    z-index: 1060;
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
  }

  .n4ra-widget-btn {
    width: 56px;
    height: 56px;
    border: 0;
    border-radius: 999px;
    color: #fff;
    cursor: pointer;
    box-shadow: 0 10px 26px rgba(53, 88, 197, 0.35);
    background: linear-gradient(135deg, #5b6ee1 0%, #6d42d7 100%);
  }

  .n4ra-widget-panel {
    display: none;
    width: 480px;
    max-width: calc(100vw - 32px);
    height: min(520px, calc(100vh - 110px));
    border-radius: 14px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 20px 48px rgba(16, 24, 40, 0.24);
    border: 1px solid rgba(91, 110, 225, 0.22);
    flex-direction: column;
    box-sizing: border-box;
  }

  .n4ra-widget-header {
    padding: 12px 14px;
    background: linear-gradient(135deg, #5b6ee1 0%, #6d42d7 100%);
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
  }

  .n4ra-widget-title {
    font-size: 14px;
    font-weight: 600;
    margin: 0;
    line-height: 1.2;
  }

  .n4ra-widget-subtitle {
    font-size: 11px;
    opacity: 0.9;
    margin: 2px 0 0;
  }

  .n4ra-widget-close {
    border: 0;
    background: transparent;
    color: #fff;
    font-size: 16px;
    cursor: pointer;
  }

  .n4ra-widget-toolbar {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 10px;
    background: #eef2f7;
    border-bottom: 1px solid #e2e8f0;
    flex-shrink: 0;
  }

  .n4ra-widget-session-select {
    flex: 1;
    min-width: 0;
    height: 34px;
    border: 1px solid #d0d7e2;
    border-radius: 8px;
    padding: 0 9px;
    font-size: 12px;
    color: #334155;
    background: #fff;
  }

  .n4ra-widget-new-chat {
    border: 1px solid #c7d2fe;
    color: #4338ca;
    background: #eef2ff;
    border-radius: 8px;
    height: 34px;
    padding: 0 10px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
  }

  .n4ra-widget-messages {
    flex: 1 1 auto;
    min-height: 0;
    overflow-y: auto;
    padding: 12px;
    background: #f7f8fc;
  }

  .n4ra-msg {
    margin-bottom: 10px;
    max-width: 94%;
    border-radius: 12px;
    padding: 9px 11px;
    font-size: 13px;
    line-height: 1.4;
    white-space: pre-wrap;
    word-break: break-word;
  }

  .n4ra-msg-user {
    margin-left: auto;
    background: #5b6ee1;
    color: #fff;
  }

  .n4ra-msg-ai {
    margin-right: auto;
    background: #fff;
    color: #1f2937;
    border: 1px solid #e5e7eb;
  }

  .n4ra-msg .answer-text strong,
  .n4ra-msg .answer-text b {
    font-weight: 600;
    color: #2d3748;
  }

  .n4ra-msg .answer-text ul,
  .n4ra-msg .answer-text ol {
    margin: 4px 0 4px 16px;
    padding: 0;
  }

  .n4ra-msg .answer-text li {
    margin-bottom: 2px;
  }

  .n4ra-msg .answer-text p {
    margin: 0 0 4px;
  }

  .n4ra-msg .answer-text p:last-child {
    margin-bottom: 0;
  }

  .n4ra-msg .answer-text h1,
  .n4ra-msg .answer-text h2,
  .n4ra-msg .answer-text h3,
  .n4ra-msg .answer-text h4 {
    font-size: 13.5px;
    font-weight: 700;
    margin: 6px 0 3px;
    color: #1a202c;
  }

  .n4ra-msg .answer-text code {
    background: #f1f3f5;
    padding: 1px 5px;
    border-radius: 3px;
    font-size: 12px;
    color: #e53e3e;
  }

  .n4ra-msg .answer-text .table-wrap {
    margin: 8px 0;
    overflow-x: auto;
  }

  .n4ra-msg .answer-text table {
    width: 100%;
    min-width: 420px;
    border-collapse: collapse;
    font-size: 12.5px;
  }

  .n4ra-msg .answer-text th,
  .n4ra-msg .answer-text td {
    border: 1px solid #d9e2ec;
    padding: 6px 8px;
    vertical-align: top;
    text-align: left;
  }

  .n4ra-msg .answer-text th {
    background: #f8fafc;
    font-weight: 700;
    color: #1a202c;
  }

  .n4ra-msg .answer-text tr:nth-child(even) td {
    background: #fcfdff;
  }

  .n4ra-msg .sources-section {
    margin-top: 12px;
    padding-top: 10px;
    border-top: 1px solid #e9ecef;
  }

  .n4ra-msg .sources-label {
    font-size: 11.5px;
    font-weight: 600;
    color: #718096;
    margin-bottom: 6px;
  }

  .n4ra-msg .source-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 7px 10px;
    margin: 5px 0;
    background: linear-gradient(135deg, #f8f9ff 0%, #f1f3fa 100%);
    border-radius: 8px;
    border-left: 3px solid #667eea;
  }

  .n4ra-msg .source-info {
    flex: 1;
    min-width: 0;
  }

  .n4ra-msg .source-title {
    font-size: 11.5px;
    font-weight: 600;
    color: #2d3748;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .n4ra-msg .source-meta {
    font-size: 10.5px;
    color: #a0aec0;
  }

  .n4ra-msg .source-title .badge {
    font-size: 8.5px;
    padding: 1px 5px;
    border-radius: 4px;
    font-weight: 600;
  }

  .n4ra-msg .btn-view-source {
    flex-shrink: 0;
    margin-left: 8px;
    padding: 3px 10px;
    font-size: 10.5px;
    border-radius: 6px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    cursor: pointer;
    white-space: nowrap;
  }

  .n4ra-msg .followup-section {
    margin-top: 10px;
    padding-top: 8px;
    border-top: 1px dashed #d7dce5;
  }

  .n4ra-msg .followup-label {
    font-size: 11px;
    font-weight: 600;
    color: #6b7280;
    margin-bottom: 6px;
  }

  .n4ra-msg .followup-btn {
    border: 1px solid #c7d2fe;
    color: #4338ca;
    background: #eef2ff;
    border-radius: 999px;
    font-size: 11px;
    padding: 4px 10px;
    margin: 0 6px 6px 0;
    cursor: pointer;
  }

  .n4ra-msg .token-usage {
    margin-top: 6px;
    font-size: 10px;
    color: #6b7280;
    line-height: 1.35;
  }

  .n4ra-widget-footer {
    flex-shrink: 0;
    border-top: 1px solid #eceff5;
    background: #fff;
    padding: 10px;
  }

  .n4ra-widget-balance {
    font-size: 11px;
    color: #64748b;
    margin-bottom: 8px;
  }

  .n4ra-widget-input {
    display: flex;
    gap: 8px;
    align-items: center;
  }

  .n4ra-widget-input textarea {
    resize: none;
    border: 1px solid #dbe0ea;
    border-radius: 10px;
    padding: 8px 10px;
    width: 100%;
    min-height: 38px;
    max-height: 90px;
    font-size: 13px;
    line-height: 1.3;
    box-sizing: border-box;
    overflow-y: auto;
  }

  .n4ra-widget-send {
    border: 0;
    border-radius: 10px;
    background: #5b6ee1;
    color: #fff;
    padding: 9px 12px;
    font-size: 13px;
    cursor: pointer;
    white-space: nowrap;
  }

  .n4ra-widget-send:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }

  @media (max-width: 576px) {
    .n4ra-widget {
      right: 12px;
      bottom: 12px;
    }

    .n4ra-widget-panel {
      width: calc(100vw - 24px);
      height: calc(100vh - 24px);
    }
  }
</style>

<div class="n4ra-widget" id="n4raWidget">
  <button class="n4ra-widget-btn" id="n4raWidgetBtn" title="N4R4 AI Assistance">
    <i class="fas fa-robot"></i>
  </button>

  <div class="n4ra-widget-panel" id="n4raWidgetPanel">
    <div class="n4ra-widget-header">
      <div>
        <p class="n4ra-widget-title">N4R4 AI Assistance</p>
        <p class="n4ra-widget-subtitle">Customer Service Dokumen Internal</p>
      </div>
      <button class="n4ra-widget-close" id="n4raWidgetClose" aria-label="Tutup">&times;</button>
    </div>
    <div class="n4ra-widget-toolbar">
      <select id="n4raWidgetSessionSelect" class="n4ra-widget-session-select">
        <option value="">-- Pilih riwayat chat --</option>
      </select>
      <button type="button" id="n4raWidgetNewChat" class="n4ra-widget-new-chat">Chat Baru</button>
    </div>
    <div class="n4ra-widget-messages" id="n4raWidgetMessages"></div>
    <div class="n4ra-widget-footer">
      <div class="n4ra-widget-balance">Saldo token: <strong id="n4raWidgetTokenBalance">-</strong></div>
      <div class="n4ra-widget-input">
        <textarea id="n4raWidgetInput" placeholder="Tanyakan dokumen, SOP, SE, SK, atau info lain..."></textarea>
        <button class="n4ra-widget-send" id="n4raWidgetSend">Kirim</button>
      </div>
    </div>
  </div>
</div>

@php
  $roleCode = auth()->user()->role ?? '';
  $n4raViewDocRoute = route('admin.dokumen.tampil');
  if ($roleCode === 'op') $n4raViewDocRoute = route('operator.dokumen.tampil');
  if ($roleCode === 'usr') $n4raViewDocRoute = route('user.dokumen.tampil');
  if ($roleCode === 'tmu') $n4raViewDocRoute = route('tamu.dokumen.tampil');
@endphp
<form id="n4raViewSourceForm" action="{{ $n4raViewDocRoute }}" method="POST" target="n4ra-doc-viewer" style="display:none;">
  @csrf
  <input type="hidden" name="id" id="n4raViewSourceId" value="">
</form>

<script>
  (function() {
    const widgetBtn = document.getElementById('n4raWidgetBtn');
    const widgetPanel = document.getElementById('n4raWidgetPanel');
    const widgetClose = document.getElementById('n4raWidgetClose');
    const sessionSelect = document.getElementById('n4raWidgetSessionSelect');
    const newChatBtn = document.getElementById('n4raWidgetNewChat');
    const messageBox = document.getElementById('n4raWidgetMessages');
    const inputEl = document.getElementById('n4raWidgetInput');
    const sendBtn = document.getElementById('n4raWidgetSend');
    const tokenEl = document.getElementById('n4raWidgetTokenBalance');

    const csrfToken = '{{ csrf_token() }}';
    const userId = '{{ (int) auth()->id() }}';
    const storageKey = `n4ra_widget_session_${userId}`;

    const endpointCreateSession = "{{ route('ai_assistance.sessions.create') }}";
    const endpointSessions = "{{ route('ai_assistance.sessions') }}";
    const endpointQuery = "{{ route('ai_assistance.query') }}";
    const endpointSessionMessagesBase = "{{ url('/ai-assistance/sessions') }}";

    let currentSessionId = null;
    let isLoading = false;
    let hasLoadedHistory = false;

    function escapeHtml(text) {
      return String(text || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
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

    function setTokenBalance(value) {
      const tokenValue = Number.isFinite(Number(value)) ? Number(value) : 0;
      tokenEl.textContent = tokenValue.toLocaleString('id-ID');
    }

    function setToolbarDisabled(disabled) {
      sessionSelect.disabled = !!disabled;
      newChatBtn.disabled = !!disabled;
    }

    function appendMessage(role, content) {
      const div = document.createElement('div');
      div.className = `n4ra-msg ${role === 'user' ? 'n4ra-msg-user' : 'n4ra-msg-ai'}`;
      div.innerHTML = role === 'user'
        ? escapeHtml(content).replace(/\n/g, '<br>')
        : String(content || '');
      messageBox.appendChild(div);
      messageBox.scrollTop = messageBox.scrollHeight;
    }

    function formatAnswer(text) {
      if (!text) return '';
      const normalizedText = String(text).replace(
        /^(\d+)\.\s+(\d{1,2}\s+(Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember)\b.*)$/gim,
        '- $2'
      );
      let html = escapeHtml(normalizedText);
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
          .map(function(cell) {
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
          headerCells.forEach(function(cell) {
            tableHtml += '<th>' + cell + '</th>';
          });
          tableHtml += '</tr></thead><tbody>';

          tableRows.forEach(function(rowCells) {
            tableHtml += '<tr>';
            headerCells.forEach(function(_, cellIndex) {
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

    function shouldHideSources(answerText) {
      const text = String(answerText || '').toLowerCase();
      if (!text) return false;
      return (
        text.indexOf('tidak ditemukan dalam dokumen yang tersedia') !== -1 ||
        text.indexOf('tidak ada dokumen yang relevan ditemukan') !== -1 ||
        text.indexOf('informasi tersebut tidak ditemukan') !== -1
      );
    }

    function formatSources(sources) {
      if (!Array.isArray(sources) || sources.length === 0) return '';

      const groupedSources = {};
      sources.forEach(function(s) {
        const key = s.doc_id || s.filename || 'unknown';
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
        const pageArr = Array.from(doc.pages).sort(function(a, b) { return parseInt(a, 10) - parseInt(b, 10); });
        const pageStr = pageArr.length > 0 ? pageArr.join(', ') : '-';
        const docId = doc.doc_id || '';

        html += '<div class="source-card">' +
          '<div class="source-info">' +
          '  <div class="source-title" title="' + escapeHtml(judul) + '">' +
          '    <span class="badge badge-primary mr-1">' + escapeHtml(jenis) + '</span>' + escapeHtml(nomor) +
          '  </div>' +
          '  <div class="source-meta">' +
          '    <i class="fas fa-file-alt mr-1"></i>' + escapeHtml(judulShort) +
          '    <span class="ml-2"><i class="fas fa-bookmark mr-1"></i>Hal. ' + escapeHtml(pageStr) + '</span>' +
          '  </div>' +
          '</div>';

        if (docId) {
          html += '<button class="btn-view-source" type="button" data-doc-id="' + escapeHtml(String(docId)) + '" title="Lihat Dokumen">' +
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

    function formatFollowUpQuestions(questions) {
      if (!Array.isArray(questions) || questions.length === 0) return '';
      const uniqueQuestions = [];
      questions.forEach(function(q) {
        const qq = String(q || '').trim();
        if (qq && uniqueQuestions.indexOf(qq) === -1) uniqueQuestions.push(qq);
      });
      if (uniqueQuestions.length === 0) return '';

      let html = '<div class="followup-section">';
      html += '<div class="followup-label"><i class="fas fa-lightbulb mr-1"></i>Saran pertanyaan lanjutan</div>';
      uniqueQuestions.slice(0, 3).forEach(function(q) {
        const safeQ = escapeHtml(q);
        html += '<button type="button" class="followup-btn" data-question="' + safeQ + '">' + safeQ + '</button>';
      });
      html += '</div>';
      return html;
    }

    function renderDefaultGreeting() {
      messageBox.innerHTML = '';
      appendMessage('ai', formatAnswer('Halo, saya N4R4 AI Assistance. Saya siap membantu pertanyaan seputar dokumen dan informasi internal.'));
    }

    async function createSession() {
      const response = await fetch(endpointCreateSession, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        },
        body: JSON.stringify({ title: 'Widget Customer Service' })
      });
      if (!response.ok) {
        throw new Error('Gagal membuat sesi AI');
      }
      const data = await response.json();
      const sessionId = data && data.session ? data.session.id : null;
      if (!sessionId) {
        throw new Error('Session ID tidak tersedia');
      }
      currentSessionId = String(sessionId);
      localStorage.setItem(storageKey, currentSessionId);
      return currentSessionId;
    }

    async function loadSessionsList(preferredSessionId) {
      const response = await fetch(endpointSessions, {
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        }
      });

      if (!response.ok) {
        throw new Error('Gagal memuat daftar sesi');
      }

      const data = await response.json();
      const sessions = Array.isArray(data.sessions) ? data.sessions : [];

      sessionSelect.innerHTML = '<option value="">-- Pilih riwayat chat --</option>';
      sessions.forEach(function(session) {
        const option = document.createElement('option');
        option.value = String(session.id);
        option.textContent = (session.title || 'Percakapan Baru') + (session.model ? ' [' + session.model + ']' : '');
        sessionSelect.appendChild(option);
      });

      if (typeof data.token_balance !== 'undefined') {
        setTokenBalance(data.token_balance);
      }

      const wanted = preferredSessionId ? String(preferredSessionId) : (currentSessionId ? String(currentSessionId) : '');
      const exists = wanted && sessions.some(function(s) { return String(s.id) === wanted; });
      if (exists) {
        currentSessionId = wanted;
        localStorage.setItem(storageKey, currentSessionId);
        sessionSelect.value = wanted;
      } else {
        currentSessionId = null;
        localStorage.removeItem(storageKey);
        sessionSelect.value = '';
      }

      return sessions;
    }

    async function loadSessionMessages(sessionId) {
      const targetSessionId = sessionId ? String(sessionId) : (currentSessionId ? String(currentSessionId) : '');
      if (!targetSessionId) return false;

      const response = await fetch(`${endpointSessionMessagesBase}/${targetSessionId}/messages`, {
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        }
      });

      if (!response.ok) return false;

      const data = await response.json();
      const messages = Array.isArray(data.messages) ? data.messages : [];

      messageBox.innerHTML = '';
      if (messages.length === 0) {
        renderDefaultGreeting();
      } else {
        messages.forEach(function(item) {
          const role = item.role === 'user' ? 'user' : 'ai';
          if (role === 'user') {
            appendMessage(role, extractDisplayedUserQuestion(item.content || ''));
          } else {
            const answerText = item.content || '';
            appendMessage('ai',
              formatAnswer(answerText) +
              formatFollowUpQuestions(item.follow_up_questions || []) +
              (shouldHideSources(answerText) ? '' : formatSources(item.sources || [])) +
              formatUsage(item.usage || {})
            );
          }
        });
      }

      if (typeof data.token_balance !== 'undefined') {
        setTokenBalance(data.token_balance);
      }
      currentSessionId = targetSessionId;
      localStorage.setItem(storageKey, currentSessionId);
      sessionSelect.value = targetSessionId;
      hasLoadedHistory = true;
      return true;
    }

    async function ensureSessionAndHistory() {
      await loadSessionsList(currentSessionId);

      if (!currentSessionId) {
        renderDefaultGreeting();
        hasLoadedHistory = true;
        return;
      }

      const loaded = await loadSessionMessages(currentSessionId);
      if (!loaded) {
        localStorage.removeItem(storageKey);
        currentSessionId = null;
        sessionSelect.value = '';
        renderDefaultGreeting();
        hasLoadedHistory = true;
      }
    }

    async function sendMessage() {
      const question = (inputEl.value || '').trim();
      if (!question || isLoading) return;

      inputEl.value = '';
      appendMessage('user', question);
      appendMessage('ai', 'Sedang memproses pertanyaan Anda...');
      const loadingNode = messageBox.lastChild;

      isLoading = true;
      sendBtn.disabled = true;
      setToolbarDisabled(true);

      try {
        if (!currentSessionId) {
          await createSession();
          await loadSessionsList(currentSessionId);
        }

        const response = await fetch(endpointQuery, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            question: question,
            session_id: Number(currentSessionId)
          })
        });

        const data = await response.json();
        if (loadingNode && loadingNode.parentNode) {
          loadingNode.parentNode.removeChild(loadingNode);
        }

        if (!response.ok || (data && data.error)) {
          const message = data && (data.answer || data.message)
            ? (data.answer || data.message)
            : 'Maaf, terjadi kendala saat memproses pertanyaan.';
          appendMessage('ai', formatAnswer(message));
          if (data && typeof data.token_balance !== 'undefined') {
            setTokenBalance(data.token_balance);
          }
          return;
        }

        const answerText = data.answer || 'Maaf, jawaban belum tersedia.';
        appendMessage('ai',
          formatAnswer(answerText) +
          formatFollowUpQuestions(data.follow_up_questions || []) +
          (shouldHideSources(answerText) ? '' : formatSources(data.sources || [])) +
          formatUsage(data.usage || {})
        );
        if (data.session_id) {
          currentSessionId = String(data.session_id);
          localStorage.setItem(storageKey, currentSessionId);
          await loadSessionsList(currentSessionId);
        }
        if (typeof data.token_balance !== 'undefined') {
          setTokenBalance(data.token_balance);
        }
      } catch (err) {
        if (loadingNode && loadingNode.parentNode) {
          loadingNode.parentNode.removeChild(loadingNode);
        }
        appendMessage('ai', formatAnswer('Koneksi ke AI Assistance sedang bermasalah. Coba beberapa saat lagi.'));
      } finally {
        isLoading = false;
        sendBtn.disabled = false;
        setToolbarDisabled(false);
        inputEl.focus();
      }
    }

    widgetBtn.addEventListener('click', async function() {
      if (widgetPanel.style.display === 'flex') {
        widgetPanel.style.display = 'none';
        widgetBtn.style.display = 'inline-block';
        return;
      }

      widgetPanel.style.display = 'flex';
      widgetBtn.style.display = 'none';
      inputEl.focus();

      if (!hasLoadedHistory) {
        renderDefaultGreeting();
      }

      try {
        setToolbarDisabled(true);
        await ensureSessionAndHistory();
      } catch (err) {
        if (!hasLoadedHistory) {
          renderDefaultGreeting();
        }
        appendMessage('ai', formatAnswer('Belum bisa terhubung ke layanan AI saat ini.'));
      } finally {
        setToolbarDisabled(false);
      }
    });

    widgetClose.addEventListener('click', function() {
      widgetPanel.style.display = 'none';
      widgetBtn.style.display = 'inline-block';
    });

    sessionSelect.addEventListener('change', async function() {
      const selectedId = sessionSelect.value ? String(sessionSelect.value) : '';
      if (!selectedId) {
        currentSessionId = null;
        localStorage.removeItem(storageKey);
        renderDefaultGreeting();
        return;
      }

      try {
        setToolbarDisabled(true);
        const loaded = await loadSessionMessages(selectedId);
        if (!loaded) {
          renderDefaultGreeting();
        }
      } catch (err) {
        renderDefaultGreeting();
        appendMessage('ai', formatAnswer('Gagal memuat riwayat chat yang dipilih.'));
      } finally {
        setToolbarDisabled(false);
      }
    });

    newChatBtn.addEventListener('click', async function() {
      if (isLoading) return;
      try {
        setToolbarDisabled(true);
        const newSessionId = await createSession();
        await loadSessionsList(newSessionId);
        renderDefaultGreeting();
        inputEl.focus();
      } catch (err) {
        appendMessage('ai', formatAnswer('Gagal membuat chat baru. Silakan coba lagi.'));
      } finally {
        setToolbarDisabled(false);
      }
    });

    sendBtn.addEventListener('click', sendMessage);
    inputEl.addEventListener('keydown', function(e) {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
      }
    });

    messageBox.addEventListener('click', function(e) {
      const followupBtn = e.target.closest('.followup-btn');
      if (followupBtn) {
        const q = followupBtn.getAttribute('data-question') || '';
        inputEl.value = q;
        inputEl.focus();
        return;
      }

      const sourceBtn = e.target.closest('.btn-view-source');
      if (sourceBtn) {
        const docId = sourceBtn.getAttribute('data-doc-id');
        if (!docId) return;
        const sourceIdInput = document.getElementById('n4raViewSourceId');
        const sourceForm = document.getElementById('n4raViewSourceForm');
        if (!sourceIdInput || !sourceForm) return;
        sourceIdInput.value = docId;
        window.open('', 'n4ra-doc-viewer', 'width=900,height=700');
        sourceForm.submit();
      }
    });

    renderDefaultGreeting();
  })();
</script>

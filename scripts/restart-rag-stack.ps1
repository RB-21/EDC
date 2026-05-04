param(
    [string]$EdcPath = (Resolve-Path (Join-Path $PSScriptRoot "..")).Path
)

$ErrorActionPreference = "Stop"

function Write-Step([string]$Message) {
    Write-Host ""
    Write-Host "==> $Message" -ForegroundColor Cyan
}

function Wait-HttpOk([string]$Url, [int]$TimeoutSeconds = 60) {
    $deadline = (Get-Date).AddSeconds($TimeoutSeconds)
    while ((Get-Date) -lt $deadline) {
        try {
            $resp = Invoke-WebRequest -Uri $Url -UseBasicParsing -TimeoutSec 5
            if ($resp.StatusCode -ge 200 -and $resp.StatusCode -lt 300) {
                return $true
            }
        } catch {
            Start-Sleep -Milliseconds 800
        }
    }
    return $false
}

$AppsRoot = Split-Path $EdcPath -Parent
$RagPath = Join-Path $AppsRoot "EDC AI RAG"
$RagPython = Join-Path $RagPath "venv\python.exe"

if (-not (Test-Path $RagPath)) {
    throw "Folder RAG tidak ditemukan: $RagPath"
}
if (-not (Test-Path $RagPython)) {
    throw "Python venv RAG tidak ditemukan: $RagPython"
}

Write-Step "Stop proses RAG lama (python/uvicorn) berdasarkan path workspace"
$ragPattern = [regex]::Escape($RagPath)
$stale = Get-CimInstance Win32_Process |
    Where-Object {
        $_.Name -in @("python.exe", "uvicorn.exe") -and
        $_.CommandLine -match $ragPattern
    }

foreach ($proc in $stale) {
    try {
        Stop-Process -Id $proc.ProcessId -Force -ErrorAction Stop
        Write-Host "Stopped PID $($proc.ProcessId) - $($proc.Name)"
    } catch {
        Write-Host "Skip PID $($proc.ProcessId): $($_.Exception.Message)" -ForegroundColor Yellow
    }
}

Write-Step "Stop proses yang masih menahan port 8100 (jika ada)"
$portPids = Get-NetTCPConnection -LocalPort 8100 -ErrorAction SilentlyContinue |
    Select-Object -ExpandProperty OwningProcess -Unique
foreach ($pid in $portPids) {
    if ($pid -and $pid -ne 0) {
        try {
            Stop-Process -Id $pid -Force -ErrorAction Stop
            Write-Host "Stopped PID $pid (port 8100)"
        } catch {
            Write-Host "Skip PID ${pid}: $($_.Exception.Message)" -ForegroundColor Yellow
        }
    }
}

Write-Step "Pastikan Qdrant hidup via Docker Compose (EDC AI RAG)"
Push-Location $RagPath
try {
    docker compose up -d qdrant | Out-Host
} finally {
    Pop-Location
}

Write-Step "Tunggu Qdrant siap di http://127.0.0.1:6333/healthz"
if (-not (Wait-HttpOk "http://127.0.0.1:6333/healthz" 45)) {
    throw "Qdrant tidak siap dalam 45 detik."
}
Write-Host "Qdrant ready."

Write-Step "Start RAG service di port 8100"
Start-Process -FilePath $RagPython `
    -ArgumentList "-m uvicorn main:app --host 0.0.0.0 --port 8100" `
    -WorkingDirectory $RagPath `
    -WindowStyle Hidden

Write-Step "Tunggu RAG siap di http://127.0.0.1:8100/health"
if (-not (Wait-HttpOk "http://127.0.0.1:8100/health" 90)) {
    throw "RAG service tidak siap dalam 90 detik."
}

$health = Invoke-RestMethod -Uri "http://127.0.0.1:8100/health" -TimeoutSec 10
Write-Host "RAG health: $($health.status) | Qdrant=$($health.qdrant_connected) DB=$($health.db_connected) GCP=$($health.gcp_connected)"

Write-Step "Verifikasi akses dari container Laravel app -> host.docker.internal:8100/health"
Push-Location $EdcPath
try {
    docker compose exec -T app sh -lc "curl -sS -m 10 http://host.docker.internal:8100/health" | Out-Host
} finally {
    Pop-Location
}

Write-Step "Selesai"
Write-Host "Silakan refresh halaman AI Assistant (admin/rag/chat)."

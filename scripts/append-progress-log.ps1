param(
    [Parameter(Mandatory = $true)]
    [string]$Scope,

    [Parameter(Mandatory = $true)]
    [string]$Files,

    [Parameter(Mandatory = $true)]
    [string]$Verification,

    [string]$Risks = "-"
)

$ErrorActionPreference = "Stop"

$root = Resolve-Path (Join-Path $PSScriptRoot "..")
$logPath = Join-Path $root "docs/rag_fastapi/WORKLOG.md"

if (!(Test-Path $logPath)) {
    throw "WORKLOG.md tidak ditemukan di: $logPath"
}

$timestamp = Get-Date -Format "yyyy-MM-dd HH:mm (K)"
$entry = @"

---

## $timestamp
Scope:
- $Scope

Files:
- $Files

Verification:
- $Verification

Risks:
- $Risks
"@

Add-Content -LiteralPath $logPath -Value $entry
Write-Host "Progress appended to $logPath"

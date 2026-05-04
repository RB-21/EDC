# RAG Troubleshooting & Recovery (EDC + EDC AI RAG)

Dokumen ini untuk menangani error seperti:
- `cURL error 7: Failed to connect to host.docker.internal:8100`
- `Error saat memproses query: [WinError 10061] ... actively refused it`
- Status AI Assistant terlihat `Offline`

## Arsitektur Singkat
- Laravel EDC berjalan di container Docker (`app`, `nginx`) dari folder `D:\Apps\EDC`.
- Service RAG FastAPI berjalan di host Windows (bukan di container) pada port `8100`.
- Qdrant berjalan di Docker dari folder `D:\Apps\EDC AI RAG` pada port `6333/6334`.
- Container Laravel mengakses RAG via `http://host.docker.internal:8100`.

## Gejala Umum dan Penyebab
1. `cURL error 7` dari Laravel ke RAG:
- RAG belum hidup.
- Port `8100` bentrok/stuck oleh proses lama.
- Proses RAG lama crash saat startup.

2. `WinError 10061` dari endpoint `/query`:
- Biasanya service backend yang dipanggil RAG menolak koneksi (proses tidak siap/stale).
- Sering pulih dengan restart bersih seluruh proses terkait.

3. Status `Offline` di halaman chat:
- Jika `/health` gagal diakses, memang offline.
- Jika `/health` mengembalikan `degraded`, UI sekarang tampil `Degraded` (bukan `Offline`).

## Recovery Cepat (Direkomendasikan)
Jalankan script berikut dari PowerShell:

```powershell
cd D:\Apps\EDC
.\scripts\restart-rag-stack.ps1
```

Script ini akan:
1. Menghentikan semua proses Python/Uvicorn lama yang terkait `EDC AI RAG`.
2. Menghentikan proses apapun yang masih memegang port `8100`.
3. Menyalakan `qdrant` via Docker Compose (`EDC AI RAG`).
4. Menyalakan ulang RAG FastAPI (`uvicorn main:app --port 8100`).
5. Verifikasi health dari host dan dari container Laravel (`host.docker.internal`).

## Recovery Manual (Jika Perlu)
1. Stop proses RAG lama:
```powershell
Get-CimInstance Win32_Process | ? { $_.Name -in 'python.exe','uvicorn.exe' -and $_.CommandLine -match 'EDC AI RAG' } | % { Stop-Process -Id $_.ProcessId -Force }
```

2. Pastikan port 8100 kosong:
```powershell
Get-NetTCPConnection -LocalPort 8100 -ErrorAction SilentlyContinue | select LocalAddress,LocalPort,State,OwningProcess
```

3. Start Qdrant:
```powershell
cd "D:\Apps\EDC AI RAG"
docker compose up -d qdrant
```

4. Start RAG:
```powershell
cd "D:\Apps\EDC AI RAG"
.\venv\python.exe -m uvicorn main:app --host 0.0.0.0 --port 8100
```

5. Uji dari host:
```powershell
Invoke-WebRequest http://127.0.0.1:8100/health -UseBasicParsing
```

6. Uji dari container Laravel:
```powershell
cd "D:\Apps\EDC"
docker compose exec -T app sh -lc "curl -sS http://host.docker.internal:8100/health"
```

## Checklist Sukses
- `http://127.0.0.1:8100/health` mengembalikan JSON.
- Dari container `app`, URL `host.docker.internal:8100/health` bisa diakses.
- Halaman `admin/rag/chat` menampilkan `Online` atau `Degraded`.
- Query chat tidak lagi mengembalikan `WinError 10061` / `cURL error 7`.

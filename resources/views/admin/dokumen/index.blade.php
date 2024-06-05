@extends('admin.template')

@section('css_libraries')
    {{-- <link rel="stylesheet" href="{{ asset('selectize.js/dist/css/selectize.bootstrap4.css') }}"> --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.13.1/datatables.min.css" />
@endsection

@section('additional_style')
    <style>
        .btn-history-perubahan{
            cursor: pointer;
        }
    </style>
@endsection

@section('title')
    EDC | PTPN VI
@endsection

@section('page-name')
    Dokumen
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Master Dokumen</h1>
        </div>
        <div class="card">
            <div class="card-header pb-0 justify-content-between">
                <h5>Daftar Dokumen</h5>
                <button class="btn btn-primary mb-0" id="btnTambahDokumen">
                    <span>Tambah</span>
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-items-center w-100" id="table-user">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Jenis</th>
                                <th>Nomor</th>
                                <th>Judul</th>
                                <th>Tanggal</th>
                                <th>Bagian</th>
                                <th>Level</th>
                                <th>Status</th>
                                <th>Dokumen</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
    <form id="tampilForm" method="post" enctype="multipart/form-data" action="{{ route('admin.dokumen.tampil') }}"
        target="result">
        @csrf
        <input type="hidden" name="id" id="tampilId">
        {{-- <button type="button" id="tampilButton">Send</button> --}}
    </form>
    <form action="{{ route('admin.downloadDokumen') }}" method="POST" id="downloadForm" target="download">
        @csrf
        <input type="hidden" name="id" id="downloadId">

    </form>
@endsection

@section('modals')
    <div class="modal fade" id="tambahModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Dokumen</h5>
                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.dokumen.simpan') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="basic-url" class="form-label">Jenis Dokumen</label>
                                    {{-- <span class="form-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                    <select name="jenis_file" id="jenis_file" class="form-control" data-live-search="true"
                                        data-style="p-0" required>
                                        <option value="">-- Pilih Jenis File --</option>
                                        @foreach ($master_jenis_file as $item)
                                            <option value="{{ $item->kode }}">{{ $item->kepanjangan }}
                                                ({{ $item->singkatan }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="basic-url" class="form-label">Nomor</label>
                                    {{-- <span class="form-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                    <input type="text" class="form-control" id="nomor" name="nomor" data-modal="tambah" required>
                                    <small class="badge badge-primary mt-1 checkNomor" data-modal="tambah">Check nomor</small>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="basic-url" class="form-label">Judul</label>
                                    {{-- <span class="form-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                    <input type="text" class="form-control" id="judul" name="judul" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="jabatan" class="form-label">Tanggal</label>
                                    {{-- <span class="form-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                    <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="basic-url" class="form-label">Bagian / Unit Usaha</label>
                                    {{-- <span class="form-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                    <select name="bagian" id="bagian" class="form-control" data-live-search="true"
                                        data-style="p-0" required>
                                        <option value="">-- Pilih Bagian --</option>
                                        @foreach ($master_bagian as $item)
                                            <option value="{{ $item->kode_bagian }}">{{ $item->nama_bagian }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="basic-url" class="form-label">Level Akses User</label>
                                    {{-- <span class="form-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                    <select name="level[]" id="level" class="form-control" data-style="p-0" multiple
                                        required>
                                        @foreach ($master_user_level as $item)
                                            <option value="{{ $item->level }}" selected>{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group mb-3">
                                    <label for="basic-url" class="form-label">File PDF</label>
                                    {{-- <span class="form-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                    <input type="file" class="form-control" id="dokumen" name="dokumen"
                                        accept="application/pdf" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="basic-url" class="form-label">Status Dokumen</label>
                                    {{-- <span class="form-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                    <select name="status_dokumen" id="status_dokumen" class="form-control" data-style="p-0"
                                        required>
                                        <option value="">-- Pilih Status Dokumen --</option>
                                        @foreach ($master_status_perubahan as $item)
                                            <option value="{{ $item->id }}" data-status="{{ $item->nama }}">{{ $item->keterangan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 perubahan-container">
                                <div class="form-group mb-3">
                                    <label for="perubahan-label" class="form-label">Dokumen yang Berubah</label>
                                    {{-- <span class="form-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                    <select name="dokumen_berubah" id="dokumen_berubah" class="form-control" data-style="p-0" data-live-search="true"
                                        required>
                                        <option value="">-- Cari Dokumen --</option>
                                        {{-- @foreach ($master_status_perubahan as $item)
                                            <option value="{{ $item->nama }}" data-status="{{ $item->nama }}">{{ $item->keterangan }}</option>
                                        @endforeach --}}
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editDokumenModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Dokumen</h5>
                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.dokumen.edit') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="basic-url" class="form-label">Jenis Dokumen</label>
                                    {{-- <span class="form-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                    <select name="jenis_file" id="jenis_file" class="form-control" data-live-search="true"
                                        data-style="p-0" required>
                                        <option value="">-- Pilih Jenis File --</option>
                                        @foreach ($master_jenis_file as $item)
                                            <option value="{{ $item->kode }}">{{ $item->kepanjangan }}
                                                ({{ $item->singkatan }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="basic-url" class="form-label">Nomor</label>
                                    {{-- <span class="form-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                    <input type="text" class="form-control" id="nomor" name="nomor" data-modal="edit" required>
                                    <small class="badge badge-primary mt-1 checkNomor" data-modal="edit">Check nomor</small>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="basic-url" class="form-label">Judul</label>
                                    {{-- <span class="form-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                    <input type="text" class="form-control" id="judul" name="judul" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="jabatan" class="form-label">Tanggal</label>
                                    {{-- <span class="form-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                    <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="basic-url" class="form-label">Bagian / Unit Usaha</label>
                                    {{-- <span class="form-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                    <select name="bagian" id="bagian" class="form-control" data-live-search="true"
                                        data-style="p-0" required>
                                        <option value="">-- Pilih Bagian --</option>
                                        @foreach ($master_bagian as $item)
                                            <option value="{{ $item->kode_bagian }}">{{ $item->nama_bagian }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="basic-url" class="form-label">Level Akses User</label>
                                    {{-- <span class="form-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                    <select name="level[]" id="level" class="form-control" data-style="p-0" multiple
                                        required>
                                        @foreach ($master_user_level as $item)
                                            <option value="{{ $item->level }}" selected>{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group mb-3">
                                    <label for="basic-url" class="form-label">File PDF</label>
                                    {{-- <span class="form-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                    <input type="file" class="form-control" id="dokumen" name="dokumen"
                                        accept="application/pdf">
                                    <small class="font-weight-bold"> <span class="text-primary">Note : </span> Kosongkan jika tidak ingin merubah file</small>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="basic-url" class="form-label">Status Dokumen</label>
                                    {{-- <span class="form-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                    <select name="status_dokumen" id="status_dokumen" class="form-control" data-style="p-0"
                                        required>
                                        <option value="">-- Pilih Status Dokumen --</option>
                                        @foreach ($master_status_perubahan as $item)
                                            <option value="{{ $item->id }}" data-status="{{ $item->nama }}">{{ $item->keterangan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 perubahan-container">
                                <div class="form-group mb-3">
                                    <label for="perubahan-label" class="form-label">Dokumen yang Berubah</label>
                                    {{-- <span class="form-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                    <select name="dokumen_berubah" id="dokumen_berubah" class="form-control" data-style="p-0" data-live-search="true"
                                        required>
                                        <option value="">-- Cari Dokumen --</option>
                                        {{-- @foreach ($master_status_perubahan as $item)
                                            <option value="{{ $item->nama }}" data-status="{{ $item->nama }}">{{ $item->keterangan }}</option>
                                        @endforeach --}}
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Edit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="historyDokumenModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">History Dokumen</h5>
                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table-sm table-bordered w-100" id="tableDokumenModal" >
                            <thead class="table-primary">
                                <th>No</th>
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                                {{-- <th>Tanggal</th> --}}
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    {{-- <button type="button" class="btn btn-primary">Tambah</button> --}}
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="historyPerubahanDokumenModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">History Perubahan Dokumen</h5>
                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table-sm table-bordered w-100" id="tablePerubahanDokumenModal" >
                            <thead class="table-primary">
                                <th>No</th>
                                <th>No Dokumen</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Status Perubahan</th>
                                <th>Dokumen Baru</th>
                                {{-- <th>Tanggal</th> --}}
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    {{-- <button type="button" class="btn btn-primary">Tambah</button> --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- <script src="{{ asset('selectize.js/dist/js/selectize.min.js') }}"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.13.1/datatables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js" integrity="sha512-CryKbMe7sjSCDPl18jtJI5DR5jtkUWxPXWaLCst6QjH8wxDexfRJic2WRmRXmstr2Y8SxDDWuBO6CQC6IE4KTA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        function isNumber(evt) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            }

            return true;
        }

        function toggleDisplayNone(elementSelector) {
            if (elementSelector.hasClass('d-none')) {
                elementSelector.removeClass('d-none')
            } else {
                elementSelector.addClass('d-none')
            }
            return true
        }

        function renderSelectOption(listOptions, selectSelector){
            let html = ``;
            listOptions.forEach((element, index) => {
                html += `<option value="${element.id}">${element.nomor} | ${element.judul} | ${element.tanggal}<option>`
            });
            selectSelector.append(html)
            return true
        }

        $(document).ready(function() {
            {{-- Kalau ada error saat menambahkan user maka akan menampilkan error  --}}
            @if ($errors->any())
                let errors = JSON.parse(atob('{{ base64_encode(json_encode($errors->all())) }}'))
                let htmlError = '<ol>'
                let listErrors = errors.map((message) => {
                    return `<li>${message}</li>`
                })
                listErrors = listErrors.join(' ')
                htmlError += listErrors
                htmlError += '</ol>'
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: htmlError
                })
            @endif

            let tableUser = $('#table-user').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.dokumen.getDataDokumen') }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                },
                language: {
                    'paginate': {
                        'previous': '<i class="fas fa-angle-double-left"></i>',
                        'next': '<i class="fas fa-angle-double-right"></i>',
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: 'text-center'
                    },
                    {
                        data: 'd_jenis_file.singkatan',
                        name: 'd_jenis_file.singkatan',
                    },
                    {
                        data: 'nomor',
                        name: 'nomor',
                    },
                    {
                        data: 'judul',
                        name: 'judul',
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal',
                    },
                    {
                        data: 'd_bagian.nama_bagian',
                        name: 'd_bagian.nama_bagian',
                    },
                    {
                        data: 'level',
                        name: 'level',
                    },
                    {
                        data: null,
                        name: null,
                        render: function(data){
                            return `<span class="badge badge-${data.d_status.class_color} btn-history-perubahan" data-id="${data.id}">${data.d_status.alias}</span>`
                        }
                    },
                    {
                        data: null,
                        name: null,
                        render: function(data) {
                            return `
                            <button data-id="${data.id}" class="btn btn-sm btn-primary btn-tampil">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button data-id="${data.id}" class="btn btn-sm btn-success btn-download">
                                <i class="fas fa-download"></i>
                            </button>
                            <button data-id="${data.id}" class="btn btn-sm btn-warning btn-history">
                                <i class="fas fa-history"></i>
                            </button>
                            <button class="btn btn-sm btn-info btn-edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            `
                        }
                    },
                ],
            })

            let dokumenHistoryId
            let tableDokumenHistory = $('#tableDokumenModal').DataTable({
                processing: true,
                serverSide: true,
                deferLoading: 0,
                ajax: {
                    url: "{{ route('admin.dokumen.getDokumenHistory') }}",
                    method: 'POST',
                    data: function(d){
                        d._token =  "{{ csrf_token() }}"
                        d.id = dokumenHistoryId
                    }
                },
                language: {
                    'paginate': {
                        'previous': '<i class="fas fa-angle-double-left"></i>',
                        'next': '<i class="fas fa-angle-double-right"></i>',
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: 'text-center'
                    },
                    {
                        data: 'user.nik',
                        name: 'user.nik',
                    },
                    {
                        data: 'user.name',
                        name: 'user.name',
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data){
                            return `${moment(data).format('DD-MM-YYYY HH:mm:ss')} WIB`
                        }
                    },
                    {
                        data: 'aksi.nama',
                        name: 'aksi.nama',
                    },
                ],
            })

            let modalPerubahanHistory = $('#historyPerubahanDokumenModal')
            let dokumenPerubahanHistoryId
            let tablePerubahanDokumenHistory = $('#tablePerubahanDokumenModal').DataTable({
                processing: true,
                serverSide: true,
                deferLoading: 0,
                ajax: {
                    url: "{{ route('admin.dokumen.getPerubahanDokumenHistory') }}",
                    method: 'POST',
                    data: function(d){
                        d._token =  "{{ csrf_token() }}"
                        d.id = dokumenPerubahanHistoryId
                    }
                },
                language: {
                    'paginate': {
                        'previous': '<i class="fas fa-angle-double-left"></i>',
                        'next': '<i class="fas fa-angle-double-right"></i>',
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: 'text-center'
                    },
                    {
                        data: null,
                        name: null,
                        render: function(row){
                            return `<a class="text-primary btn-tampil" style="cursor:pointer;" data-id="${row.id}">${row.nomor}</a>`
                        }
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal',
                    },
                    {
                        data: 'd_status',
                        name: 'd_status',
                        render: function(data){
                            return `<span class="badge badge-${data.class_color}" >${data.alias}</span>`
                        }
                    },
                    {
                        data: 'd_status_perubahan',
                        name: 'd_status_perubahan',
                        render: function(data){
                            return `<span class="badge badge-${data.class_color}" >${data.alias}</span>`
                        }
                    },
                    {
                        data: null,
                        name: null,
                        render: function(data){
                            if(data.d_next){
                                return `<p>
                                    ${data.d_next.nomor}
                                </p>`
                            }
                            return '-'
                        }
                    },
                    // {
                    //     data: 'DT_RowIndex',
                    //     name: 'DT_RowIndex',
                    // },
                    // {
                    //     data: null,
                    //     name: null,
                    //     render: function(data) {
                    //         return `
                    //         <button data-id="${data.id}" class="btn btn-sm btn-primary btn-tampil">
                    //             <i class="fas fa-eye"></i>
                    //         </button>
                    //         <button data-id="${data.id}" class="btn btn-sm btn-warning btn-history">
                    //             <i class="fas fa-history"></i>
                    //         </button>
                    //         <button class="btn btn-sm btn-info btn-edit">
                    //             <i class="fas fa-edit"></i>
                    //         </button>
                    //         `
                    //     }
                    // },
                ],
            })

            $('select.form-control').selectpicker()

            $('#btnTambahDokumen').click(function() {
                $('#tambahModal').modal('show')
            })

            $('input[name="nomor"]').keyup(function() {
                let value = $(this).val().trim()
                let modal = $(this).data('modal')
                $(this).val(value)

                $.ajax({
                    url: '{{ route('admin.dokumen.check-nomor') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        nomor: value
                    }
                }).done(function(resp) {
                    let checkNomorElement = $(`.checkNomor[data-modal="${modal}"]`)
                    if (resp.status === 1) {
                        if (checkNomorElement.hasClass('badge-primary')) {
                            checkNomorElement.removeClass('badge-primary')
                            checkNomorElement.addClass('badge-danger')
                        }
                        checkNomorElement.html('Nomor Sudah Terdaftar')
                    } else if (resp.status === 0) {
                        if (checkNomorElement.hasClass('badge-danger')) {
                            checkNomorElement.removeClass('badge-danger')
                            checkNomorElement.addClass('badge-primary')
                        }
                        checkNomorElement.html('Nomor Bisa Digunakan')
                    }
                }).fail(function(err) {
                    console.log(err)
                })
            })

            $(document).on('change', 'input[type=file]', function() {
                let inputAcceptProperties = $(this).prop('accept').split(',')
                let file = this.files[0]
                if (inputAcceptProperties.filter(property => property.replace(' ', '') == file.type)
                    .length > 0) {
                    window.open(URL.createObjectURL(this.files[0]), this.files[0].name,
                        'width="50%,height=500px"')
                } else {
                    $(this).val('')
                    Swal.fire({
                        icon: 'error',
                        title: 'Format Salah',
                        text: `Upload File Dengan Tipe ${inputAcceptProperties}`,
                        customClass: {
                            container: 'my-swal'
                        }
                    })
                }
            })


            let selectModalTambahPerubahanSelector = $('#tambahModal select[name=dokumen_berubah]')

            $(document).on('change', '#tambahModal select[name=status_dokumen]', function(){
                let status = $(this).find('option').filter(':selected').data('status')
                if(['dicabut','revisi'].includes(status)){
                    selectModalTambahPerubahanSelector.prop('required', true)
                } else {
                    selectModalTambahPerubahanSelector.prop('required', false)
                }
                return 1
            })

            $(document).on('keyup', '#tambahModal .perubahan-container .bs-searchbox input[type=search]', function(){
                let input = $(this).val()
                $.ajax({
                    url: "{{ route('admin.dokumen.getDokumenPerubahan') }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        input: input
                    }
                }).done((resp) => {
                    selectModalTambahPerubahanSelector.find('option').not(':first').remove()
                    renderSelectOption(resp.data, selectModalTambahPerubahanSelector)
                    selectModalTambahPerubahanSelector.selectpicker('refresh')
                    selectModalTambahPerubahanSelector.selectpicker('render')
                }).fail((err) => {
                    Swal.fire({
                        icon: 'error',

                    })
                })
            })

            let selectModalEditPerubahanSelector = $('#editDokumenModal select[name=dokumen_berubah]')

            $(document).on('change', '#editDokumenModal select[name=status_dokumen]', function(){
                let status = $(this).find('option').filter(':selected').data('status')
                if(['dicabut','revisi'].includes(status)){
                    selectModalEditPerubahanSelector.prop('required', true)
                } else {
                    selectModalEditPerubahanSelector.prop('required', false)
                }
                return 1
            })

            $(document).on('keyup', '#editDokumenModal .perubahan-container .bs-searchbox input[type=search]', function(){
                let input = $(this).val()
                $.ajax({
                    url: "{{ route('admin.dokumen.getDokumenPerubahan') }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        input: input
                    },
                    async: false
                }).done((resp) => {
                    selectModalEditPerubahanSelector.find('option').not(':first').remove()
                    renderSelectOption(resp.data, selectModalEditPerubahanSelector)
                    selectModalEditPerubahanSelector.selectpicker('refresh')
                    selectModalEditPerubahanSelector.selectpicker('render')
                }).fail((err) => {
                    console.log(err)
                    Swal.fire({
                        icon: 'error',

                    })
                })
            })

            $(document).on('click', '.btn-tampil', function() {
                $('#tampilId').val($(this).data('id'))
                window.open('{{ route('admin.dokumen.tampil') }}', 'result', 'width=500,height=700')
                $('#tampilForm').submit()
            })

            // $(document).on('click', '.btn-history', function() {
            //     let id = $(this).data('id')
            //     $.ajax({
            //         url: '{{ route('admin.dokumen.getDokumenHistory') }}',
            //         method: 'POST',
            //         data: {
            //             _token: '{{ csrf_token() }}',
            //             id: id
            //         }
            //     }).done(function(resp) {
            //         let modalHistory = $('#historyDokumenModal')
            //         modalHistory.find('table tbody').html(resp.tableBody)
            //         modalHistory.modal('show')
            //     }).fail(function(err) {
            //         Swal.fire({
            //             icon: 'error',
            //             title: 'Something Wrong!',
            //             text: 'Contact Administrator'
            //         })
            //     })
            // })

            $(document).on('click', '.btn-history', function() {
                let id = $(this).data('id')
                dokumenHistoryId = id
                console.log(id)
                let modalHistory = $('#historyDokumenModal')
                // modalHistory.find('table tbody').html(resp.tableBody)
                modalHistory.modal('show')
                tableDokumenHistory.draw()
            })

            $(document).on('click', '.btn-edit', function(){
                let data = tableUser.row($(this).parents('tr')).data()
                let modalEdit = $('#editDokumenModal')
                modalEdit.find('input[name="id"]').val(data.id)
                modalEdit.find('select[name="jenis_file"]').selectpicker('val', data.jenis_file_kode)
                modalEdit.find('input[name="nomor"]').val(data.nomor)
                modalEdit.find('input[name="nomor"]').trigger('keyup')
                modalEdit.find('input[name="judul"]').val(data.judul)
                modalEdit.find('input[name="tanggal"]').val(data.tanggal)
                modalEdit.find('select[name="bagian"]').selectpicker('val', data.bagian)
                modalEdit.find('select[name="level[]"]').selectpicker('val', data.level.split(','))
                modalEdit.find('select[name="status_dokumen"]').selectpicker('val', data.status_dokumen_id)
                modalEdit.find('select[name=status_dokumen]').trigger('change')
                if(data.d_prev){
                    modalEdit.find('.perubahan-container .bs-searchbox input[type=search]').val(data.d_prev.nomor)
                    modalEdit.find('.perubahan-container .bs-searchbox input[type=search]').trigger('keyup')
                    modalEdit.find('select[name="dokumen_berubah"]').selectpicker('val', data.d_prev.id)
                }
                modalEdit.modal('show')
            })

            $(document).on('click', '.btn-download', function(){
                let id = $(this).data('id')
                $('#downloadId').val(id)
                window.open('{{ route('operator.downloadDokumen') }}', 'download', 'width=500,height=700')
                $('#downloadForm').submit()
            })

            $(document).on('click', '.btn-history-perubahan', function(){
                let id = $(this).data('id')
                dokumenPerubahanHistoryId = id
                modalPerubahanHistory.modal('show')
                tablePerubahanDokumenHistory.draw()
            })
        })
    </script>
@endsection

@extends('user.template')

@section('css_libraries')
    {{-- <link rel="stylesheet" href="{{ asset('selectize.js/dist/css/selectize.bootstrap4.css') }}"> --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.13.1/datatables.min.css" />
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
                    {{-- <button class="btn btn-primary mb-0" id="btnTambahDokumen">
                        <span>Tambah</span>
                    </button> --}}
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-items-center w-100" id="table-user">
                        <thead>
                            <tr>
                                <th>
                                    No</th>
                                <th>
                                    Jenis</th>
                                <th>
                                    Nomor</th>
                                <th>
                                    Judul</th>
                                <th>
                                    Tanggal</th>
                                <th>
                                    Bagian</th>
                                <th>Level</th>
                                <th>Status</th>
                                <th>
                                    Dokumen</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
    <form id="tampilForm" method="post"  enctype="multipart/form-data" action="{{ route('user.dokumen.tampil') }}" target="result">
        @csrf
        <input type="hidden" name="id" id="tampilId">
        {{-- <button type="button" id="tampilButton">Send</button> --}}
    </form>
    <form action="{{ route('user.downloadDokumen') }}" method="POST" id="downloadForm" target="download">
        @csrf
        <input type="hidden" name="id" id="downloadId">

    </form>
@endsection

@section('modals')
    {{-- <div class="modal fade" id="tambahModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" >Tambah Dokumen</h5>
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
                                    <input type="text" class="form-control" id="nomor" name="nomor" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="basic-url" class="form-label">Judul</label>
                                    <input type="text" class="form-control" id="judul" name="judul" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="jabatan" class="form-label">Tanggal</label>
                                    <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="basic-url" class="form-label">Bagian / Unit Usaha</label>
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
                                    <select name="level[]" id="level" class="form-control" data-style="p-0" multiple
                                        required>
                                        <option value="">-- Pilih Level Akses --</option>
                                        @foreach ($master_user_level as $item)
                                            <option value="{{ $item->level }}" selected>{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group mb-3">
                                    <label for="basic-url" class="form-label">File PDF</label>
                                    <input type="file" class="form-control" id="dokumen" name="dokumen"
                                        accept="application/pdf" required>
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
    </div> --}}
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
    <script>
        function isNumber(evt) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            }

            return true;
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


            $('#table-user').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('user.dokumen.getDataDokumen') }}",
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
                            return `<span class="badge badge-${data.d_status.class_color} btn-history-perubahan" data-id="${data.id}" style="cursor:pointer">${data.d_status.alias}</span>`
                        }
                    },
                    {
                        data: null,
                        name: null,
                        render: function(data) {
                            @php
                                $renderAksi = '`';
                                foreach($master_jenis_aksi as $jenis_aksi){
                                    $renderAksi .= '<button data-id="${data.id}" class="btn btn-sm '. $jenis_aksi->button_color_class.' '. $jenis_aksi->button_class .'"><i class="'.$jenis_aksi->button_icon.'"></i></button>';
                                }
                                $renderAksi .= '`'
                            @endphp
                            return {!! $renderAksi !!}
                            // {{--  return `
                            // <button data-id="${data.id}" class="btn btn-sm btn-primary btn-tampil">
                            //     <i class="fas fa-eye"></i>
                            // </button>
                            // ` --}}
                        }
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
                    url: "{{ route('user.dokumen.getPerubahanDokumenHistory') }}",
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

            // $('#btnTambahDokumen').click(function() {
            //     $('#tambahModal').modal('show')
            // })

            $('input[data-type=number]').keypress(function(e) {
                return isNumber(e)
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

            $(document).on('click', '.btn-tampil', function(){
                $('#tampilId').val($(this).data('id'))
                window.open('{{ route('user.dokumen.tampil') }}', 'result', 'width=500,height=700')
                $('#tampilForm').submit()
            })

            @if ($master_jenis_aksi->pluck('nama')->contains('Download'))
                $(document).on('click', '.btn-download', function(){
                    let id = $(this).data('id')
                    $('#downloadId').val(id)
                    window.open('{{ route('user.downloadDokumen') }}', 'download', 'width=500,height=700')
                    $('#downloadForm').submit()
                })
            @endif

            $(document).on('click', '.btn-history-perubahan', function(){
                let id = $(this).data('id')
                dokumenPerubahanHistoryId = id
                modalPerubahanHistory.modal('show')
                tablePerubahanDokumenHistory.draw()
            })
        })
    </script>
@endsection

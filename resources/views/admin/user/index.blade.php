@extends('admin.template')

@section('css_libraries')
    {{-- <link rel="stylesheet" href="{{ asset('selectize.js/dist/css/selectize.bootstrap4.css') }}"> --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.13.1/datatables.min.css" />
@endsection

@section('title')
    EDC | PTPN VI
@endsection

@section('page-name')
    Users
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Users</h1>
        </div>
        <div class="card">
            <div class="card-header pb-0 justify-content-between">
                <h5>Daftar User</h5>
                <button class="btn btn-primary mb-0" id="btnTambahUser">
                    <span>Tambah</span>
                </button>
            </div>
            <div class="card-body p-3 pb-2">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-items-center w100" id="table-user">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>NIK</th>
                                <th>No HP</th>
                                <th>Bagian / Instansi</th>
                                <th>Jabatan</th>
                                <th>User Level</th>
                                <th>Aksi</th>
                                <th>Aktif Dari</th>
                                <th>Aktif Sampai</th>
                                <th>Status Aktif</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('modals')
<div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.user.simpan') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <label for="NIK" class="form-label">NIK</label>
                            <div class="input-group mb-3">
                                {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                <input type="text" class="form-control" id="nik" name="nik" data-type="number"
                                     required>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label for="basic-url" class="form-label">Nama Lengkap</label>
                            <div class="input-group mb-3">
                                {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                <input type="text" class="form-control" id="nama_lengkap" name="nama" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <label for="jabatan" class="form-label">Jabatan</label>
                            <div class="input-group mb-3">
                                {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                <input type="text" class="form-control" id="jabatan" name="jabatan" required>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label for="basic-url" class="form-label">No HP</label>
                            <div class="input-group mb-3">
                                {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                <input type="text" class="form-control" id="nohp" name="nohp"
                                    data-type="number" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12">
                            <div class="bagianContainer" data-tipe="tambah">
                                <label for="basic-url" class="form-label">Bagian / Unit Usaha</label>
                                <div class="input-group mb-3">
                                    {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                    <select name="bagian" id="bagian" class="form-control" data-style="p-0">
                                        <option value="">-- Pilih Bagian --</option>
                                        @foreach ($master_bagian as $item)
                                            <option value="{{ $item->kode_bagian }}">{{ $item->nama_bagian }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div data-tipe="tambah" class="d-none instansiContainer">
                                <label for="basic-url" class="form-label">Instansi</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" name="instansi" id="instansi">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <label for="basic-url" class="form-label">Role Akun</label>
                            <div class="input-group mb-3">
                                {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                <select name="role" id="role" class="form-control" data-style="p-0" data-tipe="tambah" required>
                                    <option value="">-- Pilih Role --</option>
                                    @foreach ($master_user_role as $item)
                                        <option value="{{ $item->kode }}"><span
                                                class="text-capitalize">{{ $item->nama }}</span> |
                                            {{ $item->keterangan }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label for="basic-url" class="form-label">Level Akses User</label>
                            <div class="input-group mb-3">
                                {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                <select name="level" id="level" class="form-control" data-style="p-0">
                                    <option value="">-- Pilih Level Akses --</option>
                                    @foreach ($master_user_level as $item)
                                        <option value="{{ $item->level }}">{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <label for="basic-url" class="form-label">Dokumen Yang Dapat Diakses</label>
                            <div class="input-group mb-3">
                                {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                <select name="jenis_file[]" id="jenis_file" class="form-control" data-style="p-0" required multiple>
                                    <option value="">-- Pilih Jenis File --</option>
                                    @foreach ($master_jenis_file as $item)
                                        <option value="{{ $item->kode }}">{{ $item->kepanjangan }}
                                            ({{ $item->singkatan }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label for="basic-url" class="form-label">Jenis Aksi</label>
                            <div class="input-group mb-3">
                                <select name="jenis_aksi[]" id="jenis_aksi" class="form-control" data-style="p-0" required multiple>
                                    <option value="">-- Pilih Jenis Aksi --</option>
                                    @foreach ($master_jenis_aksi as $item)
                                        <option value="{{ $item->id }}" selected>{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <label for="NIK" class="form-label">Aktif Dari</label>
                            <div class="input-group mb-3">
                                {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                <input type="date" class="form-control" id="active_from" name="active_from"
                                    data-type="number" required>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label for="basic-url" class="form-label">Aksif Sampai</label>
                            <div class="input-group mb-3">
                                {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                <input type="date" class="form-control" id="active_to" name="active_to" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="d-none kepentinganContainer" data-tipe="tambah">
                                <label for="basic-url" class="form-label">Kepentingan</label>
                                <div class="input-group mb-3">
                                    {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                    <input type="text" name="kepentingan" id="" class="form-control">
                                </div>
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
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.user.edit') }}" method="POST">
                @csrf
                <input type="hidden" name="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <label for="NIK" class="form-label">NIK</label>
                            <div class="input-group mb-3">
                                {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                <input type="text" class="form-control" id="nik" name="nik" data-type="number"
                                     required disabled>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label for="basic-url" class="form-label">Nama Lengkap</label>
                            <div class="input-group mb-3">
                                {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                <input type="text" class="form-control" id="nama_lengkap" name="nama" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <label for="jabatan" class="form-label">Jabatan</label>
                            <div class="input-group mb-3">
                                {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                <input type="text" class="form-control" id="jabatan" name="jabatan" required>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label for="basic-url" class="form-label">No HP</label>
                            <div class="input-group mb-3">
                                {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                <input type="text" class="form-control" id="nohp" name="nohp"
                                    data-type="number" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12">
                            <div class="bagianContainer" data-tipe="edit">
                                <label for="basic-url" class="form-label">Bagian / Unit Usaha</label>
                                <div class="input-group mb-3">
                                    {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                    <select name="bagian" id="bagian" class="form-control" data-style="p-0">
                                        <option value="">-- Pilih Bagian --</option>
                                        @foreach ($master_bagian as $item)
                                            <option value="{{ $item->kode_bagian }}">{{ $item->nama_bagian }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div data-tipe="edit" class="d-none instansiContainer">
                                <label for="basic-url" class="form-label">Instansi</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" name="instansi" id="instansi">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <label for="basic-url" class="form-label">Role Akun</label>
                            <div class="input-group mb-3">
                                {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                <select name="role" id="role" class="form-control" data-style="p-0" data-tipe="edit" required>
                                    <option value="">-- Pilih Role --</option>
                                    @foreach ($master_user_role as $item)
                                        <option value="{{ $item->kode }}"><span
                                                class="text-capitalize">{{ $item->nama }}</span> |
                                            {{ $item->keterangan }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label for="basic-url" class="form-label">Level Akses User</label>
                            <div class="input-group mb-3">
                                {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                <select name="level" id="level" class="form-control" data-style="p-0">
                                    <option value="">-- Pilih Level Akses --</option>
                                    @foreach ($master_user_level as $item)
                                        <option value="{{ $item->level }}">{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <label for="basic-url" class="form-label">Dokumen Yang Dapat Diakses</label>
                            <div class="input-group mb-3">
                                {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                <select name="jenis_file[]" id="jenis_file" class="form-control" data-style="p-0" multiple>
                                    <option value="">-- Pilih Jenis File --</option>
                                    @foreach ($master_jenis_file as $item)
                                        <option value="{{ $item->kode }}">{{ $item->kepanjangan }}
                                            ({{ $item->singkatan }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label for="basic-url" class="form-label">Jenis Aksi</label>
                            <div class="input-group mb-3">
                                <select name="jenis_aksi[]" id="jenis_aksi" class="form-control" data-style="p-0" required multiple>
                                    <option value="">-- Pilih Jenis Aksi --</option>
                                    @foreach ($master_jenis_aksi as $item)
                                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <label for="NIK" class="form-label">Aktif Dari</label>
                            <div class="input-group mb-3">
                                {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                <input type="date" class="form-control" id="active_from" name="active_from"
                                    data-type="number" required>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label for="basic-url" class="form-label">Aksif Sampai</label>
                            <div class="input-group mb-3">
                                {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                <input type="date" class="form-control" id="active_to" name="active_to" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="d-none kepentinganContainer" data-tipe="edit">
                                <label for="basic-url" class="form-label">Kepentingan</label>
                                <div class="input-group mb-3">
                                    {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                    <input type="text" name="kepentingan" id="" class="form-control">
                                </div>
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

<!-- Modal -->
<div class="modal fade" id="modalResetPassword" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
            <form action="{{ route('admin.user.reset_password') }}" method="post">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id">
                    <p>Password yang direset akan berubah menjadi NIK user saat ini</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Reset</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modalStatusActive" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Status Active</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
            <form action="{{ route('admin.user.active_user') }}" method="post">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Aktifkan / Nonaktifkan</button>
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
                                <th>Nomor Dokumen</th>
                                <th>Judul</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
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
            console.log(charCode)
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            }
            return true;
        }

        function toggleDisplayNone(elementSelector){
            if(elementSelector.hasClass('d-none')){
                console.log('punya d-none')
                elementSelector.removeClass('d-none')
            } else {
                console.log('gapunya dnone')
                elementSelector.addClass('d-none')
            }
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
                    url: "{{ route('admin.user.getDataUser') }}",
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
                        data: 'name',
                        name: 'name',
                    },
                    {
                        data: 'nik',
                        name: 'nik',
                    },
                    {
                        data: null,
                        name: null,
                        render: function(data){
                            if(data.u_biasa_detail){
                                return `${data.u_biasa_detail.no_hp ?? '-'}`
                            }
                            else if(data.u_tamu_detail){
                                return `${data.u_tamu_detail.no_hp ?? '-'}`
                            }
                            return `-`
                        }
                    },
                    {
                        data: null,
                        name: null,
                        render: function(data){
                            if(data.u_biasa_detail){
                                return `${data.u_biasa_detail.bagian ?? '-'}`
                            }
                            else if(data.u_tamu_detail){
                                return `${data.u_tamu_detail.instansi ?? '-'}`
                            }
                            return `-`
                        }
                    },
                    {
                        data: null,
                        name: null,
                        render: function(data){
                            if(data.u_biasa_detail){
                                return `${data.u_biasa_detail.jabatan ?? '-'}`
                            }
                            else if(data.u_tamu_detail){
                                return `${data.u_tamu_detail.jabatan ?? '-'}`
                            }
                            return `-`
                        }
                    },
                    {
                        data: 'level.nama',
                        name: 'level.nama',
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        render: function(data){
                            return data.toString()
                        }
                    },
                    {
                        data: 'active_from',
                        name: 'active_from',
                    },
                    {
                        data: 'active_to',
                        name: 'active_to',
                    },
                    {
                        data: null,
                        name: 'active_status',
                        render: function(data){
                            if(data.active_status == 0 && data.role != 'adm'){
                                return `<span class="badge badge-danger btn-status-active" style="cursor: pointer" data-tipe="nonaktif">Nonaktif</span>`
                            }
                            return `<span class="badge badge-primary btn-status-active" style="cursor: pointer" data-tipe="aktif">Aktif</span>`
                        }
                    },
                    {
                        data: 'u_role.nama',
                        name: 'u_role.nama',
                    },
                    {
                        data: null,
                        name: null,
                        className: 'text-nowrap',
                        render: function(data){
                            return `
                                <button class="btn btn-sm btn-primary btn-reset-password">
                                    <i class="fas fa-key"></i>
                                </button>
                                <button class="btn btn-sm btn-info btn-edit-user">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button data-id="${data.id}" class="btn btn-sm btn-warning btn-history-access-document">
                                    <i class="fas fa-history"></i>
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
                    url: "{{ route('admin.user.getDataHistoryDokumen') }}",
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
                        data: 'dokumen.nomor',
                        name: 'dokumen.nomor',
                    },
                    {
                        data: 'dokumen.judul',
                        name: 'dokumen.judul',
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

            $('#btnTambahUser').click(function() {
                $('#tambahModal').modal('show')
            })

            $('input[data-type=number]').keypress(function(e) {
                return isNumber(e)
            })

            let tempRole
            $('select[name="role"]').change(function(){
                let value = $(this).val()
                let dataTipe = $(this).data('tipe')
                console.log(value, dataTipe)
                if(value == 'tmu'){
                    toggleDisplayNone($(`.bagianContainer[data-tipe="${dataTipe}"]`))
                    toggleDisplayNone($(`.instansiContainer[data-tipe="${dataTipe}"]`))
                    toggleDisplayNone($(`.kepentinganContainer[data-tipe="${dataTipe}"]`))
                    tempRole = value
                } else if(tempRole == 'tmu' && value != 'tmu'){
                    toggleDisplayNone($(`.bagianContainer[data-tipe="${dataTipe}"]`))
                    toggleDisplayNone($(`.instansiContainer[data-tipe="${dataTipe}"]`))
                    toggleDisplayNone($(`.kepentinganContainer[data-tipe="${dataTipe}"]`))
                    tempRole = value
                }
                console.log($(`.bagianContainer[data-tipe="${dataTipe}"]`), $(`.instansiContainer[data-tipe="${dataTipe}"]`))
                return
            })

            $(document).on('click', '.btn-reset-password', function(){
                let dataRow = tableUser.row($(this).parents('tr')).data()
                let modalResetPassword = $('#modalResetPassword')
                modalResetPassword.find('.modal-title').html(`Reset Password ${dataRow.name}`)
                modalResetPassword.find('input[name="id"]').val(dataRow.id)
                modalResetPassword.modal('show')
            })

            $(document).on('click', '.btn-status-active', function(){
                let dataRow = tableUser.row($(this).parents('tr')).data()
                let modalStatusActive = $('#modalStatusActive')
                let tipe = $(this).data('tipe')
                console.log(tipe)
                if(tipe == 'nonaktif'){
                    modalStatusActive.find('.modal-title').html(`Aktifkan akun ${dataRow.name}`)
                    modalStatusActive.find('.modal-footer button[type="submit"]').html('Aktifkan')
                    modalStatusActive.find('input[name="id"]').val(dataRow.id)
                    modalStatusActive.modal('show')
                }
                else if(tipe == 'aktif'){
                    modalStatusActive.find('.modal-title').html(`Nonaktifkan akun ${dataRow.name}`)
                    modalStatusActive.find('.modal-footer button[type="submit"]').html('Nonaktifkan')
                    modalStatusActive.find('.modal-footer button[type="submit"]').html('Nonaktifkan')
                    modalStatusActive.find('input[name="id"]').val(dataRow.id)
                    modalStatusActive.modal('show')
                }
            })



            $(document).on('click', '.btn-edit-user', function(){
                let dataRow = tableUser.row($(this).parents('tr')).data()
                let editUserModal = $('#editUserModal')
                editUserModal.find('input[name="id"]').val(dataRow.id)
                editUserModal.find('input[name="nik"]').val(dataRow.nik)
                editUserModal.find('input[name="nama"]').val(dataRow.name)
                editUserModal.find('select[name="role"]').selectpicker('val', dataRow.role)
                editUserModal.find('select[name="role"]').trigger('change')
                if(dataRow.u_biasa_detail){
                    editUserModal.find('input[name="jabatan"]').val(dataRow.u_biasa_detail.jabatan)
                    editUserModal.find('input[name="nohp"]').val(dataRow.u_biasa_detail.no_hp)
                    editUserModal.find('select[name="bagian"]').selectpicker('val', dataRow.u_biasa_detail.bagian)
                }
                else if(dataRow.u_tamu_detail){
                    editUserModal.find('input[name="jabatan"]').val(dataRow.u_tamu_detail.jabatan)
                    editUserModal.find('input[name="nohp"]').val(dataRow.u_tamu_detail.no_hp)
                    editUserModal.find('input[name="instansi"]').val(dataRow.u_tamu_detail.instansi)
                    editUserModal.find('input[name="kepentingan"]').val(dataRow.u_tamu_detail.kepentingan)
                }
                editUserModal.find('select[name="level"]').selectpicker('val', dataRow.level.level)
                editUserModal.find('select[name="jenis_file[]"]').selectpicker('val', dataRow.jenis_file ? dataRow.jenis_file.split(',') : null)
                editUserModal.find('select[name="jenis_aksi[]"]').selectpicker('val', dataRow.jenis_aksi ? dataRow.jenis_aksi.split(',') : null)
                editUserModal.find('input[name="active_from"]').val(dataRow.active_from)
                editUserModal.find('input[name="active_to"]').val(dataRow.active_to)
                editUserModal.modal('show')
            })

            $(document).on('click', '.btn-history-access-document', function() {
                let id = $(this).data('id')
                dokumenHistoryId = id
                console.log(id)
                let modalHistory = $('#historyDokumenModal')
                // modalHistory.find('table tbody').html(resp.tableBody)
                modalHistory.modal('show')
                tableDokumenHistory.draw()
            })
        })
    </script>
@endsection

@extends('admin.template')

@section('header')
    <link rel="stylesheet" href="{{ asset('selectize.js/dist/css/selectize.bootstrap5.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.13.1/datatables.min.css" />
@endsection

@section('pages')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">User</li>
        </ol>
        <h6 class="font-weight-bolder mb-0">User</h6>
    </nav>
@endsection

@section('content')
    <div class="card">
        <div class="card-header pb-0">
            <div class="row">
                <div class="col-lg-6 col-7">
                    <h5>Daftar User</h5>
                    <p class="text-sm mb-0">
                        <button class="btn btn-primary mb-0" id="btnTambahUser">
                            <span>Tambah</span>
                        </button>
                    </p>
                </div>
                <div class="col-lg-6 col-5 my-auto text-end">
                    {{-- <div class="dropdown float-lg-end pe-4">
                        <a class="cursor-pointer" id="dropdownTable" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa fa-ellipsis-v text-secondary"></i>
                        </a>
                        <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5" aria-labelledby="dropdownTable">
                            <li><a class="dropdown-item border-radius-md" href="javascript:;">Action</a></li>
                            <li><a class="dropdown-item border-radius-md" href="javascript:;">Another action</a>
                            </li>
                            <li><a class="dropdown-item border-radius-md" href="javascript:;">Something else
                                    here</a></li>
                        </ul>
                    </div> --}}
                </div>
            </div>
        </div>
        <div class="card-body p-3 pb-2">
            <div class="table-responsive">
                <table class="table table-sm table-bordered align-items-center w-100" id="table-user">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xs font-weight-bolder">
                                No</th>
                            <th class="text-uppercase text-secondary text-xs font-weight-bolder ">
                                Nama</th>
                            <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder ">
                                NIK</th>
                            <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder ">
                                No HP</th>
                            <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder ">
                                Bagian / Instansi</th>
                            <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder ">
                                Jabatan</th>
                            <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder ">
                                User Level</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('modals')
    <div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Tambah User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.user.simpan') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <label for="NIK" class="form-label">NIK</label>
                                <div class="input-group mb-3">
                                    {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                    <input type="text" class="form-control" id="nik" name="nik"
                                        data-type="number" required>
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
                            <div class="col-sm-12 col-md-6">
                                <label for="basic-url" class="form-label">Role Akun</label>
                                <div class="input-group mb-3">
                                    {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                    <select name="role" id="role" class="form-control">
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
                                <div id="bagianContainer">
                                    <label for="basic-url" class="form-label">Bagian / Unit Usaha</label>
                                    <div class="input-group mb-3">
                                        {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                        <select name="bagian" id="bagian" class="form-control">
                                            <option value="">-- Pilih Bagian --</option>
                                            @foreach ($master_bagian as $item)
                                                <option value="{{ $item->kode_bagian }}">{{ $item->nama_bagian }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div id="instansiContainer" class="d-none">
                                    <label for="basic-url" class="form-label">Instansi</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" name="instansi" id="instansi">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <label for="basic-url" class="form-label">Level Akses User</label>
                                <div class="input-group mb-3">
                                    {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                    <select name="level" id="level" class="form-control">
                                        <option value="">-- Pilih Level Akses --</option>
                                        @foreach ($master_user_level as $item)
                                            <option value="{{ $item->level }}">{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <label for="basic-url" class="form-label">Dokumen Yang Dapat Diakses</label>
                                <div class="input-group mb-3">
                                    {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                    <select name="jenis_file[]" id="jenis_file" class="form-control" multiple>
                                        <option value="">-- Pilih Jenis File --</option>
                                        @foreach ($master_jenis_file as $item)
                                            <option value="{{ $item->kode }}">{{ $item->kepanjangan }}
                                                ({{ $item->singkatan }})</option>
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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('selectize.js/dist/js/selectize.min.js') }}"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.13.1/datatables.min.js"></script>
    <script>
        function isNumber(evt) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            }

            return true;
        }

        function toggleDisplayNone(elementSelector){
            if(elementSelector.hasClass('d-none')){
                elementSelector.removeClass('d-none')
            } else {
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

            $('#table-user').DataTable({
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
                        data: 'nohp',
                        name: 'nohp',
                    },
                    {
                        data: null,
                        name: null,
                        render: function(data){
                            return ``
                        }
                    },
                    {
                        data: 'jabatan',
                        name: 'jabatan',
                    },
                    {
                        data: 'level.nama',
                        name: 'level.nama',
                    },
                ],
            })

            $('select.form-control').selectize()

            var tambahModal = new bootstrap.Modal(document.getElementById('tambahModal'), {
                backdrop: true,
            })

            $('#btnTambahUser').click(function() {
                tambahModal.show()
            })

            $('input[data-type=number]').keypress(function(e) {
                return isNumber(e)
            })

            let tempRole
            $('#role').change(function(){
                let value = $(this).val()
                if(value == 'tmu'){
                    toggleDisplayNone($('#bagianContainer'))
                    toggleDisplayNone($('#instansiContainer'))
                    tempRole = value
                } else if(tempRole == 'tmu' && value != 'tmu'){
                    toggleDisplayNone($('#bagianContainer'))
                    toggleDisplayNone($('#instansiContainer'))
                    tempRole = value
                }
                return
            })
        })
    </script>
@endsection

@extends('admin.template')

@section('header')
    <link rel="stylesheet" href="{{ asset('selectize.js/dist/css/selectize.bootstrap5.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.13.1/datatables.min.css" />
@endsection

@section('pages')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Dokumen</li>
        </ol>
        <h6 class="font-weight-bolder mb-0">Dokumen</h6>
    </nav>
@endsection

@section('content')
    <div class="card">
        <div class="card-header pb-0">
            <div class="row">
                <div class="col-lg-6 col-7">
                    <h5>Daftar Dokumen</h5>
                    <p class="text-sm mb-0">
                        <button class="btn btn-primary mb-0" id="btnTambahDokumen">
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
                                Jenis</th>
                            <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder ">
                                Nomor</th>
                            <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder ">
                                Judul</th>
                            <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder ">
                                Tanggal</th>
                            <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder ">
                                Bagian</th>
                            <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder ">
                                Level</th>
                            <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder ">
                                Dokumen</th>
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
    <div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
        style="z-index: 1060">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Tambah Dokumen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.dokumen.simpan') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <label for="basic-url" class="form-label">Jenis Dokumen</label>
                                <div class="input-group mb-3">
                                    {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                    <select name="jenis_file" id="jenis_file" class="form-control" required>
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
                                <label for="basic-url" class="form-label">Nomor</label>
                                <div class="input-group mb-3">
                                    {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                    <input type="text" class="form-control" id="nomor" name="nomor" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="basic-url" class="form-label">Judul</label>
                                <div class="input-group mb-3">
                                    {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                    <input type="text" class="form-control" id="judul" name="judul" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                    <label for="jabatan" class="form-label">Tanggal</label>
                                    <div class="input-group mb-3">
                                        {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                        <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                                    </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="basic-url" class="form-label">Bagian / Unit Usaha</label>
                                <div class="input-group mb-3">
                                    {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                    <select name="bagian" id="bagian" class="form-control" required>
                                        <option value="">-- Pilih Bagian --</option>
                                        @foreach ($master_bagian as $item)
                                            <option value="{{ $item->kode_bagian }}">{{ $item->nama_bagian }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="basic-url" class="form-label">Level Akses User</label>
                                <div class="input-group mb-3">
                                    {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                    <select name="level[]" id="level" class="form-control" multiple required>
                                        <option value="">-- Pilih Level Akses --</option>
                                        @foreach ($master_user_level as $item)
                                            <option value="{{ $item->level }}" selected>{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <label for="basic-url" class="form-label">File PDF</label>
                                <div class="input-group mb-3">
                                    {{-- <span class="input-group-text" id="basic-addon3">https://example.com/users/</span> --}}
                                    <input type="file" class="form-control" id="dokumen" name="dokumen" accept="application/pdf" required>
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
                        data: 'dokumen',
                        name: 'dokumen',
                        render: function(data){
                            return `<a href="/storage/${data}">Lihat File</a>`
                        }
                    },
                ],
            })

            $('select.form-control').selectize()

            var tambahModal = new bootstrap.Modal(document.getElementById('tambahModal'), {
                backdrop: true,
            })

            $('#btnTambahDokumen').click(function() {
                tambahModal.show()
            })

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
        })
    </script>
@endsection

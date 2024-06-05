@extends('tamu.template')

@section('css_libraries')
    {{-- <link rel="stylesheet" href="{{ asset('selectize.js/dist/css/selectize.bootstrap4.css') }}"> --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.13.1/datatables.min.css" />
@endsection

@section('title')
    EDC | PTPN VI
@endsection

@section('page-name')
    Profile
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Profile</h1>
        </div>
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4>User Profile</h4>
                    </div>
                    <div class="card-body p-3 pb-2">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <th>NIK</th>
                                    <td> : {{ auth()->user()->nik }}</td>
                                </tr>
                                <tr>
                                    <th>Nama</th>
                                    <td> : {{ auth()->user()->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td> : {{ auth()->user()->email }}</td>
                                </tr>
                                @if (auth()->user()->role == 'tmu')
                                    <tr>
                                        <th>No HP</th>
                                        <td> : {{ auth()->user()->uTamuDetail->no_hp }}</td>
                                    </tr>
                                    <tr>
                                        <th>Instansi</th>
                                        <td> : {{ auth()->user()->uTamuDetail->instansi }}</td>
                                    </tr>
                                    <tr>
                                        <th>Jabatan</th>
                                        <td> : {{ auth()->user()->uTamuDetail->jabatan }}</td>
                                    </tr>
                                @elseif(auth()->user()->role == 'op' || auth()->user()->role == 'usr')
                                    {{-- @dd(auth()->user()) --}}
                                    <tr>
                                        <th>No HP</th>
                                        <td> : {{ auth()->user()->uBiasaDetail->no_hp }}</td>
                                    </tr>
                                    <tr>
                                        <th>Bagian</th>
                                        <td> : {{ auth()->user()->uBiasaDetail->bagian }}</td>
                                    </tr>
                                    <tr>
                                        <th>Jabatan</th>
                                        <td> : {{ auth()->user()->uBiasaDetail->jabatan }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>Level</th>
                                    <td> : {{ auth()->user()->level->nama }}</td>
                                </tr>
                                <tr>
                                    <th>Izin Dokumen</th>
                                    <td> : {{ $user_jenis_file }}</td>
                                </tr>
                                <tr>
                                    <th>Izin Aksi</th>
                                    <td> : {{ $user_jenis_aksi }}</td>
                                </tr>
                                <tr>
                                    <th>Aktif Dari</th>
                                    <td> : {{ \Carbon\Carbon::parse(auth()->user()->active_from)->toDateString() }}</td>
                                </tr>
                                <tr>
                                    <th>Aktif Sampai</th>
                                    <td> : {{ \Carbon\Carbon::parse(auth()->user()->active_to)->toDateString() }}</td>
                                </tr>
                                @if (auth()->user()->role == 'tmu')
                                <tr>
                                    <th>Kepentingan</th>
                                    <td> : {{ auth()->user()->uTamuDetail->kepentingan }}</td>
                                </tr>

                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Ganti Password</h4>
                    </div>
                    <div class="card-body p-3 pb-2">
                        <form action="{{ route('tamu.profile.edit') }}" method="POST">
                            @csrf
                            <div class="form-group">
                              <label for="">Password Baru</label>
                              <input type="text" name="password" id="password" class="form-control" placeholder="" minlength="8" maxlength="15">
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary">Edit</button>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4>Ganti Email</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('tamu.profile.edit-email') }}" method="POST">
                            @csrf
                            <div class="form-group">
                              <label for="">Email</label>
                              <input type="email" name="email" id="email" class="form-control" placeholder="" value="{{ auth()->user()->email }}" minlength="8" required>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary">Edit</button>
                        </form>
                    </div>
                </div>
                {{-- <div class="card">
                    <div class="card-header">
                        <h4>Ganti No HP</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('tamu.profile.edit-nohp') }}" method="POST">
                            @csrf
                            @php
                                $no_hp = !empty(auth()->user()->uTamuDetail)  ? auth()->user()->uTamuDetail->no_hp : '';
                            @endphp
                            <div class="form-group">
                              <label for="">No HP</label>
                              <input type="text" name="nohp" id="nohp" class="form-control" data-type="number" placeholder="" value="{{ $no_hp }}" minlength="8" maxlength="13" required>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary">Edit</button>
                        </form>
                    </div>
                </div> --}}
            </div>
        </div>
    </section>
@endsection

@section('modals')
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

        function toggleDisplayNone(elementSelector) {
            if (elementSelector.hasClass('d-none')) {
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
                        render: function(data) {
                            if (data.u_biasa_detail) {
                                return `${data.u_biasa_detail.no_hp ?? '-'}`
                            } else if (data.u_tamu_detail) {
                                return `${data.u_tamu_detail.no_hp ?? '-'}`
                            }
                            return `-`
                        }
                    },
                    {
                        data: null,
                        name: null,
                        render: function(data) {
                            if (data.u_biasa_detail) {
                                return `${data.u_biasa_detail.bagian ?? '-'}`
                            } else if (data.u_tamu_detail) {
                                return `${data.u_tamu_detail.instansi ?? '-'}`
                            }
                            return `-`
                        }
                    },
                    {
                        data: null,
                        name: null,
                        render: function(data) {
                            if (data.u_biasa_detail) {
                                return `${data.u_biasa_detail.jabatan ?? '-'}`
                            } else if (data.u_tamu_detail) {
                                return `${data.u_tamu_detail.jabatan ?? '-'}`
                            }
                            return `-`
                        }
                    },
                    {
                        data: 'level.nama',
                        name: 'level.nama',
                    },
                ],
            })

            $('select.form-control').selectpicker()

            $('input[data-type=number]').keypress(function(e) {
                return isNumber(e)
            })

        })
    </script>
@endsection

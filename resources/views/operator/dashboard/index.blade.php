@extends('operator.template')

@section('css_libraries')
    {{-- <link rel="stylesheet" href="{{ asset('selectize.js/dist/css/selectize.bootstrap4.css') }}"> --}}
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css"> --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.13.1/datatables.min.css" />
@endsection

@section('title')
    EDC | PTPN VI
@endsection

@section('page-name')
    Dashboard
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Dashboard</h1>
        </div>
        <div class="row">
            @foreach ($count_per_jenis_file_category as $item)
                @if ($item->has_sub)
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <a href="{{ route('operator.dokumen.index_by_jenis', [$item->kode, $item->bagian->kode_bagian]) }}"
                            style="text-decoration:none">
                            <div class="card card-statistic-1">
                                <div class="card-icon bg-{{ $item->bg_color }}">
                                    <i class="{{ $item->fa_icon }}"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="card-header pt-3">
                                        <h4>{{ $item->kepanjangan }}</h4>
                                    </div>
                                    <div class="card-body">
                                        {{ $item->count }}
                                    </div>

                                </div>
                            </div>
                        </a>
                    </div>
                @else
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <a href="{{ route('operator.dokumen.index_by_jenis', [$item->kode]) }}" style="text-decoration:none">
                            <div class="card card-statistic-1">
                                <div class="card-icon bg-{{ $item->bg_color }}">
                                    <i class="{{ $item->fa_icon }}"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="card-header pt-3">
                                        <h4>{{ $item->kepanjangan }}</h4>
                                    </div>
                                    <div class="card-body">
                                        {{ $item->count }}
                                    </div>

                                </div>
                            </div>
                        </a>
                    </div>
                @endif
            @endforeach
        </div>
        <div class="row">
            @foreach ($count_per_jenis_file_category as $item)
                <div class="col-lg-6 col-md-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>{{ $item->kepanjangan }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="table-{{ $item->kode }}"
                                    class="table table-sm table-bordered align-items-center w-100" id="table-user">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nomor</th>
                                            <th>Judul</th>
                                            <th>Tanggal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    <form id="tampilForm" method="post" enctype="multipart/form-data" action="{{ route('operator.dokumen.tampil') }}"
        target="result">
        @csrf
        <input type="hidden" name="id" id="tampilId">
        {{-- <button type="button" id="tampilButton">Send</button> --}}
    </form>
    <form action="{{ route('operator.downloadDokumen') }}" method="POST" id="downloadForm" target="download">
        @csrf
        <input type="hidden" name="id" id="downloadId">

    </form>
@endsection

@section('scripts')
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.13.1/datatables.min.js"></script>
    <script>
        $(document).ready(function() {
            @foreach ($count_per_jenis_file_category as $item)
                $('#table-{{ $item->kode }}').DataTable({
                    dom: 'rtip',
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('operator.getDataDokumenByJenis') }}",
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            jenis_file: "{{ $item->kode }}"
                        }
                    },
                    lengthMenu: [
                        [5],
                        ['5']
                    ],
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
                            data: null,
                            name: null,
                            className: 'text-nowrap',
                            render: function(data) {
                                @php
                                    $renderAksi = '`';
                                    foreach($item->jenis_aksi as $jenis_aksi){
                                        $renderAksi .= '<button data-id="${data.id}" class="btn btn-sm '. $jenis_aksi->button_color_class.' '. $jenis_aksi->button_class .'"><i class="'.$jenis_aksi->button_icon.'"></i></button>';
                                    }
                                    $renderAksi .= '`'
                                @endphp
                                // return `
                                // <button data-id="${data.id}" class="btn btn-sm btn-primary btn-tampil">
                                //     <i class="fas fa-eye"></i>
                                // </button>
                                // `
                                return {!! $renderAksi !!}
                            }
                        },
                    ],
                })
            @endforeach

            $(document).on('click', '.btn-tampil', function() {
                $('#tampilId').val($(this).data('id'))
                window.open('{{ route('operator.dokumen.tampil') }}', 'result', 'width=500,height=700')
                $('#tampilForm').submit()
            })

            @if ($master_jenis_aksi->pluck('nama')->contains('Download'))
                $(document).on('click', '.btn-download', function(){
                    let id = $(this).data('id')
                    $('#downloadId').val(id)
                    window.open('{{ route('operator.downloadDokumen') }}', 'download', 'width=500,height=700')
                    $('#downloadForm').submit()
                })
            @endif
        })
    </script>
@endsection

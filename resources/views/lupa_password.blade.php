<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Login &mdash; EDC</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="{{ asset('stisla-master') }}/node_modules/bootstrap-social/bootstrap-social.css">

  <!-- Template CSS -->
  <link rel="stylesheet" href="{{ asset('stisla-master') }}/assets/css/style.css">
  <link rel="stylesheet" href="{{ asset('stisla-master') }}/assets/css/components.css">

  <style>
    .love {
        display: inline-block;
        position: relative;
        top: .1.0em;
        font-size: 0.9em;
        color: #e74c3c;
        -webkit-transform: scale(.9);
        -moz-transform: scale(.9);
        transform: scale(.9);
        -webkit-animation: love .5s infinite linear alternate-reverse;
        -moz-animation: love .5s infinite linear alternate-reverse;
        animation: love .5s infinite linear alternate-reverse;
    }
    @-webkit-keyframes love {
        to {-webkit-transform: scale(1.4);}
    }
    @-moz-keyframes love {
        to {-moz-transform: scale(1.4);}
    }
    @keyframes love {
        to {transform: scale(1.2);}
    }

    .background-app{
        background-image: url("{{ asset('img/bg-edc.jpg') }}");
        background-repeat: no-repeat;
        background-size: cover;
    }
  </style>
</head>

<body class="background-app">
  <div id="app">
    <section class="section">
      <div class="container mt-5">
        <div class="row">
          <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
            <div class="login-brand">
                {{-- <h4>Electronic Enterprise Document Control  (e-EDC)</h4> --}}
              <img src="{{ asset('img/edc_logo_png.png') }}" alt="logo" width="100%" class="shadow-light">
            </div>

            <div class="card card-primary">
              <div class="card-header"><h4>Lupa Password</h4></div>

              <div class="card-body">
                <small class="text-primary">Silakan Konfirmasi NIK dan No HP</p>
                <form method="POST" action="{{ route('konfirmasi_lupa_password') }}" class="needs-validation" novalidate="">
                    @csrf
                  <div class="form-group">
                    <label for="nik">NIK</label>
                    <input id="nik" type="nik" class="form-control" name="nik" tabindex="1" maxlength="15" required autofocus>
                    <div class="invalid-feedback">
                      Silakan isi NIK
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="d-block">
                    	<label for="password" class="control-label">No HP</label>
                      {{-- <div class="float-right">
                        <a href="auth-forgot-password.html" class="text-small">
                          Forgot Password?
                        </a>
                      </div> --}}
                    </div>
                    <input id="nohp" type="text" class="form-control" name="nohp" tabindex="2" maxlength="15" required>
                    <div class="invalid-feedback">
                      Silakan isi No HP
                    </div>
                  </div>

                  {{-- <div class="form-group">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember-me">
                      <a href="">
                            Lupa Password
                      </a>
                    </div>
                  </div> --}}

                  <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                      Konfirmasi
                    </button>
                  </div>

                  {{-- <div class="d-flex justify-content-end">
                    <a href="{{ route('lupa_password') }}">Lupa Password</a>
                  </div> --}}
              </div>
            </div>
            {{-- <div class="mt-5 text-muted text-center">

            </div> --}}
            <div class="simple-footer">
              Copyright &copy; {{ \Carbon\Carbon::now()->year }} | Powered with <span class="love">💙</span> IT Team PTPN IV - Reg IV
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- General JS Scripts -->
  <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
  <script src="{{ asset('stisla-master') }}/assets/js/stisla.js"></script>

  <!-- JS Libraies -->

  <!-- Template JS File -->
  <script src="{{ asset('stisla-master') }}/assets/js/scripts.js"></script>
  <script src="{{ asset('stisla-master') }}/assets/js/custom.js"></script>

  <!-- Page Specific JS File -->
  <script src="sweetalert2.all.min.js"></script><script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    @if (Session::has('success'))
        $(document).ready(function(){
            @if(!Session::get('success')){
                Swal.fire({
                    icon: 'error',
                    title: 'Something Wrong!',
                    text: '{{ Session::get('message') }}'
                })
            }
            @endif
        })
    @endif
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
    })
  </script>
</body>
</html>

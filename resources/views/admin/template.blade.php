<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>@yield('title') &mdash; @yield('page-name')</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- CSS Libraries -->
  {{-- <link rel="stylesheet" href="{{ asset('stisla-master') }}/node_modules/jqvmap/dist/jqvmap.min.css">
  <link rel="stylesheet" href="{{ asset('stisla-master') }}/node_modules/weathericons/css/weather-icons.min.css">
  <link rel="stylesheet" href="{{ asset('stisla-master') }}/node_modules/weathericons/css/weather-icons-wind.min.css">
  <link rel="stylesheet" href="{{ asset('stisla-master') }}/node_modules/summernote/dist/summernote-bs4.css"> --}}

    @yield('css_libraries')

  <!-- Template CSS -->
  <link rel="stylesheet" href="{{ asset('stisla-master') }}/assets/css/style.css">
  <link rel="stylesheet" href="{{ asset('stisla-master') }}/assets/css/components.css">

  @yield('additional_style')
  <style>
    table.dataTable td {
        font-size: 11px;
    }
  </style>
</head>

<body>
  <div id="app">
    <div class="main-wrapper">
      <div class="navbar-bg"></div>
      <nav class="navbar navbar-expand-lg main-navbar">
        <form class="form-inline mr-auto">
          <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
            {{-- <li><a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none"><i class="fas fa-search"></i></a></li> --}}
          </ul>
          {{-- <div class="search-element">
            <input class="form-control" type="search" placeholder="Search" aria-label="Search" data-width="250">
            <button class="btn" type="submit"><i class="fas fa-search"></i></button>
            <div class="search-backdrop"></div>
            <div class="search-result">
              <div class="search-header">
                Histories
              </div>
              <div class="search-item">
                <a href="#">How to hack NASA using CSS</a>
                <a href="#" class="search-close"><i class="fas fa-times"></i></a>
              </div>
              <div class="search-item">
                <a href="#">Kodinger.com</a>
                <a href="#" class="search-close"><i class="fas fa-times"></i></a>
              </div>
              <div class="search-item">
                <a href="#">#Stisla</a>
                <a href="#" class="search-close"><i class="fas fa-times"></i></a>
              </div>
              <div class="search-header">
                Result
              </div>
              <div class="search-item">
                <a href="#">
                  <img class="mr-3 rounded" width="30" src="{{ asset('stisla-master') }}/assets/img/products/product-3-50.png" alt="product">
                  oPhone S9 Limited Edition
                </a>
              </div>
              <div class="search-item">
                <a href="#">
                  <img class="mr-3 rounded" width="30" src="{{ asset('stisla-master') }}/assets/img/products/product-2-50.png" alt="product">
                  Drone X2 New Gen-7
                </a>
              </div>
              <div class="search-item">
                <a href="#">
                  <img class="mr-3 rounded" width="30" src="{{ asset('stisla-master') }}/assets/img/products/product-1-50.png" alt="product">
                  Headphone Blitz
                </a>
              </div>
              <div class="search-header">
                Projects
              </div>
              <div class="search-item">
                <a href="#">
                  <div class="search-icon bg-danger text-white mr-3">
                    <i class="fas fa-code"></i>
                  </div>
                  Stisla Admin Template
                </a>
              </div>
              <div class="search-item">
                <a href="#">
                  <div class="search-icon bg-primary text-white mr-3">
                    <i class="fas fa-laptop"></i>
                  </div>
                  Create a new Homepage Design
                </a>
              </div>
            </div>
          </div> --}}
        </form>
        <ul class="navbar-nav navbar-right">
          {{-- <li class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown" class="nav-link nav-link-lg message-toggle beep"><i class="far fa-envelope"></i></a>
            <div class="dropdown-menu dropdown-list dropdown-menu-right">
              <div class="dropdown-header">Messages
                <div class="float-right">
                  <a href="#">Mark All As Read</a>
                </div>
              </div>
              <div class="dropdown-list-content dropdown-list-message">
                <a href="#" class="dropdown-item dropdown-item-unread">
                  <div class="dropdown-item-avatar">
                    <img alt="image" src="{{ asset('stisla-master') }}/assets/img/avatar/avatar-1.png" class="rounded-circle">
                    <div class="is-online"></div>
                  </div>
                  <div class="dropdown-item-desc">
                    <b>Kusnaedi</b>
                    <p>Hello, Bro!</p>
                    <div class="time">10 Hours Ago</div>
                  </div>
                </a>
                <a href="#" class="dropdown-item dropdown-item-unread">
                  <div class="dropdown-item-avatar">
                    <img alt="image" src="{{ asset('stisla-master') }}/assets/img/avatar/avatar-2.png" class="rounded-circle">
                  </div>
                  <div class="dropdown-item-desc">
                    <b>Dedik Sugiharto</b>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit</p>
                    <div class="time">12 Hours Ago</div>
                  </div>
                </a>
                <a href="#" class="dropdown-item dropdown-item-unread">
                  <div class="dropdown-item-avatar">
                    <img alt="image" src="{{ asset('stisla-master') }}/assets/img/avatar/avatar-3.png" class="rounded-circle">
                    <div class="is-online"></div>
                  </div>
                  <div class="dropdown-item-desc">
                    <b>Agung Ardiansyah</b>
                    <p>Sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                    <div class="time">12 Hours Ago</div>
                  </div>
                </a>
                <a href="#" class="dropdown-item">
                  <div class="dropdown-item-avatar">
                    <img alt="image" src="{{ asset('stisla-master') }}/assets/img/avatar/avatar-4.png" class="rounded-circle">
                  </div>
                  <div class="dropdown-item-desc">
                    <b>Ardian Rahardiansyah</b>
                    <p>Duis aute irure dolor in reprehenderit in voluptate velit ess</p>
                    <div class="time">16 Hours Ago</div>
                  </div>
                </a>
                <a href="#" class="dropdown-item">
                  <div class="dropdown-item-avatar">
                    <img alt="image" src="{{ asset('stisla-master') }}/assets/img/avatar/avatar-5.png" class="rounded-circle">
                  </div>
                  <div class="dropdown-item-desc">
                    <b>Alfa Zulkarnain</b>
                    <p>Exercitation ullamco laboris nisi ut aliquip ex ea commodo</p>
                    <div class="time">Yesterday</div>
                  </div>
                </a>
              </div>
              <div class="dropdown-footer text-center">
                <a href="#">View All <i class="fas fa-chevron-right"></i></a>
              </div>
            </div>
          </li> --}}
          {{-- <li class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown" class="nav-link notification-toggle nav-link-lg beep"><i class="far fa-bell"></i></a>
            <div class="dropdown-menu dropdown-list dropdown-menu-right">
              <div class="dropdown-header">Notifications
                <div class="float-right">
                  <a href="#">Mark All As Read</a>
                </div>
              </div>
              <div class="dropdown-list-content dropdown-list-icons">
                <a href="#" class="dropdown-item dropdown-item-unread">
                  <div class="dropdown-item-icon bg-primary text-white">
                    <i class="fas fa-code"></i>
                  </div>
                  <div class="dropdown-item-desc">
                    Template update is available now!
                    <div class="time text-primary">2 Min Ago</div>
                  </div>
                </a>
                <a href="#" class="dropdown-item">
                  <div class="dropdown-item-icon bg-info text-white">
                    <i class="far fa-user"></i>
                  </div>
                  <div class="dropdown-item-desc">
                    <b>You</b> and <b>Dedik Sugiharto</b> are now friends
                    <div class="time">10 Hours Ago</div>
                  </div>
                </a>
                <a href="#" class="dropdown-item">
                  <div class="dropdown-item-icon bg-success text-white">
                    <i class="fas fa-check"></i>
                  </div>
                  <div class="dropdown-item-desc">
                    <b>Kusnaedi</b> has moved task <b>Fix bug header</b> to <b>Done</b>
                    <div class="time">12 Hours Ago</div>
                  </div>
                </a>
                <a href="#" class="dropdown-item">
                  <div class="dropdown-item-icon bg-danger text-white">
                    <i class="fas fa-exclamation-triangle"></i>
                  </div>
                  <div class="dropdown-item-desc">
                    Low disk space. Let's clean it!
                    <div class="time">17 Hours Ago</div>
                  </div>
                </a>
                <a href="#" class="dropdown-item">
                  <div class="dropdown-item-icon bg-info text-white">
                    <i class="fas fa-bell"></i>
                  </div>
                  <div class="dropdown-item-desc">
                    Welcome to Stisla template!
                    <div class="time">Yesterday</div>
                  </div>
                </a>
              </div>
              <div class="dropdown-footer text-center">
                <a href="#">View All <i class="fas fa-chevron-right"></i></a>
              </div>
            </div>
          </li> --}}
          <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
            <img alt="image" src="{{ asset('stisla-master') }}/assets/img/avatar/avatar-1.png" class="rounded-circle mr-1">
            <div class="d-sm-none d-lg-inline-block">{{ auth()->user()->name }}</div></a>
            <div class="dropdown-menu dropdown-menu-right">
              <div class="dropdown-title">User Level : {{ auth()->user()->level->level }}</div>
              <a href="{{ route('admin.profile.index') }}" class="dropdown-item has-icon">
                <i class="far fa-user"></i> Profile
              </a>
              {{-- <a href="{{  }}" class="dropdown-item has-icon">
                <i class="fas fa-bolt"></i> Activities
              </a> --}}
              {{-- <a href="features-settings.html" class="dropdown-item has-icon">
                <i class="fas fa-cog"></i> Settings
              </a> --}}
              <div class="dropdown-divider"></div>
              <a href="{{ route('logout') }}" class="dropdown-item has-icon text-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
              </a>
            </div>
          </li>
        </ul>
      </nav>
      <div class="main-sidebar">
        <aside id="sidebar-wrapper">
          <div class="sidebar-brand">
            <a href="{{ route('admin.index') }}">
                <img src="{{ asset('img/edc_top.png') }}" width="75%" height="100%">
            </a>
          </div>
          <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ route('admin.index') }}">EDC</a>
          </div>
          <ul class="sidebar-menu">
              <li class="menu-header">Dashboard</li>
              <li class="nav-item {{ Route::currentRouteName() == 'admin.index' ? 'active' : '' }}">
                <a href="{{route('admin.index') }}" class="nav-link"><i class="fas fa-fire"></i><span>Dashboard</span></a>
              </li>
              <li class="menu-header">Menu</li>
              <li class="nav-item {{ Route::currentRouteName() == 'admin.dokumen.index' ? 'active' : '' }}">
                <a href="{{route('admin.dokumen.index') }}" class="nav-link"><i class="fas fa-file"></i><span>Master Dokumen</span></a>
              </li>
                @php
                    $master_jenis_file = \App\Models\MasterJenisFile::all();
                    $master_bagian_kandir = \App\Models\MasterBagian::where('tipe', 'kandir')->get()
               @endphp
                @foreach ($master_jenis_file as $item)
                    @if ($item->has_sub == true)
                        <li class="nav-item dropdown {{ Route::currentRouteName() == 'admin.dokumen.index_by_jenis' && $jenis_file->kode == $item->kode ? 'active' : '' }}">
                            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas {{ $item->fa_icon }}">
                                </i> <span>{{ $item->kepanjangan }}</span>
                            </a>
                            <ul class="dropdown-menu">
                                @foreach ($master_bagian_kandir as $item2)
                                    <li class="{{ Route::currentRouteName() == 'admin.dokumen.index_by_jenis' && $jenis_file->kode == $item->kode && $bagian->kode_bagian == $item2->kode_bagian ? 'active' : '' }}">
                                        <a class="nav-link" href="{{ route('admin.dokumen.index_by_jenis', [$item->kode, $item2->kode_bagian]) }}">{{ $item2->kode_bagian }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @else

                    <li class="nav-item  {{ Route::currentRouteName() == 'admin.dokumen.index_by_jenis' && $jenis_file->kode == $item->kode ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.dokumen.index_by_jenis', [$item->kode, null]) }}">
                            <i class="fas {{ $item->fa_icon }}"></i>
                            <span>{{ $item->kepanjangan }}</span>
                        </a>
                    </li>
                    @endif
                @endforeach

              <li class="menu-header">Users</li>
              <li class="nav-item {{ Route::currentRouteName() == 'admin.user.index' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.user.index') }}">
                    <i class="fas fa-users"></i>
                    <span>Master User</span>
                </a>
            </li>
            </ul>

            <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
              <a href="{{ route('logout') }}" class="btn btn-primary btn-lg btn-block btn-icon-split">
                <i class="fas fa-sign-out-alt"></i> Logout
              </a>
            </div>
        </aside>
      </div>

      <!-- Main Content -->
      <div class="main-content">
        @yield('content')
      </div>
      <footer class="main-footer">
        <div class="footer-left">
            @php
                $tahun_buat = 2022;
                $tahun_ini = \Carbon\Carbon::today()->year;
                // $tahun = $tahun_ini - $tahun_buat == 0 ? $tahun_buat : $tahun_ini - $tahun_buat
            @endphp
          Copyright &copy; {{ $tahun_ini == $tahun_buat ? $tahun_buat : $tahun_buat.'-'.$tahun_ini }} <div class="bullet"> </div> {{ env('APP_NAME') }}
        </div>
        {{-- <div class="footer-right">
          2.3.0
        </div> --}}
      </footer>
    </div>
    @yield('modals')
  </div>

  <!-- General JS Scripts -->
  <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
  <script src="{{ asset('stisla-master') }}/assets/js/stisla.js"></script>

  {{-- <!-- JS Libraies -->
  <script src="{{ asset('stisla-master') }}/node_modules/simpleweather/jquery.simpleWeather.min.js"></script>
  <script src="{{ asset('stisla-master') }}/node_modules/chart.js/dist/Chart.min.js"></script>
  <script src="{{ asset('stisla-master') }}/node_modules/jqvmap/dist/jquery.vmap.min.js"></script>
  <script src="{{ asset('stisla-master') }}/node_modules/jqvmap/dist/maps/jquery.vmap.world.js"></script>
  <script src="{{ asset('stisla-master') }}/node_modules/summernote/dist/summernote-bs4.js"></script>
  <script src="{{ asset('stisla-master') }}/node_modules/chocolat/dist/js/jquery.chocolat.min.js"></script> --}}

  <!-- Template JS File -->
  <script src="{{ asset('stisla-master') }}/assets/js/scripts.js"></script>
  <script src="{{ asset('stisla-master') }}/assets/js/custom.js"></script>

  <!-- Page Specific JS File -->
  {{-- <script src="{{ asset('stisla-master') }}/assets/js/page/index-0.js"></script> --}}
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session()->has('success'))
        <script>
            $(document).ready(function() {
                @if (session()->get('success'))
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: '{{ session()->get('message') }}',
                        showConfirmButton: false,
                    })
                @elseif (!session()->get('success'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: '{{ session()->get('message') }}',
                    })
                @endif
            })
        </script>
    @endif
  @yield('scripts')
</body>
</html>

<!doctype html>
<html lang="fa" dir="rtl">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        @yield('title', 'سیستم مدیریت فروش')
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>


<body>

<div class="page">

    {{-- Sidebar --}}
    <aside class="navbar navbar-vertical navbar-expand-lg navbar-dark">

        <div class="container-fluid">


            <h1 class="navbar-brand navbar-brand-autodark">
                <span>
                    Woxilon
                </span>
            </h1>


            <div class="collapse navbar-collapse show">

                <ul class="navbar-nav">


                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <span class="nav-link-icon">
                                <i class="bi bi-speedometer2"></i>
                            </span>
                            داشبورد
                        </a>
                    </li>


                    <li class="nav-item">

                        <a class="nav-link"
                           href="{{ route('products.index') }}">

                            <span class="nav-link-icon">
                                <i class="bi bi-box-seam"></i>
                            </span>

                            کالاها

                        </a>

                    </li>


                    <li class="nav-item">

                        <a class="nav-link" href="#">

                            <span class="nav-link-icon">
                                <i class="bi bi-cart"></i>
                            </span>

                            فروش

                        </a>

                    </li>


                    <li class="nav-item">

                        <a class="nav-link" href="#">

                            <span class="nav-link-icon">
                                <i class="bi bi-bar-chart"></i>
                            </span>

                            گزارشات

                        </a>

                    </li>


                </ul>

            </div>

        </div>

    </aside>



    {{-- Main --}}

    <div class="page-wrapper">


        <header class="navbar navbar-expand-md d-print-none">

            <div class="container-xl">

                <div class="navbar-nav flex-row order-md-last">

                    <div class="nav-item">

                        <span class="nav-link">

                            مدیر سیستم

                        </span>

                    </div>

                </div>

            </div>

        </header>



        <div class="page-body">

            <div class="container-xl">

                @yield('content')

            </div>

        </div>



    </div>


</div>


</body>

</html>
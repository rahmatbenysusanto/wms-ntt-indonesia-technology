<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard Mobile</title>

    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        body {
            background-color: #222163;
        }

        .mobile-header {
            padding-top: 12px;
            padding-bottom: 28px;
        }
    </style>
</head>
<body>

    <div class="mobile-header">
        <div class="row">
            <div class="col-2">

            </div>
            <div class="col-8">
                <p class="mb-0 text-center text-white">Trans Kargo Solusindo</p>
            </div>
            <div class="col-2">

            </div>
        </div>
    </div>

    <div class="container-fluid" id="menu">
        <h2 class="mb-3 text-white">Dashboard</h2>
        <div class="row">
            <div class="col-6">
                <a href="{{ route('inbound.index.mobile') }}">
                    <div class="card pt-2 pb-2 ps-4 pe-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="mdi mdi-book-arrow-left-outline" style="font-size: 34px;"></i>
                                <p class="mb-0">Inbound</p>
                            </div>
                            <i class="mdi mdi-arrow-top-right" style="font-size: 34px;"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6">
                <a href="{{ route('outbound.index.mobile') }}">
                    <div class="card pt-2 pb-2 ps-4 pe-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="mdi mdi-book-arrow-right-outline" style="font-size: 34px;"></i>
                                <p class="mb-0">Outbound</p>
                            </div>
                            <i class="mdi mdi-arrow-top-right" style="font-size: 34px;"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6">
                <a href="{{ route('inventory.index.mobile') }}">
                    <div class="card pt-2 pb-2 ps-4 pe-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="mdi mdi-package-variant-closed" style="font-size: 34px;"></i>
                                <p class="mb-0">Inventory</p>
                            </div>
                            <i class="mdi mdi-arrow-top-right" style="font-size: 34px;"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6">
                <a href="{{ route('inventory.box.mobile') }}">
                    <div class="card pt-2 pb-2 ps-4 pe-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="mdi mdi-package" style="font-size: 34px;"></i>
                                <p class="mb-0">Box</p>
                            </div>
                            <i class="mdi mdi-arrow-top-right" style="font-size: 34px;"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6">
                <div class="card pt-2 pb-2 ps-4 pe-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="mdi mdi-barcode-scan" style="font-size: 34px;"></i>
                            <p class="mb-0">Scan Box</p>
                        </div>
                        <i class="mdi mdi-arrow-top-right" style="font-size: 34px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
    {{--<script src="{{ asset('assets/js/plugins.js') }}"></script>--}}
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/dashboard-projects.init.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/sweetalerts.init.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>

    <script src="{{ asset('assets/libs/dropzone/dropzone-min.js') }}"></script>
    <script src="{{ asset('assets/libs/filepond/filepond.min.js') }}"></script>
    <script src="{{ asset('assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js') }}"></script>
    <script src="{{ asset('assets/libs/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js') }}"></script>
    <script src="{{ asset('assets/libs/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js') }}"></script>
    <script src="{{ asset('assets/libs/filepond-plugin-file-encode/filepond-plugin-file-encode.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-file-upload.init.js') }}"></script>
</body>
</html>

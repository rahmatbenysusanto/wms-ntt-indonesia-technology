<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="@yield('sizeBarSize', 'lg')" data-sidebar-image="none" data-preloader="disable">
<head>
    <meta charset="utf-8" />
    <title>@yield('title') | Trans Kargo Solusindo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    <script src="{{ asset('assets/js/layout.js') }}"></script>
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/libs/dropzone/dropzone.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/libs/filepond/filepond.min.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css') }}">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />

</head>

<body>

<!-- Begin page -->
<div id="layout-wrapper">

    <header id="page-topbar">
        <div class="layout-width">
            <div class="navbar-header">
                <div class="d-flex">
                    <!-- LOGO -->
                    <div class="navbar-brand-box horizontal-logo">
                        <a href="#" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="{{ asset('assets/images/maximaz-logo.png') }}" alt="" height="22">
                        </span>
                            <span class="logo-lg">
                            <img src="{{ asset('assets/images/maximaz-logo.png') }}" alt="" height="17">
                        </span>
                        </a>

                        <a href="#" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="{{ asset('assets/images/maximaz-logo.png') }}" alt="" height="22">
                        </span>
                            <span class="logo-lg">
                            <img src="{{ asset('assets/images/maximaz-logo.png') }}" alt="" height="17">
                        </span>
                        </a>
                    </div>

                    <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger shadow-none" id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                    </button>
                </div>

                <div class="d-flex align-items-center">

                    <div class="dropdown d-md-none topbar-head-dropdown header-item">
                        <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle shadow-none" id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bx bx-search fs-22"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-search-dropdown">
                            <form class="p-3">
                                <div class="form-group m-0">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Search ..." aria-label="Recipient's username">
                                        <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="ms-1 header-item d-none d-sm-flex">
                        <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle shadow-none" data-toggle="fullscreen">
                            <i class='bx bx-fullscreen fs-22'></i>
                        </button>
                    </div>

                    <div class="ms-1 header-item d-none d-sm-flex">
                        <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode shadow-none">
                            <i class='bx bx-moon fs-22'></i>
                        </button>
                    </div>

                    <div class="dropdown topbar-head-dropdown ms-1 header-item" id="notificationDropdown">
                        <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle shadow-none" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                            <i class='bx bx-bell fs-22'></i>
                            <span class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger">3<span class="visually-hidden">unread messages</span></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-notifications-dropdown">

                            <div class="dropdown-head bg-primary bg-pattern rounded-top">
                                <div class="p-3">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h6 class="m-0 fs-16 fw-semibold text-white"> Notifications </h6>
                                        </div>
                                        <div class="col-auto dropdown-tabs">
                                            <span class="badge bg-light-subtle text-body fs-13"> 4 New</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="px-2 pt-2">
                                    <ul class="nav nav-tabs dropdown-tabs nav-tabs-custom" data-dropdown-tabs="true" id="notificationItemsTab" role="tablist">
                                        <li class="nav-item waves-effect waves-light">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#all-noti-tab" role="tab" aria-selected="true">
                                                All (4)
                                            </a>
                                        </li>
                                        <li class="nav-item waves-effect waves-light">
                                            <a class="nav-link" data-bs-toggle="tab" href="#messages-tab" role="tab" aria-selected="false">
                                                Messages
                                            </a>
                                        </li>
                                        <li class="nav-item waves-effect waves-light">
                                            <a class="nav-link" data-bs-toggle="tab" href="#alerts-tab" role="tab" aria-selected="false">
                                                Alerts
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                            </div>

                            <div class="tab-content position-relative" id="notificationItemsTabContent">
                                <div class="tab-pane fade show active py-2 ps-2" id="all-noti-tab" role="tabpanel">
                                    <div data-simplebar style="max-height: 300px;" class="pe-2">
                                        <div class="text-reset notification-item d-block dropdown-item position-relative">
                                            <div class="d-flex">
                                                <div class="avatar-xs me-3 flex-shrink-0">
                                                <span class="avatar-title bg-info-subtle text-info rounded-circle fs-16">
                                                    <i class="bx bx-badge-check"></i>
                                                </span>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <a href="#!" class="stretched-link">
                                                        <h6 class="mt-0 mb-2 lh-base">Your <b>Elite</b> author Graphic
                                                            Optimization <span class="text-secondary">reward</span> is
                                                            ready!
                                                        </h6>
                                                    </a>
                                                    <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                        <span><i class="mdi mdi-clock-outline"></i> Just 30 sec ago</span>
                                                    </p>
                                                </div>
                                                <div class="px-2 fs-15">
                                                    <div class="form-check notification-check">
                                                        <input class="form-check-input" type="checkbox" value="" id="all-notification-check01">
                                                        <label class="form-check-label" for="all-notification-check01"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-reset notification-item d-block dropdown-item position-relative">
                                            <div class="d-flex">
                                                <img src="assets/images/users/avatar-2.jpg" class="me-3 rounded-circle avatar-xs flex-shrink-0" alt="user-pic">
                                                <div class="flex-grow-1">
                                                    <a href="#!" class="stretched-link">
                                                        <h6 class="mt-0 mb-1 fs-13 fw-semibold">Angela Bernier</h6>
                                                    </a>
                                                    <div class="fs-13 text-muted">
                                                        <p class="mb-1">Answered to your comment on the cash flow forecast's
                                                            graph 🔔.</p>
                                                    </div>
                                                    <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                        <span><i class="mdi mdi-clock-outline"></i> 48 min ago</span>
                                                    </p>
                                                </div>
                                                <div class="px-2 fs-15">
                                                    <div class="form-check notification-check">
                                                        <input class="form-check-input" type="checkbox" value="" id="all-notification-check02">
                                                        <label class="form-check-label" for="all-notification-check02"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-reset notification-item d-block dropdown-item position-relative">
                                            <div class="d-flex">
                                                <div class="avatar-xs me-3 flex-shrink-0">
                                                <span class="avatar-title bg-danger-subtle text-danger rounded-circle fs-16">
                                                    <i class='bx bx-message-square-dots'></i>
                                                </span>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <a href="#!" class="stretched-link">
                                                        <h6 class="mt-0 mb-2 fs-13 lh-base">You have received <b class="text-success">20</b> new messages in the conversation
                                                        </h6>
                                                    </a>
                                                    <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                        <span><i class="mdi mdi-clock-outline"></i> 2 hrs ago</span>
                                                    </p>
                                                </div>
                                                <div class="px-2 fs-15">
                                                    <div class="form-check notification-check">
                                                        <input class="form-check-input" type="checkbox" value="" id="all-notification-check03">
                                                        <label class="form-check-label" for="all-notification-check03"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-reset notification-item d-block dropdown-item position-relative">
                                            <div class="d-flex">
                                                <img src="assets/images/users/avatar-8.jpg" class="me-3 rounded-circle avatar-xs flex-shrink-0" alt="user-pic">
                                                <div class="flex-grow-1">
                                                    <a href="#!" class="stretched-link">
                                                        <h6 class="mt-0 mb-1 fs-13 fw-semibold">Maureen Gibson</h6>
                                                    </a>
                                                    <div class="fs-13 text-muted">
                                                        <p class="mb-1">We talked about a project on linkedin.</p>
                                                    </div>
                                                    <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                        <span><i class="mdi mdi-clock-outline"></i> 4 hrs ago</span>
                                                    </p>
                                                </div>
                                                <div class="px-2 fs-15">
                                                    <div class="form-check notification-check">
                                                        <input class="form-check-input" type="checkbox" value="" id="all-notification-check04">
                                                        <label class="form-check-label" for="all-notification-check04"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="my-3 text-center view-all">
                                            <button type="button" class="btn btn-soft-success waves-effect waves-light">View
                                                All Notifications <i class="ri-arrow-right-line align-middle"></i></button>
                                        </div>
                                    </div>

                                </div>

                                <div class="tab-pane fade py-2 ps-2" id="messages-tab" role="tabpanel" aria-labelledby="messages-tab">
                                    <div data-simplebar style="max-height: 300px;" class="pe-2">
                                        <div class="text-reset notification-item d-block dropdown-item">
                                            <div class="d-flex">
                                                <img src="assets/images/users/avatar-3.jpg" class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                                <div class="flex-grow-1">
                                                    <a href="#!" class="stretched-link">
                                                        <h6 class="mt-0 mb-1 fs-13 fw-semibold">James Lemire</h6>
                                                    </a>
                                                    <div class="fs-13 text-muted">
                                                        <p class="mb-1">We talked about a project on linkedin.</p>
                                                    </div>
                                                    <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                        <span><i class="mdi mdi-clock-outline"></i> 30 min ago</span>
                                                    </p>
                                                </div>
                                                <div class="px-2 fs-15">
                                                    <div class="form-check notification-check">
                                                        <input class="form-check-input" type="checkbox" value="" id="messages-notification-check01">
                                                        <label class="form-check-label" for="messages-notification-check01"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-reset notification-item d-block dropdown-item">
                                            <div class="d-flex">
                                                <img src="assets/images/users/avatar-2.jpg" class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                                <div class="flex-grow-1">
                                                    <a href="#!" class="stretched-link">
                                                        <h6 class="mt-0 mb-1 fs-13 fw-semibold">Angela Bernier</h6>
                                                    </a>
                                                    <div class="fs-13 text-muted">
                                                        <p class="mb-1">Answered to your comment on the cash flow forecast's
                                                            graph 🔔.</p>
                                                    </div>
                                                    <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                        <span><i class="mdi mdi-clock-outline"></i> 2 hrs ago</span>
                                                    </p>
                                                </div>
                                                <div class="px-2 fs-15">
                                                    <div class="form-check notification-check">
                                                        <input class="form-check-input" type="checkbox" value="" id="messages-notification-check02">
                                                        <label class="form-check-label" for="messages-notification-check02"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-reset notification-item d-block dropdown-item">
                                            <div class="d-flex">
                                                <img src="assets/images/users/avatar-6.jpg" class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                                <div class="flex-grow-1">
                                                    <a href="#!" class="stretched-link">
                                                        <h6 class="mt-0 mb-1 fs-13 fw-semibold">Kenneth Brown</h6>
                                                    </a>
                                                    <div class="fs-13 text-muted">
                                                        <p class="mb-1">Mentionned you in his comment on 📃 invoice #12501.
                                                        </p>
                                                    </div>
                                                    <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                        <span><i class="mdi mdi-clock-outline"></i> 10 hrs ago</span>
                                                    </p>
                                                </div>
                                                <div class="px-2 fs-15">
                                                    <div class="form-check notification-check">
                                                        <input class="form-check-input" type="checkbox" value="" id="messages-notification-check03">
                                                        <label class="form-check-label" for="messages-notification-check03"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-reset notification-item d-block dropdown-item">
                                            <div class="d-flex">
                                                <img src="assets/images/users/avatar-8.jpg" class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                                <div class="flex-grow-1">
                                                    <a href="#!" class="stretched-link">
                                                        <h6 class="mt-0 mb-1 fs-13 fw-semibold">Maureen Gibson</h6>
                                                    </a>
                                                    <div class="fs-13 text-muted">
                                                        <p class="mb-1">We talked about a project on linkedin.</p>
                                                    </div>
                                                    <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                        <span><i class="mdi mdi-clock-outline"></i> 3 days ago</span>
                                                    </p>
                                                </div>
                                                <div class="px-2 fs-15">
                                                    <div class="form-check notification-check">
                                                        <input class="form-check-input" type="checkbox" value="" id="messages-notification-check04">
                                                        <label class="form-check-label" for="messages-notification-check04"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="my-3 text-center view-all">
                                            <button type="button" class="btn btn-soft-success waves-effect waves-light">View
                                                All Messages <i class="ri-arrow-right-line align-middle"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade p-4" id="alerts-tab" role="tabpanel" aria-labelledby="alerts-tab"></div>

                                <div class="notification-actions" id="notification-actions">
                                    <div class="d-flex text-muted justify-content-center">
                                        Select <div id="select-content" class="text-body fw-semibold px-1">0</div> Result <button type="button" class="btn btn-link link-danger p-0 ms-3" data-bs-toggle="modal" data-bs-target="#removeNotificationModal">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dropdown ms-sm-3 header-item topbar-user">
                        <button type="button" class="btn shadow-none" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle header-profile-user" src="{{ asset('assets/images/users/avatar-1.jpg') }}" alt="Header Avatar">
                            <span class="text-start ms-xl-2">
                                <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ Auth::user()->name }}</span>
                                <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">Admin Gudang</span>
                            </span>
                        </span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <h6 class="dropdown-header">Welcome {{ Auth::user()->name }}</h6>
                            <a class="dropdown-item" href="{{ route('logout') }}"><i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle" data-key="t-logout">Logout</span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- removeNotificationModal -->
    <div id="removeNotificationModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="NotificationModalbtn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="mt-2 text-center">
                        <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                        <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                            <h4>Are you sure ?</h4>
                            <p class="text-muted mx-4 mb-0">Are you sure you want to remove this Notification ?</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                        <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn w-sm btn-danger" id="delete-notification">Yes, Delete It!</button>
                    </div>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <!-- ========== App Menu ========== -->
    <div class="app-menu navbar-menu">
        <!-- LOGO -->
        <div class="navbar-brand-box">
            <!-- Dark Logo-->
            <a href="#" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/images/maximaz-logo.png') }}" alt="" height="22">
                    </span>
                <span class="logo-lg">
                        <img src="{{ asset('assets/images/maximaz-logo.png') }}" alt="" height="17">
                    </span>
            </a>
            <!-- Light Logo-->
            <a href="#" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/images/maximaz-logo.png') }}" alt="" height="22">
                    </span>
                <span class="logo-lg">
                        <img src="{{ asset('assets/images/maximaz-logo.png') }}" alt="" height="35">
                    </span>
            </a>
            <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
                <i class="ri-record-circle-line"></i>
            </button>
        </div>

        <div id="scrollbar">
            <div class="container-fluid">

                <div id="two-column-menu"></div>
                <ul class="navbar-nav" id="navbar-nav">
                    <li class="menu-title"><span data-key="t-menu">Dashboard</span></li>
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ in_array($title, ['Dashboard', 'Dashboard PO', 'Dashboard Aging', 'Dashboard Outbound']) ? 'active' : '' }}"
                           href="#sidebarDashboard" data-bs-toggle="collapse" role="button"
                           aria-expanded="false" aria-controls="sidebarDashboard">
                            <i class="mdi mdi-speedometer"></i>
                            <span data-key="t-widgets">Dashboard</span>
                        </a>

                        <div class="collapse menu-dropdown {{ in_array($title, ['Dashboard', 'Dashboard PO', 'Dashboard Aging', 'Dashboard Outbound']) ? 'show' : '' }}"
                             id="sidebarDashboard">
                            <ul class="nav nav-sm flex-column">
                                @if(Session::get('userHasMenu')->contains('Main Dashboard'))
                                    <li class="nav-item">
                                        <a href="{{ route('dashboard') }}" class="nav-link {{ $title == 'Dashboard' ? 'active' : '' }}" data-key="t-analytics"> Main </a>
                                    </li>
                                @endif
                                @if(Session::get('userHasMenu')->contains('PO Dashboard'))
                                    <li class="nav-item">
                                        <a href="{{ route('dashboard.po') }}" class="nav-link {{ $title == 'Dashboard PO' ? 'active' : '' }}" data-key="t-analytics"> PO </a>
                                    </li>
                                @endif
                                @if(Session::get('userHasMenu')->contains('Aging Dashboard'))
                                    <li class="nav-item">
                                        <a href="{{ route('dashboard.aging') }}" class="nav-link {{ $title == 'Dashboard Aging' ? 'active' : '' }}" data-key="t-analytics"> Aging </a>
                                    </li>
                                @endif
                                @if(Session::get('userHasMenu')->contains('Outbound Dashboard'))
                                    <li class="nav-item">
                                        <a href="{{ route('dashboard.outbound') }}" class="nav-link {{ $title == 'Dashboard Outbound' ? 'active' : '' }}" data-key="t-analytics"> Outbound </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>

                    @if(Session::get('userHasMenu', collect())->intersect(['Purchase Order', 'Quality Control', 'Put Away'])->isNotEmpty())
                        <li class="menu-title"><span data-key="t-menu">WAREHOUSE MODULE</span></li>
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ in_array($title, ['Purchase Order', 'Quality Control', 'Put Away']) ? 'active' : '' }}" href="#sidebarInbound" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarInbound">
                                <i class="mdi mdi-package-down"></i> <span data-key="t-dashboards">Inbound</span>
                            </a>
                            <div class="collapse menu-dropdown {{ in_array($title, ['Purchase Order', 'Quality Control', 'Put Away']) ? 'show' : '' }}" id="sidebarInbound">
                                <ul class="nav nav-sm flex-column">
                                    @if(Session::get('userHasMenu')->contains('Purchase Order'))
                                        <li class="nav-item">
                                            <a href="{{ route('inbound.purchase-order') }}" class="nav-link {{ in_array($title, ['Purchase Order']) ? 'active' : '' }}" data-key="t-analytics"> Purchase Order </a>
                                        </li>
                                    @endif
                                    @if(Session::get('userHasMenu')->contains('Quality Control'))
                                        <li class="nav-item">
                                            <a href="{{ route('inbound.quality-control') }}" class="nav-link {{ in_array($title, ['Quality Control']) ? 'active' : '' }}" data-key="t-analytics"> Quality Control </a>
                                        </li>
                                    @endif
                                    @if(Session::get('userHasMenu')->contains('Put Away'))
                                        <li class="nav-item">
                                            <a href="{{ route('inbound.put-away') }}" class="nav-link {{ in_array($title, ['Put Away']) ? 'active' : '' }}" data-key="t-analytics"> Put Away </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </li>
                    @endif

                    @if(Session::get('userHasMenu', collect())->intersect(['Produk List', 'Produk Aging', 'Box List', 'Transfer Location', 'Cycle Count'])->isNotEmpty())
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ in_array($title, ['Inventory', 'Cycle Count', 'Transfer Location', 'Inventory Box', 'Inventory Aging']) ? 'active' : '' }}" href="#sidebarInventory" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarInventory">
                                <i class="mdi mdi-package-variant"></i> <span data-key="t-dashboards">Inventory</span>
                            </a>
                            <div class="collapse menu-dropdown {{ in_array($title, ['Inventory', 'Cycle Count', 'Transfer Location', 'Inventory Box', 'Inventory Aging']) ? 'show' : '' }}" id="sidebarInventory">
                                <ul class="nav nav-sm flex-column">
                                    @if(Session::get('userHasMenu')->contains('Produk List'))
                                        <li class="nav-item">
                                            <a href="{{ route('inventory.index') }}" class="nav-link {{ $title == 'Inventory' ? 'active' : '' }}" data-key="t-analytics"> Produk List </a>
                                        </li>
                                    @endif
                                    @if(Session::get('userHasMenu')->contains('Produk Aging'))
                                        <li class="nav-item">
                                            <a href="{{ route('inventory.aging') }}" class="nav-link {{ $title == 'Inventory Aging' ? 'active' : '' }}" data-key="t-analytics"> Produk Aging </a>
                                        </li>
                                    @endif
                                    @if(Session::get('userHasMenu')->contains('Box List'))
                                        <li class="nav-item">
                                            <a href="{{ route('inventory.box') }}" class="nav-link {{ $title == 'Inventory Box' ? 'active' : '' }}" data-key="t-analytics"> Box List </a>
                                        </li>
                                    @endif
                                    @if(Session::get('userHasMenu')->contains('Transfer Location'))
                                        <li class="nav-item">
                                            <a href="{{ route('inventory.transfer-location') }}" class="nav-link {{ $title == 'Transfer Location' ? 'active' : '' }}" data-key="t-analytics"> Transfer Location </a>
                                        </li>
                                    @endif
                                    @if(Session::get('userHasMenu')->contains('Cycle Count'))
                                        <li class="nav-item">
                                            <a href="{{ route('inventory.cycle-count') }}" class="nav-link {{ $title == 'Cycle Count' ? 'active' : '' }}" data-key="t-analytics"> Cycle Count </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </li>
                    @endif

                    @if(Session::get('userHasMenu')->contains('Order List'))
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ $title == 'Outbound' ? 'active' : '' }}" href="#sidebarOutbound" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarOutbound">
                                <i class="mdi mdi-package-up"></i> <span data-key="t-dashboards">Outbound</span>
                            </a>
                            <div class="collapse menu-dropdown {{ $title == 'Outbound' ? 'show' : '' }}" id="sidebarOutbound">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ route('outbound.index') }}" class="nav-link {{ $title == 'Outbound' ? 'active' : '' }}" data-key="t-analytics"> Order List </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endif

                    @if(Session::get('userHasMenu', collect())->intersect(['General Room Inventory', 'General Room Outbound', 'PM Room Inventory', 'PM Room Outbound', 'Spare Room Inventory', 'Spare Room Outbound'])->isNotEmpty())
                        <li class="menu-title"><span data-key="t-menu">Warehouse Room</span></li>

                        @if(Session::get('userHasMenu', collect())->intersect(['General Room Inventory', 'General Room Outbound'])->isNotEmpty())
                            <li class="nav-item">
                                <a class="nav-link menu-link {{ in_array($title, ['General Room', 'General Room Outbound']) ? 'active' : '' }}" href="#sidebarGeneralRoom" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarGeneralRoom">
                                    <i class="mdi mdi-book-clock"></i> <span data-key="t-dashboards">General Room</span>
                                </a>
                                <div class="collapse menu-dropdown {{ in_array($title, ['General Room', 'General Room Outbound']) ? 'show' : '' }}" id="sidebarGeneralRoom">
                                    <ul class="nav nav-sm flex-column">
                                        @if(Session::get('userHasMenu')->contains('General Room Inventory'))
                                            <li class="nav-item">
                                                <a href="{{ route('general-room.index') }}" class="nav-link {{ $title == 'General Room' ? 'active' : '' }}" data-key="t-analytics"> Inventory </a>
                                            </li>
                                        @endif
                                        @if(Session::get('userHasMenu')->contains('General Room Outbound'))
                                            <li class="nav-item">
                                                <a href="{{ route('general-room.outbound') }}" class="nav-link {{ $title == 'General Room Outbound' ? 'active' : '' }}" data-key="t-analytics"> Outbound </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </li>
                        @endif

                        @if(Session::get('userHasMenu', collect())->intersect(['PM Room Inventory', 'PM Room Outbound'])->isNotEmpty())
                            <li class="nav-item">
                                <a class="nav-link menu-link {{ in_array($title, ['Pm Room', 'Pm Room Outbound']) ? 'active' : '' }}" href="#sidebarPmRoom" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarPmRoom">
                                    <i class="mdi mdi-notebook-outline"></i> <span data-key="t-dashboards">PM Room</span>
                                </a>
                                <div class="collapse menu-dropdown {{ in_array($title, ['Pm Room', 'Pm Room Outbound']) ? 'show' : '' }}" id="sidebarPmRoom">
                                    <ul class="nav nav-sm flex-column">
                                        @if(Session::get('userHasMenu')->contains('PM Room Inventory'))
                                            <li class="nav-item">
                                                <a href="{{ route('pm-room.index') }}" class="nav-link {{ $title == 'Pm Room' ? 'active' : '' }}" data-key="t-analytics"> Inventory </a>
                                            </li>
                                        @endif
                                        @if(Session::get('userHasMenu')->contains('PM Room Outbound'))
                                            <li class="nav-item">
                                                <a href="{{ route('pm-room.outbound') }}" class="nav-link {{ $title == 'Pm Room Outbound' ? 'active' : '' }}" data-key="t-analytics"> Outbound </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </li>
                        @endif

                        @if(Session::get('userHasMenu', collect())->intersect(['Spare Room Inventory', 'Spare Room Outbound'])->isNotEmpty())
                            <li class="nav-item">
                                <a class="nav-link menu-link {{ in_array($title, ['Spare Room', 'Spare Room Outbound']) ? 'active' : '' }}" href="#sidebarSpareRoom" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarSpareRoom">
                                    <i class="mdi mdi-notebook-check-outline"></i> <span data-key="t-dashboards">Spare Room</span>
                                </a>
                                <div class="collapse menu-dropdown {{ in_array($title, ['Spare Room', 'Spare Room Outbound']) ? 'show' : '' }}" id="sidebarSpareRoom">
                                    <ul class="nav nav-sm flex-column">
                                        @if(Session::get('userHasMenu')->contains('Spare Room Inventory'))
                                            <li class="nav-item">
                                                <a href="{{ route('spare-room.index') }}" class="nav-link {{ $title == 'Spare Room' ? 'active' : '' }}" data-key="t-analytics"> Inventory </a>
                                            </li>
                                        @endif
                                        @if(Session::get('userHasMenu')->contains('Spare Room Outbound'))
                                            <li class="nav-item">
                                                <a href="{{ route('spare-room.outbound') }}" class="nav-link {{ $title == 'Spare Room Outbound' ? 'active' : '' }}" data-key="t-analytics"> Outbound </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </li>
                        @endif
                    @endif

                    @if(Session::get('userHasMenu', collect())->intersect(['Vendor', 'Customer'])->isNotEmpty())
                        <li class="menu-title"><span data-key="t-menu">Manajemen Client</span></li>
                        @if(Session::get('userHasMenu')->contains('Vendor'))
                            <li class="nav-item">
                                <a class="nav-link menu-link {{ $title == 'Vendor' ? 'active' : '' }}" href="{{ route('vendor') }}">
                                    <i class="mdi mdi-truck"></i> <span data-key="t-vendor">Vendor</span>
                                </a>
                            </li>
                        @endif
                        @if(Session::get('userHasMenu')->contains('Customer'))
                            <li class="nav-item">
                                <a class="nav-link menu-link {{ $title == 'Customer' ? 'active' : '' }}" href="{{ route('customer') }}">
                                    <i class="mdi mdi-account-group"></i> <span data-key="t-customer">Customer</span>
                                </a>
                            </li>
                        @endif
                    @endif

                    @if(Session::get('userHasMenu', collect())->intersect(['User', 'Storage'])->isNotEmpty())
                        <li class="menu-title"><span data-key="t-menu">Manajemen Gudang</span></li>
                        @if(Session::get('userHasMenu')->contains('User'))
                            <li class="nav-item">
                                <a class="nav-link menu-link {{ $title == 'User' ? 'active' : '' }}" href="{{ route('user.index') }}">
                                    <i class="mdi mdi-account-circle"></i> <span data-key="t-user"> User </span>
                                </a>
                            </li>
                        @endif

                        @if(Session::get('userHasMenu')->contains('Storage'))
                            <li class="nav-item">
                                <a class="nav-link menu-link {{ in_array($title, ['Storage Raw', 'Storage Area', 'Storage Rak', 'Storage Bin']) ? 'active' : '' }}" href="#sidebarStorage" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarStorage">
                                    <i class="mdi mdi-database"></i> <span data-key="t-dashboards">Storage</span>
                                </a>
                                <div class="collapse menu-dropdown {{ in_array($title, ['Storage Raw', 'Storage Area', 'Storage Rak', 'Storage Bin']) ? 'show' : '' }}" id="sidebarStorage">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="{{ route('storage.raw') }}" class="nav-link {{ $title == 'Storage Raw' ? 'active' : '' }}" data-key="t-analytics"> Area </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('storage.area') }}" class="nav-link {{ $title == 'Storage Area' ? 'active' : '' }}" data-key="t-analytics"> Raw </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('storage.rak') }}" class="nav-link {{ $title == 'Storage Rak' ? 'active' : '' }}" data-key="t-analytics"> Rak </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('storage.bin') }}" class="nav-link {{ $title == 'Storage Bin' ? 'active' : '' }}" data-key="t-analytics"> Bin </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        @endif
                    @endif
                </ul>
            </div>
        </div>

        <div class="sidebar-background"></div>
    </div>

    <div class="vertical-overlay"></div>

    <div class="main-content">

        <div class="page-content">
            <div class="container-fluid">

                @yield('content')

            </div>
        </div>

        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <script>document.write(new Date().getFullYear())</script> © Maximaz.
                    </div>
                    <div class="col-sm-6">
                        <div class="text-sm-end d-none d-sm-block">
                            Design & Develop by Trans Kargo Solusindo
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

</div>

<button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
    <i class="ri-arrow-up-line"></i>
</button>

<!-- JAVASCRIPT -->
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

@yield('js')

@if($msg = Session::get('success'))
    <script>
        Swal.fire({
            title: 'Success',
            text: '{{ $msg }}',
            icon: 'success'
        });
    </script>
@endif

@if($msg = Session::get('error'))
    <script>
        Swal.fire({
            title: 'Failed',
            text: '{{ $msg }}',
            icon: 'error'
        });
    </script>
@endif

</body>

</html>

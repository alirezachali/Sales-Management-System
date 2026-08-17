@extends('layouts.app')
@section('title', 'تنظیمات سیستم')
@section('content')

    <div class="container-fluid">

        <!-- Success Alert Section -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" title="یستن"></button>
            </div>
        @endif

        <!-- Error Alert Section -->
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" title="یستن"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">

            <div class="page-header d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold mb-1">
                        <i class="bi bi-gear-fill text-primary"></i>
                        تنظیمات سیستم
                    </h3>
                    <small class="text-muted">
                        مدیریت اطلاعات فروشگاه و تنظیمات نرم افزار
                    </small>
                </div>
                <button type="submit" form="settingsForm" class="btn btn-success">
                    <i class="bi bi-check-circle"></i>
                    ذخیره تغییرات
                </button>
            </div>

            <div class="card-body">

                <form id="settingsForm" action="{{ route('settings.update') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <ul class="nav nav-tabs mb-4" id="settingsTabs">

                        <li class="nav-item">
                            <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#store">
                                <i class="bi bi-shop"></i>
                                اطلاعات فروشگاه
                            </button>
                        </li>

                        <li class="nav-item">
                            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#sales">
                                <i class="bi bi-receipt"></i>
                                فروش
                            </button>
                        </li>

                        <li class="nav-item">
                            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#print">
                                <i class="bi bi-printer"></i>
                                چاپ
                            </button>
                        </li>

                        <li class="nav-item">
                            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#barcode">
                                <i class="bi bi-upc-scan"></i>
                                بارکد و لیبل
                            </button>
                        </li>

                        <li class="nav-item">
                            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#system">
                                <i class="bi bi-cpu"></i>
                                سیستم
                            </button>
                        </li>

                        <li class="nav-item">
                            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#backup">
                                <i class="bi bi-database"></i>
                                پشتیبان گیری
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">

                        <div class="tab-pane fade show active" id="store">
                            @include('settings.tabs.store')
                        </div>

                        <div class="tab-pane fade" id="sales">
                            @include('settings.tabs.sales')
                        </div>

                        <div class="tab-pane fade" id="print">
                            @include('settings.tabs.print')
                        </div>

                        <div class="tab-pane fade" id="barcode">
                            @include('settings.tabs.barcode')
                        </div>

                        <div class="tab-pane fade" id="system">
                            @include('settings.tabs.system')
                        </div>

                        <div class="tab-pane fade" id="backup">
                            @include('settings.tabs.backup')
                        </div>

                    </div>

                </form>

            </div>

            <hr>

        </div>
    </div>

@endsection
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // بازیابی آخرین تب باز شده
            const activeTab = localStorage.getItem('settings-tab');
            if (activeTab) {
                const trigger = document.querySelector(
                    `[data-bs-target="${activeTab}"]`
                );
                if (trigger) {
                    bootstrap.Tab.getOrCreateInstance(trigger).show();
                }
            }

            // ذخیره تب فعال
            document.querySelectorAll('#settingsTabs .nav-link').forEach(tab => {
                tab.addEventListener('shown.bs.tab', function() {
                    localStorage.setItem(
                        'settings-tab',
                        this.dataset.bsTarget
                    );
                });
            });

        });
    </script>
@endsection

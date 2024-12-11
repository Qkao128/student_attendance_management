@extends('layout/layout')

@section('page_title', 'Dashboard')

@section('style')
    <style>
        .dashboard-menu-button {
            text-decoration: none;
        }

        .dashboard-menu-button i {
            font-size: 40px;
        }

        .dashboard-menu-button {
            font-size: 25px;
        }

        @media(max-width: 768px) {
            .dashboard-menu-button i {
                font-size: 25px;
            }

            .dashboard-menu-button {
                font-size: 18px;
            }
        }
    </style>
@endsection

@section('content')

    <div>
        <ul class="breadcrumb text-muted mb-2">
            <li class="breadcrumb-item">
                Dashboard
            </li>
            <li class="breadcrumb-item">
                Home
            </li>
        </ul>
    </div>

    <div>
        <h4 class="header-title">Dashboard</h4>
    </div>

    <div class="mt-4">
        @livewire('dashboard-list')
    </div>

@endsection

@section('script')
    @stack('scripts')
@endsection

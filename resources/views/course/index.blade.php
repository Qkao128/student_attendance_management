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

    <div class="row mb-3">
        <div class="col">
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    Dashboard
                </li>
                <li class="breadcrumb-item">
                    Course Management
                </li>
            </ul>

        </div>
        <div class="col-12 col-md-auto">
            <div class="d-flex float-end gap-2">
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-6">
            <a class="dashboard-menu-button">
                <div class="card border-0 bg-white rounded-4 card-shadow card-hover">
                    <div class="card-body p-2 py-4 p-md-4 text-center">
                        <div>
                            <i class="fa-solid fa-user-group text-muted"></i>
                        </div>

                        <div class="mt-1 fw-bold text-muted">
                            vsdv
                        </div>
                    </div>
                </div>
            </a>
        </div>


    </div>
@endsection

@section('script')
    <script>
        $(function() {});
    </script>
@endsection

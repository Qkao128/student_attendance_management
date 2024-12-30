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

    <?php $filterDate = $filterDate ?? now()->format('Y-m-d'); ?>

    <div class="d-flex justify-content-between align-items-center mt-4"
        style="box-shadow: 0px 1px 2px RGBA(0, 0, 0, 0.25); border-radius: 10px;">
        <!-- 顯示當前日期 -->
        <div class="input-group">
            <span class="input-group-text" role="button" id="prevDate" style="background-color: #F4F6FA;">
                <i class="fa-solid fa-chevron-left"></i>
            </span>
            <input class="form-control text-center" type="date" id="filterDate" value="{{ now()->format('Y-m-d') }}"
                style="background-color: #F4F6FA;" />
            <span class="input-group-text" role="button" id="nextDate" style="background-color: #F4F6FA;">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
        </div>
    </div>

    @if ($isHoliday)
        <div class="alert alert-info mt-4">
            Today is a holiday !
        </div>
    @endif

    <div class="row g-3 mt-3">
        <div class="col-12 col-md-4">

            <div class="row g-3">
                <div class="col-12 col-sm-4 col-md-12">
                    <div class="card border-0 card-shadow px-1 h-100 mb-0">
                        <div class="card-body px-md-4">
                            <h5 class="mb-3">Class Attendance Summary :</h5>
                            <div class="justify-content-around d-flex align-items-center mt-4 mb-3">
                                <div class="ms-2 ms-sm-3 fs-5" id="classAttendanceSummary">
                                    {{ $dashboards['class_summary']['attended'] }} /
                                    {{ $dashboards['class_summary']['total'] }}
                                </div>
                                <div>
                                    <i class="fa-solid fa-users-rectangle" id="icon-class"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-4 col-md-12">
                    <div class="card border-0 card-shadow px-1 h-100 mb-0">
                        <div class="card-body px-md-4">
                            <h5 class="mb-3">Student Attendance Summary :</h5>
                            <div class="justify-content-around d-flex align-items-center mt-4 mb-3">
                                <div class="ms-2 ms-sm-3 fs-5" id="studentAttendanceSummary">
                                    {{ $dashboards['student_summary']['attended'] }} /
                                    {{ $dashboards['student_summary']['total'] }}
                                </div>
                                <div class="d-flex align-self-center">
                                    <i class="fa-solid fa-user-check" id="icon-student-check"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-4 col-md-12">
                    <div class="card border-0 card-shadow px-1 h-100 mb-0">
                        <div class="card-body px-md-4">
                            <h5 class="mb-3">Unavailable Students :</h5>
                            <div class="justify-content-around d-flex align-items-center mt-4 mb-3">
                                <div class="ms-3 ms-sm-4 fs-5" id="unavailableStudents">
                                    {{ $dashboards['student_summary']['unavailable'] }}
                                </div>
                                <div class="d-flex align-self-center ms-4">
                                    <i class="fa-solid fa-user-xmark" id="icon-student-xmark"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-12 col-md-8">
            <div class="card border-0 card-shadow px-1 h-100 mb-0">
                <div class="card-body px-md-4 my-3" id="today-attendance-statistics-content">
                    <h5 class="mb-3">Today Attendance Statistics :</h5>

                    <div class="text-center no-data-found-container mt-2 mt-md-5 p-2" style="display: none;">
                        <img class="no-data-found mt-2" src="{{ asset('img/no-data-found.png') }}">
                        <div class="mt-4 h5 text-muted">
                            No data found
                        </div>
                    </div>

                    <div class="w-100 mt-2 mt-md-5 mt-lg-4 d-flex align-self-center justify-content-center px-3"
                        id="today-attendance-statistics">
                        <canvas id="attendancePieChart"></canvas>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <div class="mt-4">
        @livewire('dashboard-list', ['date' => $filterDate ?? now()->format('Y-m-d')])
    </div>

@endsection

@section('script')
    <script>
        $(document).ready(function() {
            function updateDashboard(date) {
                $.ajax({
                    url: "{{ route('dashboard.data') }}",
                    method: "POST",
                    data: {
                        date: date,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        // 更新數據顯示
                        $("#classAttendanceSummary").text(
                            `${data.dashboards.class_summary.attended} / ${data.dashboards.class_summary.total}`
                        );
                        $("#studentAttendanceSummary").text(
                            `${data.dashboards.student_summary.attended} / ${data.dashboards.student_summary.total}`
                        );
                        $("#unavailableStudents").text(data.dashboards.student_summary.unavailable);

                        Livewire.dispatch('updateDate', {
                            date
                        });
                    },
                    error: function() {
                        alert('Failed to fetch data.');
                    }
                });
            }

            let attendancePieChart;

            // 動態設置圖例位置
            function getLegendPosition() {
                return window.innerWidth <= 1200 ? 'top' : 'left';
            }

            // 渲染圖表
            function renderPieChart(data) {
                const canvas = document.getElementById('attendancePieChart');
                const noDataContainer = document.querySelector('.no-data-found-container');

                const totalStudents = data.total_students || 0;
                const statusCounts = data.total_status_counts || {};

                const notSubmittedCount = totalStudents - Object.values(statusCounts).reduce((a, b) => a + b, 0);

                // 判斷是否所有數據都為 0
                const hasData = Object.values(statusCounts).some(value => value > 0) || notSubmittedCount > 0;

                if (!hasData) {
                    // 如果沒有數據，顯示 "No data found" 圖片，隱藏 canvas
                    noDataContainer.style.display = 'block'; // 顯示圖片
                    canvas.style.display = 'none'; // 隱藏 canvas
                    return; // 終止圖表渲染
                } else {
                    // 如果有數據，顯示 canvas，隱藏 "No data found" 圖片
                    noDataContainer.style.display = 'none'; // 隱藏圖片
                    canvas.style.display = 'block'; // 顯示 canvas
                }

                const ctx = canvas.getContext('2d');

                if (attendancePieChart) {
                    attendancePieChart.destroy(); // 銷毀舊圖表，重新渲染
                }

                attendancePieChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: [
                            'Present',
                            'Absence',
                            'Medical',
                            'Late',
                            'Leave Approval',
                            'Not Submitted'
                        ],
                        datasets: [{
                            data: [
                                statusCounts.Present || 0,
                                statusCounts.Absence || 0,
                                statusCounts.Medical || 0,
                                statusCounts.Late || 0,
                                statusCounts.LeaveApproval || 0,
                                notSubmittedCount || 0
                            ],
                            backgroundColor: [
                                '#32CD32', // Present
                                '#EE0000', // Absent
                                '#2222FF', // Medical
                                '#007777', // Late
                                '#000000', // Leave Approval
                                'rgba(211, 211, 211, 0.5)' // Not Submitted
                            ],
                            borderColor: [
                                '#32CD32',
                                '#EE0000',
                                '#2222FF',
                                '#007777',
                                '#000000',
                                'rgba(211, 211, 211, 0.5)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: getLegendPosition(),
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(tooltipItem) {
                                        const value = tooltipItem.raw;
                                        return `${tooltipItem.label}: ${value}`;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // 監聽視窗大小變化，重新渲染圖表
            window.addEventListener('resize', function() {
                if (attendancePieChart) {
                    attendancePieChart.options.plugins.legend.position = getLegendPosition();
                    attendancePieChart.update();
                }
            });

            // 初始化圖表數據
            function updatePieChart(date) {
                $.ajax({
                    url: "{{ route('dashboard.pieChartData') }}",
                    method: "POST",
                    data: {
                        date: date,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        renderPieChart(response.status_statistics);
                    },
                    error: function() {
                        alert('Failed to fetch chart data.');
                    }
                });
            }

            $("#filterDate").on("change", function() {
                const date = $(this).val();
                updateDashboard(date);
                updatePieChart(date);
            });

            $("#prevDate").on("click", function() {
                const currentDate = new Date($("#filterDate").val());
                currentDate.setDate(currentDate.getDate() - 1);
                const formattedDate = currentDate.toISOString().split('T')[0];
                $("#filterDate").val(formattedDate);
                updateDashboard(formattedDate);
                updatePieChart(formattedDate);
            });

            $("#nextDate").on("click", function() {
                const currentDate = new Date($("#filterDate").val());
                currentDate.setDate(currentDate.getDate() + 1);
                const formattedDate = currentDate.toISOString().split('T')[0];
                $("#filterDate").val(formattedDate);
                updateDashboard(formattedDate);
                updatePieChart(formattedDate);
            });

            const today = new Date().toISOString().split('T')[0];
            $("#filterDate").val(today);
            updateDashboard(today);
            updatePieChart(today);

        });
    </script>

    @stack('scripts')
@endsection

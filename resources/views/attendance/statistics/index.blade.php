@php
    use Carbon\Carbon;
@endphp

@extends('layout/layout')

@section('page_title', 'Attendance_Statistics')

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
                Attendance Statistics
            </li>
        </ul>
    </div>

    @php
        $today = Carbon::today()->toDateString();
        $formattedDate = Carbon::parse($today)->format('d F Y');
    @endphp

    <div>
        <h4 class="header-title">Attendance Statistics</h4>
    </div>

    <div class="mt-4">
        <h5 class="header-title">Today Statistics (<span
                class="fst-italic text-decoration-underline">{{ $formattedDate }}</span> ) :</h5>
    </div>


    <div class="d-flex justify-content-between align-items-center mt-4 visually-hidden"
        style="box-shadow: 0px 1px 2px RGBA(0, 0, 0, 0.25); border-radius: 10px;">
        <!-- 顯示當前日期 -->
        <div class="input-group">
            <input class="form-control text-center" type="date" id="filterDate" value="{{ now()->format('Y-m-d') }}"
                style="background-color: #F4F6FA;" readonly />
        </div>
    </div>

    <div id="holidayStatusContainer"></div>

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
                                <div class="ms-3 ms-sm-4 fs-5" id="unavailableMonMonthlyStudents">
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

                    <div class="text-center no-data-found-today-container mt-2 mt-md-5 p-2" style="display: none;">
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

    <div>
        <h5 class="header-title mt-5">Attendance Statistics by Month :</h5>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-5 mb-2"
        style="box-shadow: 0px 1px 2px RGBA(0, 0, 0, 0.25); border-radius: 10px;">
        <div class="input-group">
            <span class="input-group-text" role="button" id="prevMonth" style="background-color: #F4F6FA;">
                <i class="fa-solid fa-chevron-left"></i>
            </span>
            <input class="form-control text-center" type="month" id="filterMonth" style="background-color: #F4F6FA;"
                onclick="this.showPicker()" />

            <span class="input-group-text" role="button" id="nextMonth" style="background-color: #F4F6FA;">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
        </div>
    </div>


    <div class="row g-3 mt-2">
        <div class="col-12 col-md-8">
            <div class="card border-0 card-shadow px-1 h-100 mb-0">
                <div class="card-body pb-0">
                    <div class="row g-3">
                        <div class="col-12 col-md align-self-center mt-0">
                            Attendance Status by Course:
                        </div>
                        <div class="col-12 col-md-auto d-flex justify-content-end align-items-end">
                            <div class="form-group mb-4">
                                <select class="form-select" id="course_id" name="course_id" required style="width: 250px">
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-5 px-2" id="no-data-container" style="display: none; text-align: center;">
                        <img class="no-data-found mt-3" src="{{ asset('img/no-data-found.png') }}">
                        <div class="h5 mt-3 text-muted">
                            No data found
                        </div>
                    </div>

                    <div class="chart-container ps-3 mb-5">
                        <!-- Chart container -->
                        <div class="w-100 d-flex align-self-center justify-content-center px-3"
                            id="monthly-attendance-statistics" style="display: none;">
                            <canvas id="attendanceDonutChart"></canvas>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="row g-3">
                <div class="col-12 col-sm-4 col-md-12">
                    <div class="card border-0 card-shadow px-1 h-100 mb-0">
                        <div class="card-body px-md-4">
                            <h5 class="mb-3">Class Attendance Summary :</h5>
                            <div class="justify-content-around d-flex align-items-center mt-4 mb-3">
                                <div class="ms-2 ms-sm-3 fs-5 text-center" id="classAttendanceSummary">
                                    <span id="totalClasses">0</span>
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
                                <div class="ms-2 ms-sm-3 fs-5 text-center" id="studentAttendanceSummary">
                                    <span id="presentQuantity">0</span>
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
                                <div class="ms-3 ms-sm-4 fs-5 text-center" id="unavailableMonMonthlyStudents">
                                    <span id="unavailableMonthlyStudents">0</span>
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

        <div>
            <h4 class="header-title my-4">Summary of Data for All Classes</h4>
        </div>

        <div>
            @livewire('attendance-statistics-class-list', ['userId' => Auth()->id()])
        </div>


    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            function updateDashboard() {
                $.ajax({
                    url: "{{ route('dashboard.data') }}",
                    method: "POST",
                    data: {
                        date: "{{ now()->format('Y-m-d') }}",
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
                        $("#unavailableMonMonthlyStudents").text(data.dashboards.student_summary
                            .unavailable);

                        Livewire.dispatch('updateDate', {
                            date: "{{ now()->format('Y-m-d') }}"
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
                const noDataContainer = document.querySelector('.no-data-found-today-container');

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

                if (attendanceDonutChart) {
                    attendanceDonutChart.options.plugins.legend.position = getLegendPosition();
                    attendanceDonutChart.update();
                }
            });

            // 初始化圖表數據
            function updatePieChart() {
                $.ajax({
                    url: "{{ route('dashboard.pieChartData') }}",
                    method: "POST",
                    data: {
                        date: "{{ now()->format('Y-m-d') }}",
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

            updateDashboard();
            updatePieChart();


            // 本月
            $("#course_id").select2({
                theme: 'bootstrap-5',
                allowClear: true,
                placeholder: 'Select course',
                ajax: {
                    url: "{{ route('course.select_search') }}", // 课程搜索的接口
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search_term: params.term, // 搜索词
                            page: params.page,
                            _token: "{{ csrf_token() }}" // CSRF Token
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data.results, function(item) {
                                return {
                                    text: item.name,
                                    id: item.id
                                };
                            }),
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    }
                }
            });

            $("#course_id").on('change', function() {
                const courseId = $(this).val();
                Livewire.dispatch('updateCourse', {
                    courseId
                });
            });

            // 渲染 Donut Chart
            let attendanceDonutChart;

            // 渲染 Donut Chart
            function renderDonutChart(data) {
                const canvasMonthly = document.getElementById('attendanceDonutChart');
                const noDataMonthlyContainer = document.querySelector('#no-data-container');

                const totalStudents = data.total_students || 0;
                const statusCounts = data.total_status_counts || {};

                const hasData = Object.values(statusCounts).some(value => value > 0);

                if (!hasData) {
                    noDataMonthlyContainer.style.display = 'block';
                    canvasMonthly.style.display = 'none';
                    return;
                } else {
                    noDataMonthlyContainer.style.display = 'none';
                    canvasMonthly.style.display = 'block';
                }

                const ctx = canvasMonthly.getContext('2d');

                if (attendanceDonutChart) {
                    attendanceDonutChart.destroy();
                }

                attendanceDonutChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Present', 'Absence', 'Late', 'Medical', 'Leave Approval'],
                        datasets: [{
                            data: [
                                statusCounts.Present || 0,
                                statusCounts.Absence || 0,
                                statusCounts.Late || 0,
                                statusCounts.Medical || 0,
                                statusCounts.LeaveApproval || 0
                            ],
                            backgroundColor: ['#32CD32', '#EE0000', '#007777', '#2222FF',
                                '#000000'
                            ],
                            borderColor: ['#32CD32', '#EE0000', '#007777', '#2222FF', '#000000'],
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

            // 更新 Donut Chart 數據
            function updateDonutChart(month, courseId) {
                $.ajax({
                    url: "{{ route('attendance_statistics.pieMonthlyChartData') }}",
                    method: "POST",
                    data: {
                        month: month,
                        course_id: courseId || null,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        const data = response.data;

                        // 更新 Donut Chart
                        renderDonutChart(data.status_statistics);
                        // 更新统计数据
                        $("#presentQuantity").text(data
                            .present_quantity); // 包括 Present, Late, LeaveApproval
                        $("#unavailableMonthlyStudents").text(data.unavailable_students);
                        $("#totalClasses").text(data.total_classes);
                    },
                    error: function() {
                        alert('Failed to fetch chart data.');
                    }
                });
            }


            // 月份選擇器切換事件
            $("#filterMonth").on("change", function() {
                const month = $(this).val();
                const courseId = $("#course_id").val();

                // 更新 Donut Chart
                updateDonutChart(month, courseId);

                // 發送到 Livewire
                Livewire.dispatch('updateMonth', {
                    month,
                });
            });

            $("#prevMonth").on("click", function() {
                const currentDate = new Date($("#filterMonth").val());
                currentDate.setMonth(currentDate.getMonth() - 1);
                const formattedMonth = currentDate.toISOString().split("T")[0].slice(0, 7);
                $("#filterMonth").val(formattedMonth);
                const courseId = $("#course_id").val();

                // 更新 Donut Chart
                updateDonutChart(formattedMonth, courseId);

                // 發送到 Livewire
                Livewire.dispatch('updateMonth', {
                    month: formattedMonth,
                });
            });

            $("#nextMonth").on("click", function() {
                const currentDate = new Date($("#filterMonth").val());
                currentDate.setMonth(currentDate.getMonth() + 1);
                const formattedMonth = currentDate.toISOString().split("T")[0].slice(0, 7);
                $("#filterMonth").val(formattedMonth);
                const courseId = $("#course_id").val();

                // 更新 Donut Chart
                updateDonutChart(formattedMonth, courseId);

                // 發送到 Livewire
                Livewire.dispatch('updateMonth', {
                    month: formattedMonth,
                });
            });

            $("#course_id").on("change", function() {
                const month = $("#filterMonth").val();
                const courseId = $(this).val();
                updateDonutChart(month, courseId);
            });

            const currentMonth = new Date().toISOString().split("T")[0].slice(0, 7);
            $("#filterMonth").val(currentMonth);

            // 初始化時觸發更新 Donut Chart
            updateDonutChart(currentMonth, null);

            // 初始化時發送到 Livewire
            Livewire.dispatch('updateMonth', {
                month: currentMonth,
            });

            function fetchHolidayStatus(date) {
                $.ajax({
                    url: '{{ route('dashboard.isHoliday') }}',
                    method: "POST",
                    data: {
                        date: date,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#holidayStatusContainer').empty();
                        if (response.items.length > 0) {
                            response.items.forEach(function(item) {
                                const borderColor = item.background_color;
                                const bgColor = hexToRgba(borderColor, 0.1); // 使用 10% 不透明度
                                const alertType = item.is_holidays ? 'alert-success' :
                                    'alert-info';
                                const alertHtml = `
                                    <div class="alert ${alertType} mt-4" style="border-color: ${borderColor}; background-color: ${bgColor};">
                                        Today is <strong>${item.title}</strong> !
                                    </div>
                                `;
                                $('#holidayStatusContainer').append(alertHtml);
                            });
                        }
                    }
                });
            }

            // 將十六進制顏色轉換為 RGBA
            function hexToRgba(hex, opacity) {
                const bigint = parseInt(hex.replace('#', ''), 16);
                const r = (bigint >> 16) & 255;
                const g = (bigint >> 8) & 255;
                const b = bigint & 255;
                return `rgba(${r}, ${g}, ${b}, ${opacity})`;
            }

            const today = "{{ Carbon::now()->format('Y-m-d') }}";
            fetchHolidayStatus(today);

        });
    </script>

    @stack('scripts')
@endsection

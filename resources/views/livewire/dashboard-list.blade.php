@php
    use Carbon\Carbon;
    use App\Enums\Status;
@endphp

<div>
    <div class="d-flex justify-content-between align-items-center"
        style=" box-shadow: 0px 4px 2px RGBA(0, 0, 0, 0.25); border-radius: 10px;">
        <!-- 顯示當前日期 -->
        <div class="input-group">
            <span class="input-group-text" role="button" wire:click="changeDate(false)" style="background-color: #F4F6FA;">
                <i class="fa-solid fa-chevron-left"></i>
            </span>
            <input class="form-control text-center" type="date" wire:model.live="filter.date"
                wire:change="applyFilter()" style="background-color: #F4F6FA;" onchange="updatePieChart()"
                onclick="this.showPicker()" />

            <span class="input-group-text" role="button" wire:click="changeDate(true)"
                style="background-color: #F4F6FA;">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
        </div>
    </div>


    <div class="row g-3 mt-3">
        <div class="col-12 col-md-4">

            <div class="row g-3">
                <div class="col-12 col-sm-4 col-md-12">
                    <div class="card border-0 card-shadow px-1 h-100 mb-0">
                        <div class="card-body px-md-4">
                            <h5 class="mb-3">Class Attendance Summary :</h5>
                            <div class="justify-content-around d-flex align-items-center mt-4 mb-3">
                                <div class="ms-2 ms-sm-3 fs-5">
                                    @if ($dashboards['class_summary']['attended'] == 0 && $dashboards['class_summary']['total'] == 0)
                                        {{ '-' }}
                                    @else
                                        {{ $dashboards['class_summary']['attended'] ?? '0' }} /
                                        {{ $dashboards['class_summary']['total'] }}
                                    @endif
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
                                <div class="ms-2 ms-sm-3 fs-5">
                                    @if ($dashboards['student_summary']['attended'] == 0 && $dashboards['student_summary']['total'] == 0)
                                        {{ '-' }}
                                    @else
                                        {{ $dashboards['student_summary']['attended'] ?? '0' }} /
                                        {{ $dashboards['student_summary']['total'] }}
                                    @endif
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
                                <div class="ms-3 ms-sm-4 fs-5">

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
                    <div class="w-100 d-flex align-self-center justify-content-center px-3"
                        id="today-attendance-statistics">
                        <canvas id="attendancePieChart"></canvas>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="mt-5">
        <h4 class="header-title">Attendance Submitted Classes :</h4>
    </div>

    <div class="search-input-group mt-4">
        <div class="search-input-icon">
            <i class="fa fa-search"></i>
        </div>
        <input type="text" class="form-control search-input" placeholder="Search class"
            wire:keydown.debounce.250ms="filterClass($event.target.value)" wire:model="filter.class">
    </div>


    <div class="row g-4 mt-1">
        @foreach ($attendances as $attendance)
            <div class="col-12">
                <div class="card border-0 card-shadow px-1">
                    <div class="card-body px-md-4">
                        <div class="row g-2 g-md-3 align-items-center">

                            <div class="col-12 col-sm">
                                <div class="row gap-2 d-md-block">
                                    <div class="col-12">
                                        {{ $attendance['class_name'] }}
                                    </div>

                                    <div class="col-12 text-muted mt-1">
                                        {{ $attendance['course_name'] }}
                                    </div>
                                </div>
                            </div>

                            <hr class="d-block d-sm-none">

                            <div class="col-12 col-sm">
                                <div class="row gap-2 d-md-block">
                                    <div class="col-12">
                                        Member :
                                    </div>

                                    <div class="col-12 mt-1 text-muted">
                                        @if ($attendance['attendance_summary']['student_count'] == 0)
                                            <span class="badge bg-primary">
                                                {{ $attendance['attendance_summary']['arrived_count'] ?? '0' }}
                                            </span>
                                        @else
                                            <span class="badge bg-primary">
                                                {{ $attendance['attendance_summary']['arrived_count'] ?? '0' }} /
                                                {{ $attendance['attendance_summary']['student_count'] }}
                                            </span>
                                        @endif

                                        <i class="fa fa-user ms-2"></i>
                                    </div>

                                </div>
                            </div>

                            <hr class="d-block d-sm-none">

                            <div class="col-12 col-sm">
                                <div class="row gap-2 d-md-block">
                                    <div class="col-12">
                                        Updated At :
                                    </div>

                                    <div class="col-12 mt-1">
                                        {{ $attendance['attendance_summary']['latest_attendance_time'] ?? '-' }}
                                    </div>
                                </div>
                            </div>


                            <div class="col-12 col-md-auto text-end ms-3 mt-2 mt-md-0">
                                @if (!empty($attendance['attendance_summary']))
                                    <span class="badge bg-success p-2">Submitted</span>
                                @else
                                    <span class="badge bg-danger p-2">Not Submitted</span>
                                @endif
                            </div>
                        </div>

                        @if (!empty($attendance['attendance_summary']['status_counts']))
                            <hr class="my-3">
                            <div class="row">
                                <div class="col-12 col-sm">
                                    <div class="row g-3">
                                        <!-- 如果有出勤的學生數量 -->
                                        @if (($attendance['attendance_summary']['status_counts'][Status::Present()->key] ?? 0) > 0)
                                            <div class="col-auto me-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="status-icon rounded-circle border-present">
                                                        P
                                                    </div>
                                                    <p class="card-text ms-2">
                                                        <span
                                                            class="count">{{ $attendance['attendance_summary']['status_counts'][Status::Present()->key] }}</span>
                                                        <i class="fa fa-user ms-2"></i>
                                                    </p>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- 如果有缺席的學生數量 -->
                                        @if (($attendance['attendance_summary']['status_counts'][Status::Absence()->key] ?? 0) > 0)
                                            <div class="col-auto me-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="status-icon rounded-circle border-absence">
                                                        A
                                                    </div>
                                                    <p class="card-text ms-2">
                                                        <span
                                                            class="count">{{ $attendance['attendance_summary']['status_counts'][Status::Absence()->key] }}</span>
                                                        <i class="fa fa-user ms-2"></i>
                                                    </p>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- 如果有遲到的學生數量 -->
                                        @if (($attendance['attendance_summary']['status_counts'][Status::Late()->key] ?? 0) > 0)
                                            <div class="col-auto me-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="status-icon rounded-circle border-late">
                                                        L
                                                    </div>
                                                    <p class="card-text ms-2">
                                                        <span
                                                            class="count">{{ $attendance['attendance_summary']['status_counts'][Status::Late()->key] }}</span>
                                                        <i class="fa fa-user ms-2"></i>
                                                    </p>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- 如果有醫假學生數量 -->
                                        @if (($attendance['attendance_summary']['status_counts'][Status::Medical()->key] ?? 0) > 0)
                                            <div class="col-auto me-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="status-icon rounded-circle border-medical">
                                                        MC
                                                    </div>
                                                    <p class="card-text ms-2">
                                                        <span
                                                            class="count">{{ $attendance['attendance_summary']['status_counts'][Status::Medical()->key] }}</span>
                                                        <i class="fa fa-user ms-2"></i>
                                                    </p>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- 如果有批准假期的學生數量 -->
                                        @if (($attendance['attendance_summary']['status_counts'][Status::LeaveApproval()->key] ?? 0) > 0)
                                            <div class="col-auto me-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="status-icon rounded-circle border-leave-approval">
                                                        AP
                                                    </div>
                                                    <p class="card-text ms-2">
                                                        <span
                                                            class="count">{{ $attendance['attendance_summary']['status_counts'][Status::LeaveApproval()->key] }}</span>
                                                        <i class="fa fa-user ms-2"></i>
                                                    </p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-12 col-sm-auto">
                                    <div class="d-flex justify-content-end mt-4 mt-sm-3">
                                        <label class="text-muted">Teacher :</label>
                                        <span class="ms-2 ms-md-4 me-2 me-sm-4"
                                            style="margin-top: -1px;">{{ $attendance['teacher_name'] }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif


                    </div>
                </div>
            </div>

            <input type="hidden" wire:model="filter.user_id">
            <input type="hidden" wire:model="filter.course_id">
        @endforeach
    </div>

    <div class="d-grid mt-4">
        <div x-intersect.full="$wire.loadMore()">
        </div>

        <div wire:loading>
            <div class="d-flex justify-content-center">
                <div class="more-loader-pulse-container">
                    <div class="more-loader-pulse-bubble more-loader-pulse-bubble-1"></div>
                    <div class="more-loader-pulse-bubble more-loader-pulse-bubble-2"></div>
                    <div class="more-loader-pulse-bubble more-loader-pulse-bubble-3"></div>
                </div>
            </div>
        </div>

        @if (count($attendances) === 0)
            <div class="text-center" wire:loading.remove>
                <img class="no-data-found mt-2" src="{{ asset('img/no-data-found.png') }}">
                <div class="mt-4 h5 text-muted">
                    No data found
                </div>
            </div>
        @endif
    </div>

</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            let attendancePieChart;

            function renderPieChart(data) {
                const ctx = document.getElementById('attendancePieChart').getContext('2d');

                if (attendancePieChart) {
                    attendancePieChart.destroy();
                }

                // 根據螢幕寬度決定圖例位置
                const legendPosition = window.innerWidth <= 1200 ? 'top' : 'left';

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
                                data.total_status_counts.Present || 0,
                                data.total_status_counts.Absence || 0,
                                data.total_status_counts.Medical || 0,
                                data.total_status_counts.Late || 0,
                                data.total_status_counts.LeaveApproval || 0,
                                {{ $dashboards['student_summary']['total'] }} - Object.values(
                                    data.total_status_counts).reduce((a, b) => a + b, 0)
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
                        plugins: {
                            legend: {
                                position: legendPosition,
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

            // 初始渲染
            renderPieChart(@json($dashboards['status_statistics']));

            // 監聽視窗大小變化並重新渲染
            $(window).resize(function() {
                renderPieChart(@json($dashboards['status_statistics']));
            });

            // 監聽 Livewire 派發的事件
            window.addEventListener('update-pie-chart', event => {
                renderPieChart(event.detail.statusStatistics);
            });

            function updatePieChart() {
                @this.dispatch('update-pie-chart');
            }
        });
    </script>
@endpush

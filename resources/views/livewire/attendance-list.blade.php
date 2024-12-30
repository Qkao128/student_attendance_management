@php
    use Carbon\Carbon;
    use App\Enums\Status;
@endphp

<div id="attendance-list">

    @if ($isHoliday)
        <div class="alert alert-info mt-3 mb-1">
            Today is a holiday !
        </div>
    @endif

    <div class="row align-items-center g-3 mt-1">
        <div class="col-12 col-sm-5">
            <div class="d-flex justify-content-between align-items-center"
                style=" box-shadow: 0px 4px 2px RGBA(0, 0, 0, 0.25); border-radius: 10px;">
                <!-- 顯示當前日期 -->
                <div class="input-group">
                    <span class="input-group-text" role="button" wire:click="changeDate(false)"
                        style="background-color: #F4F6FA;">
                        <i class="fa-solid fa-chevron-left"></i>
                    </span>
                    <input class="form-control text-center" type="date" wire:model="filter.date"
                        wire:change="applyFilter()" style="background-color: #F4F6FA;" onclick="this.showPicker()">
                    <span class="input-group-text" role="button" wire:click="changeDate(true)"
                        style="background-color: #F4F6FA;">
                        <i class="fa-solid fa-chevron-right"></i>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-10 col-sm">
            <div class="search-input-group">
                <div class="search-input-icon">
                    <i class="fa fa-search"></i>
                </div>
                <input type="text" class="form-control search-input" placeholder="Search class"
                    wire:keydown.debounce.250ms="filterClass($event.target.value)" wire:model="filter.class">
            </div>
        </div>

        <div class="col-2 col-sm-auto">
            <button type="button" class="btn btn-link text-secondary" onclick="toggleFilter('#filter')">
                <i class="fa-solid fa-filter"></i>
            </button>
        </div>
    </div>


    <div id="filter" class="filter-popup-wraper d-none">
        <div class="filter-popup-content">
            <form wire:submit.prevent="applyFilter" id='filter-form'>
                <div class="filter-popup-body">
                    <h3 class="fw-bold text-center">Filter</h3>

                    <button type="button" class="btn btn-link text-dark filter-popup-close-btn p-0"
                        onclick="toggleFilter('#filter')">
                        <i class="fa-solid fa-xmark"></i>
                    </button>

                    <div class="row mt-3">
                        <div class="col-12 col-md-6">
                            <div class="form-group mb-3" wire:ignore>
                                <label class="form-label" for="filter_course_id">Course</label>
                                <select class="form-select" id="filter_course_id" style="width:100%;">
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="form-group mb-3" wire:ignore>
                                <label class="form-label" for="filter_user_id">Teacher</label>
                                <select class="form-select" id="filter_user_id" wire:ignore style="width:100%;">
                                </select>
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label" for="filter-class">Class</label>
                                <input type="text" class="form-control" id="filter-class" wire:model="filter.class"
                                    placeholder="Enter class">
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label" for="filter-class">Date</label>
                                <input class="form-control" type="date" wire:model="filter.date"
                                    style="background-color: #F4F6FA;" onclick="this.showPicker()">
                            </div>
                        </div>


                    </div>
                </div>

                <div class="filter-popup-footer">
                    <div class="row g-2 p-3">
                        <div class="col-4 col-lg-6">
                            <button type="button" class="btn btn-danger btn-lg w-100" wire:click="resetFilter()"
                                onclick="toggleFilter('#filter')">
                                Reset
                            </button>
                        </div>
                        <div class="col-8 col-lg-6">
                            <button type="submit" class="btn btn-primary btn-lg w-100"
                                onclick="toggleFilter('#filter')">
                                Filter
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <h5 class="my-3">
        <span
            class="badge text-black fw-normal {{ $filter['is_submitted'] === true ? 'border' : '' }} {{ $filter['is_submitted'] === true ? 'text-white' : '' }}"
            wire:click="updateSubmittedStatus(true)" role="button"
            style="background-color: {{ $filter['is_submitted'] === true ? '#007bff' : '#F4F6FA' }};box-shadow: 0px 4px 2px RGBA(0, 0, 0, 0.25); border-radius: 10px;">
            Submitted
        </span>

        <span
            class="badge text-black fw-normal {{ $filter['is_submitted'] === true ? 'border' : '' }} ms-sm-2  {{ $filter['is_submitted'] === false ? 'text-white' : '' }}"
            wire:click="updateSubmittedStatus(false)" role="button"
            style="background-color: {{ $filter['is_submitted'] === false ? '#007bff' : '#F4F6FA' }};box-shadow: 0px 4px 2px RGBA(0, 0, 0, 0.25); border-radius: 10px;">
            Not Submitted
        </span>

        <span
            class="badge text-black fw-normal {{ $filter['is_user'] === true ? 'border' : '' }} mt-1 mt-sm-0 ms-sm-2  {{ $filter['is_user'] === true ? 'text-white' : '' }}"
            wire:click="filterByCurrentUser" role="button"
            style="background-color: {{ $filter['is_user'] === true ? '#007bff' : '#F4F6FA' }};box-shadow: 0px 4px 2px RGBA(0, 0, 0, 0.25); border-radius: 10px;">
            My Classes
        </span>
    </h5>

    <div class="row g-4">
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

                            <hr class="d-block d-sm-none">

                            <div class="col-12 col-md-auto">
                                <div class="row gap-2 d-md-block">
                                    <div class="col-12">
                                        Action :
                                    </div>

                                    <div class="col-12 mt-1">
                                        <div class="d-inline-flex gap-3">
                                            <a href="{{ route('attendance.show', ['id' => $attendance['class_id'], 'date' => $filter['date'] ?? now()->format('Y-m-d')]) }}"
                                                class="btn btn-info rounded-4">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-auto text-end ms-3 mt-2 mt-md-0">
                                @if ($attendance['attendance_summary']['has_attendance'])
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
        $('#filter_course_id').select2({
            theme: 'bootstrap-5',
            allowClear: true,
            placeholder: 'Select course',
            dropdownParent: $('#filter'),
            ajax: {
                url: "{{ route('course.select_search') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search_term: params.term,
                        page: params.page,
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
        }).on('change', function(e) {
            var selectedCourseId = $(this).val();
            @this.set('filter.course_id', selectedCourseId, false);
        });


        $('#filter_user_id').select2({
            theme: 'bootstrap-5',
            allowClear: true,
            placeholder: 'Select user',
            dropdownParent: $('#filter'),
            ajax: {
                url: "{{ route('user.select_search') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search_term: params.term,
                        page: params.page,
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data.results, function(item) {
                            return {
                                text: item.username,
                                id: item.id
                            };
                        }),
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                }
            }
        }).on('change', function(e) {
            var selectedUserId = $(this).val();
            @this.set('filter.user_id', selectedUserId, false); // 僅更新 Livewire 的 user_id
        });

        function updateFilterUserId(userId) {
            const filterUserIdElement = $('#filter_user_id');
            filterUserIdElement.val(userId).trigger('change');
        }
    </script>
@endpush

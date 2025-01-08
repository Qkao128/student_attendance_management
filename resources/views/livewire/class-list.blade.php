@php
    use Carbon\Carbon;
@endphp

<div id="course-list">
    <div class="row align-items-center g-0 mt-3">
        <div class="col">
            <div class="search-input-group">
                <div class="search-input-icon">
                    <i class="fa fa-search"></i>
                </div>
                <input type="text" class="form-control search-input" placeholder="Search class"
                    wire:keydown.debounce.250ms="filterClass($event.target.value)" wire:model="filter.class">
            </div>
        </div>

        @hasrole('SuperAdmin')
            <div class="col-auto">
                <button type="button" class="btn btn-link text-secondary" onclick="toggleFilter('#filter')">
                    <i class="fa-solid fa-filter"></i>
                </button>
            </div>
        @endhasrole
    </div>


    @hasrole('SuperAdmin')
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
                                    <select class="form-select" id="filter_user_id" style="width:100%;">
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
                                    <label class="form-label" for="filter-is_disabled">Status</label>
                                    <select class="form-control form-select" id="filter-is_disabled"
                                        wire:model="filter.is_disabled">
                                        <option value="" hidden>Please select</option>
                                        <option value="0">Active</option>
                                        <option value="1">Disabled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="filter-popup-footer">
                        <div class="row g-2 p-3">
                            <div class="col-4 col-lg-6">
                                <button type="button" class="btn btn-danger text-white rounded-4 btn-lg w-100"
                                    wire:click="resetFilter()" onclick="toggleFilter('#filter')">
                                    Reset
                                </button>
                            </div>
                            <div class="col-8 col-lg-6">
                                <button type="submit" class="btn btn-primary text-white rounded-4 btn-lg w-100"
                                    onclick="toggleFilter('#filter')">
                                    Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <h5 class="d-flex mt-3">
            <span class="badge text-black fw-normal {{ $filter['is_disabled'] === null ? 'border text-white' : '' }}"
                wire:click="updateDisabledStatus(null)" role="button"
                style="background-color: {{ $filter['is_disabled'] === null ? '#007bff' : '#F4F6FA' }}; box-shadow: 0px 4px 2px rgba(0, 0, 0, 0.25); border-radius: 10px;">
                All
            </span>

            <span class="badge text-black fw-normal ms-2 {{ $filter['is_disabled'] === false ? 'border text-white' : '' }}"
                wire:click="updateDisabledStatus(false)" role="button"
                style="background-color: {{ $filter['is_disabled'] === false ? '#007bff' : '#F4F6FA' }}; box-shadow: 0px 4px 2px rgba(0, 0, 0, 0.25); border-radius: 10px;">
                Active
            </span>

            <span class="badge text-black fw-normal ms-2 {{ $filter['is_disabled'] === true ? 'border text-white' : '' }}"
                wire:click="updateDisabledStatus(true)" role="button"
                style="background-color: {{ $filter['is_disabled'] === true ? '#007bff' : '#F4F6FA' }}; box-shadow: 0px 4px 2px rgba(0, 0, 0, 0.25); border-radius: 10px;">
                Disabled
            </span>

            <span class="badge text-black fw-normal ms-2 {{ $filter['user_id'] === $userId ? 'border text-white' : '' }}"
                wire:click="filterByCurrentUser" role="button"
                style="background-color: {{ $filter['user_id'] === $userId ? '#007bff' : '#F4F6FA' }}; box-shadow: 0px 4px 2px rgba(0, 0, 0, 0.25); border-radius: 10px;">
                My Classes
            </span>
        </h5>
    @endhasrole


    <div class="row g-4 mt-2">
        @foreach ($classes as $class)
            <div class="col-12">
                <div class="card border-0 card-shadow px-1"
                    style="background-color: {{ $class->is_disabled ? '#e8e8e8' : '#F4F6FA' }};">
                    <div class="card-body px-md-4">
                        <div class="row g-2 g-md-3 align-items-center">

                            <div class="col-12 col-sm">
                                <div class="row gap-2 d-md-block">
                                    <div class="col-12 text-muted">
                                        Name :
                                    </div>

                                    <div class="col-12 mt-1">
                                        {{ $class->name }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm">
                                <div class="row gap-2 d-md-block">
                                    <div class="col-12 text-muted">
                                        Member :
                                    </div>

                                    <div class="col-12 mt-1">
                                        <span class="badge bg-primary">{{ $class->member_count ?? 0 }}</span>
                                        <i class="fa-solid fa-user ms-2"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm">
                                <div class="row gap-2 d-md-block">
                                    <div class="col-12 text-muted">
                                        Created At :
                                    </div>

                                    <div class="col-12 mt-1">
                                        {{ Carbon::parse($class->created_at)->format('d-m-Y h:i A') }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-auto">
                                <div class="row gap-2 d-md-block">
                                    <div class="col-12 text-muted">
                                        Action :
                                    </div>

                                    <div class="col-12 mt-1">
                                        <div class="d-inline-flex gap-3">
                                            <a href="{{ route('class.show', ['id' => $class->id]) }}"
                                                class="btn btn-info rounded-4">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>

                                            @hasrole('Admin')
                                                <!-- 如果是 Admin，這段代碼會被隱藏 -->
                                            @else
                                                <a href="{{ route('class.edit', ['id' => $class->id]) }}"
                                                    class="btn btn-warning text-dark">
                                                    <i class="fa-solid fa-pen-nib"></i>
                                                </a>
                                            @endhasrole

                                            <form method="POST" action="{{ route('class.destroy', $class->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger"
                                                    onclick="deleteFormConfirmation(event)">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="mb-0">

                            <div class="col-12 col-md">
                                <div class="row gap-2 d-md-block">
                                    <div class="col-12 text-muted">
                                        Course :
                                    </div>

                                    <div class="col-12 mt-1">
                                        {{ $class->course_name }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md">
                                <div class="row gap-2 d-md-block">
                                    <div class="col-12 text-muted">
                                        Teacher :
                                    </div>

                                    <div class="col-12 mt-1">
                                        {{ $class->user_name }}
                                    </div>
                                </div>
                            </div>

                            @if ($class->is_disabled)
                                <div class="col-auto ms-auto text-end">
                                    <div class="badge bg-danger">
                                        Disabled
                                    </div>
                                </div>
                            @else
                                <div class="col-auto ms-auto text-end">
                                    <div class="badge bg-success">
                                        Active
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
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

        @if (count($classes) === 0)
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
            @this.set('filter.user_id', selectedUserId, false);
        });
    </script>
@endpush

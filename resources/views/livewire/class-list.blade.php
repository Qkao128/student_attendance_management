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

        <div class="col-auto">
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
                                <label class="form-label" for="filter-user_id">Teacher</label>
                                <select class="form-select" id="filter-user_id" style="width:100%;">
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="form-group mb-3" wire:ignore>
                                <label class="form-label" for="filter-course_id">Course</label>
                                <select class="form-select" id="filter-course_id" style="width:100%;">
                                </select>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label class="form-label" for="filter-class">Class</label>
                                <input type="text" class="form-control" id="filter-class" wire:model="filter.class"
                                    placeholder="Enter class">
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



    <div class="row g-4 mt-2">
        @foreach ($classes as $class)
            <div class="col-12">
                <div class="card border-0 card-shadow px-1">
                    <div class="card-body px-md-4">
                        <div class="row g-2 g-md-3 align-items-center">

                            <div class="col-12 col-sm">
                                <div class="row gap-2 d-md-block">
                                    <div class="col-12 text-muted">
                                        Name :
                                    </div>

                                    <div class="col-12 mt-1">
                                        {{ $class->class }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm">
                                <div class="row gap-2 d-md-block">
                                    <div class="col-12 text-muted">
                                        Member :
                                    </div>

                                    <div class="col-12 mt-1">
                                        {{ $class->created_at }}
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

                                            <a href="{{ route('class.edit', ['id' => $class->id]) }}"
                                                class="btn btn-warning text-dark">
                                                <i class="fa-solid fa-pen-nib"></i>
                                            </a>

                                            <form method="POST" action="{{ route('class.destroy', $class->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger text-dark"
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
                                        {{ $class->courseModal->course }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md">
                                <div class="row gap-2 d-md-block">
                                    <div class="col-12 text-muted">
                                        Teacher :
                                    </div>

                                    <div class="col-12 mt-1">
                                        {{ $class->userModal->name }}
                                    </div>
                                </div>
                            </div>

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

        @if (empty($classes))
            <div class="text-center" wire:loading.remove>
                <img class="no-data-found-icon" src="{{ asset('img/no-data-found.png') }}">
                <div class="mt-4 h5 text-muted">
                    No data found
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
    <script>
        $('#filter-course_id').select2({
            theme: 'bootstrap-5',
            allowClear: true,
            placeholder: 'Select course',
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
                                text: item.course,
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


        $('#filter-user_id').select2({
            theme: 'bootstrap-5',
            allowClear: true,
            placeholder: 'Select user',
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
            var selectedUserId = $(this).val();
            @this.set('filter.user_id', selectedUserId, false);
        });
    </script>
@endpush

@php
    use Carbon\Carbon;
    use App\Enums\Status;
@endphp

<div id="attendance-list">

    <div class="search-input-group">
        <div class="search-input-icon">
            <i class="fa fa-search"></i>
        </div>
        <input type="text" class="form-control search-input" placeholder="Search class"
            wire:keydown.debounce.250ms="filterClass($event.target.value)" wire:model="filter.class">
    </div>



    <div class="row g-4 mt-1">

        <div class="row g-4 mt-2">
            @foreach ($statistics as $class)
                <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                    <a href="{{ route('attendance_statistics.show', ['id' => $class['class_id'], 'date' => $filterMonth]) }}"
                        class="text-decoration-none">
                        <div class="card border-0 card-shadow px-1 h-100">
                            <div class="card-body px-md-4">
                                <div class="row g-2 g-md-3 align-items-center">
                                    <div class="col-12 col-sm">
                                        <div class="row gap-2 d-md-block">
                                            <div class="col-12">
                                                {{ $class['class_name'] }}
                                            </div>
                                            <div class="col-12 text-muted mt-1">
                                                {{ $class['course_name'] }}
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="d-block d-sm-none">
                                    <div class="col-12 col-md-auto">
                                        <div class="row gap-2 d-md-block">
                                            <div class="col-12">
                                                Member :
                                            </div>
                                            <div class="col-12 mt-1 text-muted text-md-end">
                                                <span
                                                    class="badge bg-primary">{{ $class['attendance_summary']['student_count'] }}</span>
                                                <i class="fa fa-user ms-2"></i>
                                            </div>
                                        </div>

                                    </div>
                                    <hr class="d-block">
                                    <div class="col-12 col-sm">
                                        <div class="row gap-2 d-md-block">
                                            <div class="col-12">
                                                Attendance :
                                            </div>
                                            <div class="col-12 mt-1">
                                                {{ $class['attendance_summary']['present_count'] }} /
                                                {{ $class['attendance_summary']['total_status_count'] }}
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="d-block d-sm-none">
                                    <div class="col-12 col-md-auto">
                                        <div class="mt-1 text-end">
                                            {{ number_format($class['attendance_summary']['present_percentage'], 2) }} %
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach

        </div>
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

        @if (count($statistics) === 0)
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
    <script></script>
@endpush

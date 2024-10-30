@php
    use Carbon\Carbon;
@endphp

<div id="course-list">
    <div class="row g-4 mt-2">
        @foreach ($courses as $course)
            <div class="col-12">
                <div class="card border-0 card-shadow px-1">
                    <div class="card-body px-md-4">
                        <div class="row g-2 g-md-3 align-items-center">

                            <div class="col">
                                <div class="row gap-2 d-md-block">
                                    <div class="col-12 text-muted">
                                        Name :
                                    </div>

                                    <div class="col-12 mt-1">
                                        {{ $course->course }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md">
                                <div class="row gap-2 d-md-block">
                                    <div class="col-12 text-muted">
                                        Created At :
                                    </div>

                                    <div class="col-12 mt-1">
                                        {{ Carbon::parse($course->created_at)->format('d-m-Y h:i A') }}
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
                                            <button class="btn btn-warning text-dark" data-bs-toggle="modal"
                                                data-bs-target="#edit-course-modal-{{ $course->id }}">
                                                <i class="fa-solid fa-pen-nib"></i>
                                            </button>

                                            <form method="POST" action="{{ route('course.destroy', $course->id) }}">
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

                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal for editing course -->
            <div class="modal fade" id="edit-course-modal-{{ $course->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">Edit Course</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('course.update', ['id' => $course->id]) }}" method="POST">
                                @method('PATCH')
                                @csrf
                                <div class="w-100">
                                    <div class="form-group" id="edit-course-modal-content">
                                        <label class="form-label" for="course">Name</label>
                                        <input type="text" class="form-control" id="course" name="course"
                                            value="{{ old('course', $course->course) }}" placeholder="Enter name"
                                            required>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-success text-white rounded-4">Submit</button>
                                </div>
                            </form>
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

        @if (empty($courses))
            <div class="text-center" wire:loading.remove>
                <img class="no-data-found-icon" src="{{ asset('img/no-data-found.png') }}">
                <div class="mt-4 h5 text-muted">
                    No data found
                </div>
            </div>
        @endif
    </div>
</div>

@php
    use Carbon\Carbon;
    use App\Enums\Status;
@endphp

@extends('layout/layout')


@section('style')
    <style>
        #student-column-container {
            overflow-x: auto;
            width: 100%;
        }

        .student-column-content {
            background-color: #F4F6FA;
            width: max-content;
        }

        .profile_image_container {
            min-width: 260px;
        }

        .student-name {
            min-width: 250px;
        }

        .removeButton {
            position: absolute;
            top: 6px;
            left: 6px;
            transform: translate(-50%, -50%);
            width: 21px;
            height: 21px;
            padding: 0;
        }

        .removeButton i {
            font-size: 15px;
        }
    </style>
@endsection

@section('page_title', 'Attendance')

@section('content')
    <div class="row mt-2">
        <div class="col text-muted">
            <ul class="breadcrumb mb-2 mb-md-1">
                <li class="breadcrumb-item">
                    Dashboard
                </li>
                <li class="breadcrumb-item">
                    Manage Attendance
                </li>
                <li class="breadcrumb-item">
                    Attendance Details
                </li>
            </ul>

        </div>
    </div>

    <div class="row align-items-center mb-2">
        <div class="col">
            <h4 class="header-title">Attendance Details</h4>
        </div>

        <div class="col-12 col-md-auto mt-0 mt-md-1">
            <div class="d-flex float-end align-items-center gap-2">
                <a href="{{ route('attendance.index') }}" class="btn btn-dark rounded-4 text-white">
                    <i class="fa-solid fa-angle-left text-white"></i>
                    Back
                </a>
            </div>
        </div>
    </div>

    <div class="text-muted fs-5 mb-3 fst-italic text-decoration-underline">{{ $date }}</div>

    <div class="card border-0 card-shadow px-1">
        <div class="card-body px-md-4">
            <div class="row g-2 g-md-3 align-items-center">

                <div class="col">
                    <div class="row gap-2 d-md-block">
                        <div class="col-12">
                            {{ $class->name }}
                        </div>

                        <div class="col-12 text-muted mt-1">
                            {{ $course->name }}
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md">
                    <div class="row gap-2 d-md-block">
                        <div class="col-12">
                            Member :
                        </div>
                        <div class="col-12 mt-1 text-muted">
                            @if ($attendanceSummary['student_count'] == 0 || is_null($attendanceSummary['student_count']))
                                {{ $attendanceSummary['arrived_count'] ?? '-' }}
                            @else
                                {{ $attendanceSummary['arrived_count'] ?? '0' }} / {{ $attendanceSummary['student_count'] }}
                            @endif
                            <i class="fa fa-user ms-2"></i>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-auto">
                    <div class="row gap-2 d-md-block">
                        <div class="col-12 text-muted">
                            Updated At :
                        </div>

                        <div class="col-12 mt-1">
                            {{ $latestAttendanceUpdatedAt ? Carbon::parse($latestAttendanceUpdatedAt)->format('d-m-Y h:i A') : '-' }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="my-4">
        <h4 class="mb-0">Status :</h4>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card text-center shadow-sm mb-4" data-bs-toggle="modal" data-bs-target="#present-modal"
                role="button">
                <div class="card-body d-flex align-items-center">
                    <div class=" me-4">
                        <div class="status-icon rounded-circle border-present">
                            P
                        </div>
                    </div>
                    <div>
                        <p class="card-title">Present</p>
                        <p class="card-text">
                            <span class="count">{{ $attendanceCounts[Status::Present()->key] ?? 0 }}</span>
                            <i class="fa fa-user ms-2"></i>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-center shadow-sm mb-4" data-bs-toggle="modal" data-bs-target="#absence-modal"
                role="button">
                <div class="card-body d-flex align-items-center">
                    <div class=" me-4">
                        <div class="status-icon rounded-circle border-absence">
                            A
                        </div>
                    </div>
                    <div>
                        <p class="card-title">Absence</p>
                        <p class="card-text">

                            <span class="count">{{ $attendanceCounts[Status::Absence()->key] ?? 0 }}</span>
                            <i class="fa fa-user ms-2"></i>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-center shadow-sm mb-4" data-bs-toggle="modal" data-bs-target="#late-modal" role="button">
                <div class="card-body d-flex align-items-center">
                    <div class=" me-4">
                        <div class="status-icon rounded-circle border-late">
                            L
                        </div>
                    </div>
                    <div>
                        <p class="card-title">Late</p>
                        <p class="card-text">
                            <span class="count">{{ $attendanceCounts[Status::Late()->key] ?? 0 }}</span>
                            <i class="fa fa-user ms-2"></i>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-center shadow-sm mb-4" data-bs-toggle="modal" data-bs-target="#medical-modal"
                role="button">
                <div class="card-body d-flex align-items-center">
                    <div class=" me-4">
                        <div class="status-icon rounded-circle border-medical">
                            MC
                        </div>
                    </div>
                    <div>
                        <p class="card-title">Medical</p>
                        <p class="card-text">
                            <span class="count">{{ $attendanceCounts[Status::Medical()->key] ?? 0 }}</span>
                            <i class="fa fa-user ms-2"></i>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-center shadow-sm mb-4" data-bs-toggle="modal" data-bs-target="#leaveapproval-modal"
                role="button">
                <div class="card-body d-flex align-items-center">
                    <div class=" me-4">
                        <div class="status-icon rounded-circle border-leave-approval">
                            AP
                        </div>
                    </div>
                    <div>
                        <p class="card-title">Leave Approval</p>
                        <p class="card-text">
                            <span class="count">{{ $attendanceCounts[Status::LeaveApproval()->key] ?? 0 }}</span>
                            <i class="fa fa-user ms-2"></i>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="my-3">
        <h4 class="mb-0">Attendance Update :</h4>
    </div>

    @livewire('attendance-student-list', ['classId' => $class->id, 'date' => $date])


    @foreach (Status::asArray() as $statusKey => $statusValue)
        <div class="modal fade" id="{{ strtolower($statusKey) }}-modal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">{{ $statusKey }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="height: 600px; overflow:auto;">
                        @if (isset($studentsByStatus[$statusKey]) && count($studentsByStatus[$statusKey]) > 0)
                            <h5 class="mb-3">Student List :</h5>
                            @foreach ($studentsByStatus[$statusKey] as $entry)
                                <div class="card border-0 card-shadow my-2">
                                    <div class="card-body ps-0 pe-0 pt-0 pb-3">
                                        <div class="row g-3 mt-1">
                                            <div class="col-12 col-lg-3">
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <div class="circle-img-md-wrap rounded-circle border">
                                                        <img src="{{ $entry['student']['profile_image'] ? asset('storage/profile_image/' . $entry['student']['profile_image']) : asset('img/default-student-avatar.png') }}"
                                                            onerror="this.onerror=null;this.src='{{ asset('img/default-student-avatar.png') }}'">
                                                    </div>
                                                </div>
                                            </div>



                                            <div class="col-12 col-lg-9">
                                                <div class="fw-bold text-center text-lg-start">
                                                    {{ $entry['student']['name'] }}
                                                </div>
                                                <div class="my-2 ms-4 ms-sm-5 ms-lg-0">
                                                    Details:
                                                    <div class="text-muted">
                                                        {{ $entry['details'] ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            @endforeach
                        @else
                            <div class="mt-3 text-center">
                                <span>No students found for {{ $statusKey }}.</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach










@endsection

@section('script')
    <script>
        $(function() {
            $('#form').validate({
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
                invalidHandler: function(form, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        notifier.show('Warning!', 'Please check all the fields.', 'warning',
                            '', 4000);
                    }
                },
            })

        });
    </script>


    @stack('scripts')
@endsection

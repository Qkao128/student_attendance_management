@php
    use Carbon\Carbon;
    use App\Enums\Status;
@endphp

@extends('layout/layout')

@section('page_title', 'Attendance_Statistics')

@section('content')
    <div class="row mt-2">
        <div class="col text-muted">
            <ul class="breadcrumb mb-2 mb-md-1">
                <li class="breadcrumb-item">
                    Dashboard
                </li>
                <li class="breadcrumb-item">
                    Attendance Statistics
                </li>
                <li class="breadcrumb-item">
                    Attendance Statistics Details
                </li>
            </ul>

        </div>
    </div>

    <div class="row align-items-center mb-2">
        <div class="col">
            <h4 class="header-title">Attendance Statistics Details</h4>
        </div>

        <div class="col-12 col-md-auto mt-0 mt-md-1">
            <div class="d-flex float-end align-items-center gap-2">
                <a href="{{ route('attendance_statistics.index') }}" class="btn btn-dark rounded-4 text-white">
                    <i class="fa-solid fa-angle-left text-white"></i>
                    Back
                </a>
            </div>
        </div>
    </div>

    <div class="card border-0 card-shadow px-1 mt-3">
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

                <div class="col-12 col-sm-auto">
                    <div class="row gap-2 d-md-block">
                        <div class="col-12 text-muted">
                            Created At :
                        </div>

                        <div class="col-12 mt-1">
                            {{ Carbon::parse($class->created_at)->format('d-m-Y h:i A') }}
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

    @php
        $formattedMonth = date('F Y', strtotime($month . '-01'));
    @endphp

    <div class="mt-5">
        <h5 class="mb-4">Attendance Statistics by Month (<span
                class="fst-italic text-decoration-underline">{{ $formattedMonth }}</span> ) :</h5>

        <div class="card border-0 card-shadow px-1 mt-0">
            <div class="card-body">
                <div class="row g-2 g-md-3 align-items-center p-0">
                    <div class="col-12 col-sm-6 col-md-4">

                        <div class="d-flex align-items-center">
                            <div class=" me-4">
                                <div class="status-attendance-icon rounded-circle border-present">
                                    P
                                </div>
                            </div>
                            <div>
                                <p class="card-title mb-0">Present</p>
                            </div>

                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-4">

                        <div class="d-flex align-items-center">
                            <div class=" me-4">
                                <div class="status-attendance-icon rounded-circle border-absence">
                                    A
                                </div>
                            </div>
                            <div>
                                <p class="card-title mb-0">Absence</p>
                            </div>

                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-4">

                        <div class="d-flex align-items-center">
                            <div class=" me-4">
                                <div class="status-attendance-icon rounded-circle border-late">
                                    L
                                </div>
                            </div>
                            <div>
                                <p class="card-title mb-0">Late</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-4">

                        <div class="d-flex align-items-center">
                            <div class=" me-4">
                                <div class="status-attendance-icon rounded-circle border-medical">
                                    MC
                                </div>
                            </div>
                            <div>
                                <p class="card-title mb-0">Medical</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-4">

                        <div class="d-flex align-items-center">
                            <div class=" me-4">
                                <div class="status-attendance-icon rounded-circle border-leave-approval">
                                    AP
                                </div>
                            </div>
                            <div>
                                <p class="card-title mb-0">Leave Approval</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive mt-4">
            <table class="table table-bordered ">
                <thead>
                    <tr>
                        <th class="bg-primary text-white">Student Name</th>
                        @foreach (Carbon::parse($month . '-01')->daysUntil(Carbon::parse($month . '-01')->endOfMonth()) as $day)
                            <th class="bg-primary text-white">{{ $day->format('d') }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($attendanceTable as $row)
                        <tr>
                            <td class="d-flex align-self-center text-wrap text-break"
                                style="max-width: 300px; min-width: 210px;">{{ $row['student_name'] }}</td>
                            @foreach ($row['attendance'] as $status)
                                <td>
                                    <span
                                        class="
                                            @if ($status === 'H') border-holiday
                                            @elseif ($status === 'P') border-present
                                            @elseif ($status === 'A') border-absence
                                            @elseif ($status === 'L') border-late
                                            @elseif ($status === 'MC') border-medical
                                            @elseif ($status === 'AP') border-leave-approval @endif status-attendance-icon rounded-circle">
                                        {{ $status }}
                                    </span>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 mt-md-5">
        <h5 class="mb-4">Attendance Exceptions :</h5>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="bg-primary text-white">Date</th>
                        <th class="bg-primary text-white">Student Name</th>
                        <th class="bg-primary text-white">Status</th>
                        <th class="bg-primary text-white">Reason</th>
                        <th class="bg-primary text-white">Attachament</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($nonPresentDetails as $detail)
                        <tr>
                            <td style="min-width: 180px;max-width: 200px;">
                                {{ $detail['date'] }}</td>
                            <td class="text-wrap text-break h-100" style="min-width: 210px;">
                                {{ $detail['student_name'] }}</td>
                            <td style="width: 100px;">
                                <span
                                    class="
                                        @if ($detail['status'] === 'H') border-holiday
                                        @elseif ($detail['status'] === 'P') border-present
                                        @elseif ($detail['status'] === 'A') border-absence
                                        @elseif ($detail['status'] === 'L') border-late
                                        @elseif ($detail['status'] === 'MC') border-medical
                                        @elseif ($detail['status'] === 'AP') border-leave-approval @endif
                                        status-attendance-icon rounded-circle ms-1">
                                    {{ $detail['status'] }}
                                </span>
                            </td>
                            <td class="text-wrap text-break " style="min-width: 270px;width: 100%;">
                                @if ($detail['reason'])
                                    {{ $detail['reason'] }}
                                @else
                                    <span class="fst-italic text-muted ms-1">N/A</span>
                                @endif
                            </td>
                            <td class="text-center" style="min-width: 160px;width: 100%;">
                                @if ($detail['file'])
                                    <a href="{{ asset('storage/attendance_files/' . $detail['file']) }}" target="_blank"
                                        class="btn btn-primary text-truncate text-white" style="width: 80px">View</a>
                                @else
                                    <span class="fst-italic text-muted ms-1">No Attachament</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    @if (empty($nonPresentDetails))
                        <td colspan="5">
                            <div class="text-center">
                                <img class="no-data-found mt-2" src="{{ asset('img/no-data-found.png') }}">
                                <div class="mt-4 h5 text-muted">
                                    No data found
                                </div>
                            </div>
                        </td>
                    @endif

                </tbody>
            </table>
        </div>
    </div>


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

@endsection

@php
    use Carbon\Carbon;
@endphp

@extends('layout/layout')

@section('page_title', 'Holidays')

@section('content')
    <div id="admin-course">
        <div class="row mt-2">
            <div class="col text-muted">
                <ul class="breadcrumb mb-2 mb-md-1">
                    <li class="breadcrumb-item">
                        Dashboard
                    </li>
                    <li class="breadcrumb-item">
                        Manage Holidays
                    </li>
                </ul>

            </div>
        </div>

        <div class="row align-items-center my-1">
            <div class="col">
                <h4 class="header-title">Manage Holidays</h4>
            </div>

            <div class="col-12 col-md-auto mt-0 mt-md-1">
                <div class="d-flex float-end align-items-center">
                    <button type="button" class="btn btn-success text-white rounded-4" data-bs-toggle="modal"
                        data-bs-target="#add-holiday-modal">
                        Add
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12 col-xl-4">
            @livewire('holiday-list', ['year' => now()->year, 'month' => now()->month])
        </div>

        <div class="col-12 col-xl-8 mt-4 mt-xl-0">
            <div class="mb-3 w-100" id="customer-column-container">
                <div class="px-1" id="customer-column-calculation-container">
                    <div class="form-group customer-column-calculation-content rounded">
                        <div class="d-flex p-3 rounded w-100">
                            <div id="calendar" class="w-100" style="min-width: 570px;min-height: 600px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add-holiday-modal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        Add New Holidays
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form action="{{ route('holiday.store') }}" id="form" method="POST">
                        @csrf

                        <div class="row" id="add-holiday-modal-content">
                            <div class="col-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label" for="date_from">Date from<span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="date_from" name="date_from"
                                        value="{{ Carbon::now()->toDateString() }}" required>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="date_to">Date to<span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="date_to" name="date_to"
                                        value="{{ Carbon::now()->toDateString() }}" required>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="title">Title<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title"
                                    value="{{ old('title') }}" placeholder="Enter title" required>

                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="background_color">Custom background colour<span
                                        class="text-danger">*</span></label>
                                <div class="form-group justify-content-between d-flex">
                                    <div>
                                        <input type="color" class="form-control" id="background_color"
                                            name="background_color" style="min-width: 100px;height: 37px;" required>
                                    </div>

                                    <div>
                                        <i class="fa-solid fa-palette me-3" style="margin-top: 9px;"></i>
                                    </div>
                                </div>

                            </div>


                            <div class="col-12">
                                <div class="form-group mt-3">
                                    <label class="form-label" for="details">Details</label>
                                    <textarea class="form-control" style="resize: none;" name="details" rows="5" id="details"
                                        placeholder="Add detail for this holidays...">{{ old('details') }}</textarea>
                                </div>
                            </div>


                        </div>


                        <div class="d-flex float-end gap-2">
                            <button type="submit" class="btn btn-primary text-white rounded-4" id="submit-btn"
                                form="form">Submit</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="eventDetailsModal" tabindex="-1" aria-labelledby="eventDetailsLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventDetailsLabel">
                        <p class="fw-bold mb-0" id="event-title"></p>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="height: 550px;">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Started At :</label>
                            <p class="text-muted" id="event-start"></p>
                        </div>

                        <div class="col-12 col-sm-6">
                            <label class="form-label">Ended At :</label>
                            <p class="text-muted" id="event-end"></p>
                        </div>

                        <div class="col-12 mt-1">
                            <label class="form-label">Details :</label>
                            <div class="form-control" style="height: 300px; max-height: 300px;overflow-y: auto;">
                                <p id="event-details"></p>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')

    <script>
        $(document).ready(function() {
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
                }
            })

            $('#date_to').attr('min', $('#date_from').val());

            $('#date_from').on('change', function() {
                let dateFormValue = $(this).val();
                let dateToValue = $('#date_to').val();

                $('#date_to').attr('min', dateFormValue);

                if (dateToValue < dateFormValue) {
                    $('#date_to').val(dateFormValue);
                }
            });

            const calendarEl = $('#calendar')[0]; // jQuery 轉換為 DOM 元素

            const calendar = new FullCalendar.Calendar(calendarEl, {
                plugins: [FullCalendar.dayGridPlugin],
                initialView: 'dayGridMonth',
                themeSystem: 'bootstrap5',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,dayGridWeek,dayGridDay'
                },
                events: function(fetchInfo, successCallback, failureCallback) {
                    $.ajax({
                        url: "{{ route('holiday.getHolidays') }}",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            successCallback(response);
                        }
                    });
                },
                eventDidMount: function(info) {
                    $(info.el).css('cursor', 'pointer');
                },
                eventClick: function(info) {
                    const eventTitle = info.event.title;
                    const eventStart = info.event.start.toLocaleDateString();
                    const eventEnd = info.event.end.toLocaleDateString();
                    const eventDetails = info.event.extendedProps.details ||
                        'No additional details provided.';

                    $('#event-title').text(`${eventTitle}`);
                    $('#event-start').text(`${eventStart}`);
                    $('#event-end').text(`${eventEnd}`);
                    $('#event-details').text(`${eventDetails}`);

                    $('#eventDetailsModal').modal('show');
                },
                datesSet: function(dateInfo) {
                    const currentYear = dateInfo.view.currentStart.getFullYear();
                    const currentMonth = dateInfo.view.currentStart.getMonth() + 1;

                    Livewire.dispatch('updateDate', {
                        currentYear: currentYear,
                        currentMonth: currentMonth
                    });
                }
            });

            // 渲染日曆
            calendar.render();

        });
    </script>

    @stack('scripts')
@endsection

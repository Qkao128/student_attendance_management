@php
    use Carbon\Carbon;
    use App\Enums\UserType;

@endphp

<div>
    <div class="table-responsive">
        <table class="table table-striped table-borderless">
            <thead>
                <tr>
                    <th class="bg-primary text-white p-2 px-sm-3">Holidays</th>
                    <th class="bg-primary text-white p-2 px-sm-3 text-left text-sm-center" style="width: 150px;">Date
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($holidays as $holiday)
                    @hasrole('Monitor')
                        <tr>
                        @else
                        <tr role="button"
                            onclick="selectHoliday({{ $holiday->id }}, '{{ $holiday->title }}', '{{ $holiday->date_from }}', '{{ $holiday->date_to }}', '{{ $holiday->background_color }}', '{{ $holiday->details }}')">
                        @endhasrole

                        <td class="p-2 px-sm-3 d-flex align-self-center text-wrap text-break"
                            style="max-width: 300px; min-width: 210px;">
                            <div class="p-1" style="min-width: 50px; height: 27px;border: 1px solid black;">
                                <div class="w-100 h-100" style="background-color: {{ $holiday->background_color }};">
                                </div>
                            </div>
                            <span class="ms-2">{{ $holiday->title }}</span>
                        </td>
                        <td class="p-2 px-sm-3 text-left text-sm-center" style="min-width: 210px;width: 100%;">
                            <span
                                class="{{ $holiday->date_from && Carbon::parse($holiday->date_from)->isToday() ? 'text-success' : '' }}">
                                {{ $holiday->date_from && $holiday->date_to ? Carbon::parse($holiday->date_from)->format('Y/m/d') . ' - ' . Carbon::parse($holiday->date_to)->format('Y/m/d') : '-' }}
                            </span>
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>

    <div class="modal fade" id="holiday-list-modal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Holiday</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="holiday-form" method="POST" action="{{ route('holiday.update', ['id' => ':id']) }}">
                        @csrf
                        @method('PATCH')

                        <input type="hidden" id="modal-holiday-id" name="id" value="">

                        <div class="row" style="margin-bottom: 100px;">

                            <div class="col-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label" for="modal-date-from">Date From</label>
                                    <input type="date" class="form-control" id="modal-date-from" name="date_from"
                                        required>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label" for="modal-date-to">Date To</label>
                                    <input type="date" class="form-control" id="modal-date-to" name="date_to"
                                        required>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label" for="modal-title">Title</label>
                                    <input type="text" class="form-control" id="modal-title" name="title" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label" for="modal-background-color">Custom background colour<span
                                            class="text-danger">*</span></label>
                                    <div class="form-group justify-content-between d-flex">
                                        <div>
                                            <input type="color" class="form-control" id="modal-background-color"
                                                name="background_color" style="min-width: 100px;height: 37px;" required>
                                        </div>

                                        <div>
                                            <i class="fa-solid fa-palette me-3" style="margin-top: 9px;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label class="form-label" for="modal-details">Details</label>
                                    <textarea class="form-control" id="modal-details" style="resize: none;" name="details" rows="5"
                                        placeholder="Add detail for this holidays..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save Changes</button>

                    </form>
                    <form id="delete-form" method="POST" action="{{ route('holiday.destroy', ['id' => ':id']) }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger" onclick="deleteFormConfirmation(event)">
                            <i class="fa-solid fa-trash-can"></i> Delete
                        </button>
                    </form>


                </div>
            </div>
        </div>
    </div>
</div>



<div class="d-grid">
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

    @if (count($holidays) === 0)
        <div class="alert text-center" wire:loading.remove>
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
            $('#holiday-form').validate({
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


            // 初始化日期范围
            $('#holiday-list-modal').on('shown.bs.modal', function() {
                // 获取当前的 date_from 和 date_to 值
                var dateFrom = $('#modal-date-from').val();
                var dateTo = $('#modal-date-to').val();

                // 如果 date_from 已经有值，设置 date_to 的最小日期
                if (dateFrom) {
                    $('#modal-date-to').attr('min', dateFrom);
                }

                // 如果 date_to 小于 date_from，自动更新 date_to 为 date_from
                if (dateTo && dateFrom && dateTo < dateFrom) {
                    $('#modal-date-to').val(dateFrom);
                }

                // 当修改 date_from 时，实时更新 date_to 的最小日期
                $('#modal-date-from').on('change', function() {
                    var dateFrom = $(this).val();
                    $('#modal-date-to').attr('min', dateFrom);

                    // 如果 date_to 小于 date_from，自动更新 date_to 为 date_from
                    var dateTo = $('#modal-date-to').val();
                    if (dateTo && dateTo < dateFrom) {
                        $('#modal-date-to').val(dateFrom); // 将 date_to 自动设为 date_from
                    }
                });

                // 当修改 date_to 时，如果 date_to 小于 date_from，自动调整 date_from 为 date_to
                $('#modal-date-to').on('change', function() {
                    var dateFrom = $('#modal-date-from').val();
                    var dateTo = $(this).val();

                    // 如果 date_to 小于 date_from，自动更新 date_from 为 date_to
                    if (dateTo && dateFrom && dateTo < dateFrom) {
                        $('#modal-date-from').val(dateTo); // 更新 date_from 为 date_to
                    }
                });
            });

        });

        function selectHoliday(id, title, dateFrom, dateTo, backgroundColor, details) {
            // 更新模态框字段
            $('#modal-holiday-id').val(id); // 设置隐藏字段 id 的值
            $('#modal-title').val(title);
            $('#modal-date-from').val(dateFrom);
            $('#modal-date-to').val(dateTo);
            $('#modal-background-color').val(backgroundColor);
            $('#modal-details').val(details);

            // 动态设置表单的 action，直接在 URL 中包含 holiday ID
            let formAction = `{{ route('holiday.update', ['id' => ':id']) }}`.replace(':id', id);
            $('#holiday-form').attr('action', formAction);

            // 设置删除表单的 action，包含 holiday ID
            let deleteFormAction = `{{ route('holiday.destroy', ['id' => ':id']) }}`.replace(':id', id);
            $('#delete-form').attr('action', deleteFormAction);

            // 打开模态框
            $('#holiday-list-modal').modal('show');
        }
    </script>
@endpush

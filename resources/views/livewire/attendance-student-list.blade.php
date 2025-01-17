@php
    use Carbon\Carbon;
    use App\Enums\Status;
@endphp

@section('style')
    <style>
        .form-control {
            background-color: #FAFAFA !important;
        }
    </style>
@endsection

<div id="attendance-student-list">

    <form action="{{ route('attendance.store', ['classId' => $classId, 'date' => $date]) }}" id="form" method="POST"
        enctype="multipart/form-data" style="background-color: #edf0f2">
        @csrf
        <div class="row g-3 mt-2 px-3">
            @hasrole('Monitor')
                <label class="form-label mb-0">Upload Attendance Proof :</label>

                <div id="attendance-global-file-upload-container">
                    <div id="attendance-global-file-upload-container">

                        @if (!empty($file))
                            <!-- 有文件時才顯示的文件查看模式（默認隱藏） -->
                            <div id="attendance-global-file-view-container"
                                class="d-flex align-items-center justify-content-center">
                                <div id="file-att">
                                    <span id="view-att-file" class="d-flex" style="width: 250px">
                                        <a href="{{ asset('storage/attendance_files/' . $file->file) }}" target="_blank"
                                            class="btn btn-primary text-truncate text-white">
                                            {{ $file->file ?? 'View File' }}
                                        </a>

                                        <i class="fa-solid fa-pen ms-4" id="edit-file-att-icon"></i>
                                    </span>


                                    <span class="d-none align-items-center" id="file-att-input">
                                        <input type="file" name="file" class="form-control">
                                        <input type="hidden" name="file_status" value="">
                                        <i class="fa-solid fa-xmark ms-2" id="cancel-file-att-icon"></i>
                                    </span>

                                </div>

                            </div>
                        @else
                            <!-- 默認顯示文件上傳框 -->
                            <div id="attendance-global-file-input-container" class="d-flex align-items-center">
                                <input id="attendance-file-input" type="file" name="file" class="form-control">
                                {{-- <i id="attendance-global-file-cancel-icon"
                                    class="fa-solid fa-xmark ms-2 cancel-file-icon d-none"></i>
                                <input type="hidden" id="attendance-global-file-status" name="file_status" value=""> --}}
                            </div>
                        @endif

                    </div>
                </div>
            @else
                <label class="form-label mb-0">Upload Attendance Proof :</label>

                <div id="attendance-global-file-upload-container">
                    <div id="attendance-global-file-upload-container">

                        @if (!empty($file))
                            <!-- 有文件時才顯示的文件查看模式（默認隱藏） -->
                            <div class="d-flex align-items-center justify-content-center">
                                <div>
                                    <span class="d-flex" style="width: 250px">
                                        <a href="{{ asset('storage/attendance_files/' . $file->file) }}" target="_blank"
                                            class="btn btn-primary text-truncate text-white">
                                            {{ $file->file ?? 'View File' }}
                                        </a>
                                    </span>
                                </div>

                            </div>
                        @else
                            <div id="attendance-global-file-input-container" class="text-center">
                                <span class="text-muted">no Attendance Proof has been uploaded.</span>
                            </div>
                        @endif

                    </div>
                </div>
            @endhasrole

            <label class="form-label">Student List :</label>
            @foreach ($students as $key => $student)
                <div class="col-12 col-sm-6 col-md-4 col-lg-6 col-xxl-3">
                    <div class="card border-0 card-shadow px-1 h-100">
                        <div class="card-body px-md-4">
                            <input type="hidden" name="students[{{ $key }}][student_id]"
                                value="{{ $student->id }}">
                            <input type="hidden" name="students[{{ $key }}][status]" class="status-input"
                                value="{{ $student->attendance_status }}" required>

                            <div class="d-flex justify-content-center flex-column align-items-center mt-3">
                                <div class="circle-img-lg-wrap rounded-circle border">
                                    <img src="{{ $student->profile_image ? asset('storage/profile_image/' . $student->profile_image) : asset('img/default-student-avatar.png') }}"
                                        onerror="this.onerror=null;this.src='{{ asset('img/default-student-avatar.png') }}'">
                                </div>
                                <div class="fw-bold mt-3">{{ $student->name }}</div>
                            </div>

                            <div id="file-upload-container-{{ $key }}" class="file-upload-container"
                                style="display: none;">
                                <hr class="my-3">

                                <label class="form-label">Upload Attachament :</label>
                                <div class="px-3">
                                    @if (!empty($student->attendance_file))
                                        <!-- 有文件的情況 -->
                                        <div id="file-container-{{ $key }}"
                                            class="d-flex justify-content-around">
                                            <span id="view-file" class="d-flex" style="width: 150px">
                                                <a href="{{ asset('storage/attendance_files/' . $student->attendance_file) }}"
                                                    target="_blank" class="btn btn-primary text-truncate text-white">
                                                    {{ $student->attendance_file }}
                                                </a>

                                                <i class="fa-solid fa-pen edit-file-icon ms-4"
                                                    data-student-id="{{ $key }}"></i>
                                            </span>

                                            <span class="d-none align-items-center"
                                                id="file-input-{{ $key }}">
                                                <input type="file" name="students[{{ $key }}][file]"
                                                    class="form-control">
                                                <input type="hidden" name="students[{{ $key }}][file_status]"
                                                    value="">
                                                <i class="fa-solid fa-xmark cancel-file-icon ms-2"
                                                    data-student-id="{{ $key }}"></i>
                                            </span>
                                        </div>
                                    @else
                                        <!-- 沒有文件的情況 -->
                                        <input type="file" name="students[{{ $key }}][file]"
                                            class="form-control">
                                    @endif
                                </div>
                            </div>

                            <hr class="my-3">

                            <div>
                                <label class="form-label">Details :</label>
                                <div class="d-flex justify-content-around">
                                    <div id="student-attendance-detail-{{ $key }}" class="attendance-detail"
                                        style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        @if (!empty($student->attendance_details))
                                            <label class="text-truncate detail-text overflow-hidden"
                                                title="{{ $student->attendance_details }}">
                                                {{ $student->attendance_details }}
                                            </label>
                                            <input type="hidden" name="students[{{ $key }}][details]"
                                                value="{{ $student->attendance_details }}">
                                        @else
                                            <label class="text-muted">Enter details...</label>
                                            <input type="hidden" name="students[{{ $key }}][details]"
                                                value="">
                                        @endif
                                    </div>

                                    <div class="icon-container d-flex">
                                        @if (!empty($student->attendance_details))
                                            <i class="fa-solid fa-pen edit-icon"
                                                data-student-id="{{ $key }}"></i>
                                        @else
                                            <i class="fa-solid fa-circle-plus add-edit-icon"
                                                data-student-id="{{ $key }}" style="height: 15px"></i>
                                        @endif
                                    </div>
                                </div>

                            </div>

                            <div class="status mt-3">
                                <label class="form-label">Status :</label>
                                <div class="row g-2 g-md-3 mb-1">
                                    <div class="col-auto">
                                        <div class="d-flex align-items-center">
                                            <div class="status-updated-icon rounded-circle border-present status-option"
                                                data-status="{{ Status::Present()->key }}"
                                                data-student-id="{{ $key }}">
                                                P
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-auto">
                                        <div class="d-flex align-items-center">
                                            <div class="status-updated-icon rounded-circle border-absence status-option"
                                                data-status="{{ Status::Absence()->key }}"
                                                data-student-id="{{ $key }}">
                                                A
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-auto">
                                        <div class="d-flex align-items-center">
                                            <div class="status-updated-icon rounded-circle border-late status-option"
                                                data-status="{{ Status::Late()->key }}"
                                                data-student-id="{{ $key }}">
                                                L
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-auto">
                                        <div class="d-flex align-items-center">
                                            <div class="status-updated-icon rounded-circle border-medical status-option"
                                                data-status="{{ Status::Medical()->key }}"
                                                data-student-id="{{ $key }}">
                                                MC
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-auto">
                                        <div class="d-flex align-items-center">
                                            <div class="status-updated-icon rounded-circle border-leave-approval status-option"
                                                data-status="{{ Status::LeaveApproval()->key }}"
                                                data-student-id="{{ $key }}">
                                                AP
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-danger error-message mt-2" id="status-error-{{ $key }}"
                                    style="display: none;">Please select a status.</div>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        @php
            $attendanceDate = Carbon::parse($date); // 解析傳入日期
            $currentMonth = Carbon::now()->format('Y-m') === $attendanceDate->format('Y-m'); // 是否為當月
            $isWithinOneWeek = $attendanceDate->isPast() ? $attendanceDate->diffInDays(Carbon::now()) <= 7 : false; // 是否在過去 7 天內
        @endphp

        @if ($students->isNotEmpty())

            @if ($isHoliday['is_holiday'] == true)
                <div class="text-danger text-center mt-3 pt-2 ps-3 pe-3 pb-4">
                    This day is designated as a holiday.
                </div>
            @else
                @if ($attendanceDate->isFuture())
                    <div class="text-danger text-center mt-3 p-3">
                        Attendance records cannot be submitted for future dates.
                    </div>
                @else
                    @hasrole('SuperAdmin')
                        <!-- SuperAdmin 沒有任何限制，直接顯示提交按鈕 -->
                        <div class="text-end p-3 mt-5">
                            <button type="submit" class="btn btn-success text-white rounded-4">Submit</button>
                        </div>
                        @elsehasrole('Admin')
                        @if ($currentMonth)
                            <!-- Admin 只檢查是否為當月 -->
                            <div class="text-end p-3 mt-5">
                                <button type="submit" class="btn btn-success text-white rounded-4">Submit</button>
                            </div>
                        @else
                            <!-- 顯示過期消息 -->
                            <div class="text-danger text-center mt-3 p-3">
                                Attendance records for previous months cannot be modified.
                            </div>
                        @endif
                        @elsehasrole('Monitor')

                        @if ($currentMonth && $isHoliday['is_holiday'] == false)
                            @if ($isWithinOneWeek)
                                <!-- 當前月份且不是節假日，且提交日期在一週內才顯示提交按鈕 -->
                                <div class="text-end p-3 mt-5">
                                    <button type="submit" class="btn btn-success text-white rounded-4">Submit</button>
                                </div>
                            @else
                                <!-- 超過一週時顯示錯誤信息 -->
                                <div class="text-danger text-center mt-3 p-3">
                                    You cannot submit the attendance after one week of the attendance date.
                                </div>
                            @endif
                        @else
                            <div class="text-danger text-center mt-3 p-3">
                                Attendance records for previous months cannot be modified.
                            </div>
                        @endif
                    @else
                        <div class="text-danger text-center mt-3 p-3">
                            You do not have permission to submit attendance.
                        </div>
                    @endhasrole
                @endif
            @endif

        @endif

    </form>

    @if (count($students) === 0)
        <div class="text-center">
            <img class="no-data-found mt-2" src="{{ asset('img/no-data-found.png') }}">
            <div class="mt-4 h5 text-muted">
                No data found
            </div>
        </div>
    @endif

</div>


@push('scripts')
    <script>
        $(document).ready(function() {
            const currentDate = new Date();
            const attendanceDate = new Date("{{ $date }}");

            // Check if the user has the 'SuperAdmin' role
            const userRole =
                "{{ Auth::user()->getRoleNames()->first() }}"; // Assuming you fetch the role like this

            if (userRole !== 'SuperAdmin') {
                // Check if the dates are not in the same month
                const isSameMonth =
                    currentDate.getFullYear() === attendanceDate.getFullYear() &&
                    currentDate.getMonth() === attendanceDate.getMonth();

                if (!isSameMonth) {
                    $('#form').on('submit', function(e) {
                        e.preventDefault(); // Prevent form submission
                        alert('Attendance records for previous months cannot be modified.');
                    });

                    $('.btn-success').hide(); // Hide the submit button
                }
            }

            // 檢查每個學生的狀態
            $('#form').on('submit', function(e) {
                let isValid = true;

                $('.status-input').each(function() {
                    const studentId = $(this).attr('name').match(/\d+/)[0];
                    if (!$(this).val()) {
                        isValid = false;
                        $(`#status-error-${studentId}`).show();
                    } else {
                        $(`#status-error-${studentId}`).hide();
                    }
                });

                if (!isValid) {
                    e.preventDefault(); // 阻止提交
                    alert('Please select a status for all students.');
                }
            });


            // 點擊 + 號顯示輸入框
            $(document).on("click", ".add-edit-icon", function() {
                let studentId = $(this).data("student-id");
                let detailContainer = $(`#student-attendance-detail-${studentId}`);
                let iconContainer = $(this).parent();

                if ($(this).hasClass("fa-circle-plus")) {
                    let inputField = `
            <input type="text" class="form-control detail-input"
                   name="students[${studentId}][details]"
                   placeholder="Enter details..."
                   style="width: 100%; min-width: 120px;">
            `;

                    // 顯示輸入框並隱藏原始文本
                    detailContainer.html(inputField);
                    iconContainer.html(`
            <i class="fa-solid fa-check save-icon ms-2 mt-2" data-student-id="${studentId}" style="font-size: 16px;"></i>
            <i class="fa-solid fa-xmark cancel-icon ms-2 mt-2" data-student-id="${studentId}" style="font-size: 16px;"></i>
            `);
                }
            });

            // 點擊保存圖標保存數據
            $(document).on("click", ".save-icon", function() {
                let studentId = $(this).data("student-id");
                let detailContainer = $(`#student-attendance-detail-${studentId}`);
                let iconContainer = $(this).parent();
                let inputValue = detailContainer.find("input").val().trim();

                if (inputValue) {
                    // 更新隱藏的 input，值為輸入框內容
                    let inputField = `
            <input type="hidden" name="students[${studentId}][details]" value="${inputValue}">
            `;
                    detailContainer.html(`
            <label class="text-truncate detail-text" title="${inputValue}">
                ${inputValue}
            </label>
            ${inputField}
            `);
                    iconContainer.html(`
            <i class="fa-solid fa-pen edit-icon" data-student-id="${studentId}"></i>
            `);
                } else {
                    // 如果沒有輸入值則設定為空（空字串）
                    detailContainer.html(`
            <label class="text-muted">Enter details...</label>
            <input type="hidden" name="students[${studentId}][details]" value="">
            `);
                    iconContainer.html(`
            <i class="fa-solid fa-circle-plus add-edit-icon" data-student-id="${studentId}" style="height: 15px"></i>
            `);
                }
            });

            // 點擊取消圖標恢復原來的內容，並且清空 input 的值
            $(document).on("click", ".cancel-icon", function() {
                let studentId = $(this).data("student-id");
                let detailContainer = $(`#student-attendance-detail-${studentId}`);
                let iconContainer = $(this).parent();

                // 直接將 input 的值設為空並保留 input 元素
                detailContainer.find("input").val('');
                detailContainer.html(`
                    <label class="text-muted">Enter details...</label>
                    <input type="hidden" name="students[${studentId}][details]" value="">
                `);

                // 恢復原來的圖標
                iconContainer.html(`
                <i class="fa-solid fa-circle-plus add-edit-icon" data-student-id="${studentId}" style="height: 15px"></i>
                `);
            });

            // 點擊編輯圖標進行修改
            $(document).on("click", ".edit-icon", function() {
                let studentId = $(this).data("student-id");
                let detailContainer = $(`#student-attendance-detail-${studentId}`);
                let iconContainer = $(this).parent();
                let currentText = detailContainer.find("label").text().trim();

                let inputField = `
                <input type="text" class="form-control detail-input"
                    name="students[${studentId}][details]"
                    placeholder="Enter details..."
                    style="width: 100%; min-width: 120px;"
                    value="${currentText}">
                `;

                detailContainer.html(inputField);
                iconContainer.html(`
                <i class="fa-solid fa-check save-icon ms-2 mt-2" data-student-id="${studentId}"></i>
                <i class="fa-solid fa-xmark cancel-icon ms-2 mt-2" data-student-id="${studentId}"></i>
                `);
            });

            $('.status-input').each(function() {
                const studentId = $(this).attr('name').match(/\d+/)[0];
                const statusValue = $(this).val(); // 取得隱藏的 status 值

                if (statusValue) {
                    // 根據當前狀態值設置選中的樣式
                    const selectedOption = $(
                        `.status-option[data-student-id="${studentId}"][data-status="${statusValue}"]`);
                    selectedOption.css({
                        'background-color': selectedOption.css('border-color'),
                        'color': '#fff'
                    });
                }
            });

            // 點擊狀態選項更新狀態
            $(document).on('click', '.status-option', function() {
                const studentId = $(this).data('student-id');
                const statusValue = $(this).data('status');

                // 清除同一學生的所有選中狀態
                $(`.status-option[data-student-id="${studentId}"]`).css({
                    'background-color': '',
                    'color': ''
                });

                // 選中當前狀態
                $(this).css({
                    'background-color': $(this).css('border-color'),
                    'color': '#fff'
                });

                // 更新隱藏的狀態輸入
                $(`input[name="students[${studentId}][status]"]`).val(statusValue);

                // 顯示或隱藏文件上傳框
                const fileUploadContainer = $(`#file-upload-container-${studentId}`);
                if (statusValue !== '{{ Status::Present()->key }}') {
                    fileUploadContainer.show(); // 顯示文件上傳框
                } else {
                    fileUploadContainer.hide(); // 隱藏文件上傳框
                }
            });

            // 頁面加載時初始化文件上傳框的狀態
            $('.status-input').each(function() {
                const studentId = $(this).attr('name').match(/\d+/)[0];
                const statusValue = $(this).val();

                // 設置選中的狀態樣式
                if (statusValue) {
                    const selectedOption = $(
                        `.status-option[data-student-id="${studentId}"][data-status="${statusValue}"]`
                    );
                    selectedOption.css({
                        'background-color': selectedOption.css('border-color'),
                        'color': '#fff'
                    });
                }

                // 根據狀態值顯示/隱藏文件上傳框
                const fileUploadContainer = $(`#file-upload-container-${studentId}`);
                if (statusValue && statusValue !== '{{ Status::Present()->key }}') {
                    fileUploadContainer.show();
                } else {
                    fileUploadContainer.hide();
                }
            });

            $(document).on('click', '.edit-file-icon', function() {
                let studentId = $(this).data('student-id');
                $(`#file-input-${studentId}`).removeClass('d-none').addClass('d-flex');
                $(`#view-file-${studentId}`).removeClass('d-flex').addClass('d-none');
                $(`input[name="students[${studentId}][file_status]"]`).val('edited');
            });

            $(document).on('click', '.cancel-file-icon', function() {
                let studentId = $(this).data('student-id');
                $(`#file-input-${studentId}`).removeClass('d-flex').addClass('d-none');
                $(`#view-file-${studentId}`).removeClass('d-none').addClass('d-flex');
                $(`input[name="students[${studentId}][file_status]"]`).val('');
            });



            $(document).on('click', '#edit-file-att-icon', function() {
                $(`#file-att-input`).removeClass('d-none').addClass('d-flex');
                $(`#view-att-file`).removeClass('d-flex').addClass('d-none');
                $(`input[name="file_status"]`).val('edited');
            });

            $(document).on('click', '#cancel-file-att-icon', function() {
                $(`#file-att-input`).removeClass('d-flex').addClass('d-none');
                $(`#view-att-file`).removeClass('d-none').addClass('d-flex');
                $(`input[name="file_status"]`).val('');
            });

        });
    </script>
@endpush

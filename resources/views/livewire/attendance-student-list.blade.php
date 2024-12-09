@php
    use Carbon\Carbon;
    use App\Enums\Status;
@endphp

<div id="attendance-student-list">
    <form action="{{ route('attendance.store', ['classId' => $classId, 'date' => $date]) }}" id="form" method="POST"
        style="background-color: #edf0f2">
        @csrf
        <div class="row g-3 mt-2 px-3">
            @foreach ($students as $key => $student)
                <div class="col-12 col-sm-6 col-md-4 col-lg-6 col-xxl-3">
                    <div class="card border-0 card-shadow px-1">
                        <div class="card-body px-md-4">
                            <input type="hidden" name="students[{{ $key }}][student_id]"
                                value="{{ $student->id }}">
                            <input type="hidden" name="students[{{ $key }}][status]" class="status-input"
                                value="{{ $student->attendance_status }}" required>

                            <div class="d-flex justify-content-center flex-column align-items-center mt-3">
                                <div class="circle-img-lg-wrap rounded-circle border">
                                    <img src="{{ $student->profile_image ? asset('storage/profile_image/' . $student->profile_image) : asset('img/default-avatar.png') }}"
                                        onerror="this.onerror=null;this.src='{{ asset('img/default-avatar.png') }}'">
                                </div>
                                <div class="fw-bold mt-3">{{ $student->name }}</div>
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
                                <span class="text-danger error-message" id="status-error-{{ $key }}"
                                    style="display: none;">Please select a status.</span>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        @if ($students->isNotEmpty())
            <div class="text-end p-3 mt-5">
                @if (Carbon::parse($date)->format('Y-m') === Carbon::now()->format('Y-m'))
                    <!-- 當前月份才顯示提交按鈕 -->
                    <button type="submit" class="btn btn-success text-white rounded-4">Submit</button>
                @endif
            </div>
            @if (Carbon::parse($date)->format('Y-m') !== Carbon::now()->format('Y-m'))
                <!-- 顯示過期消息 -->
                <div class="text-danger text-center p-3">
                    Attendance records for previous months cannot be modified.
                </div>
            @endif
        @endif

    </form>

    @if ($students->isEmpty())
        <div class="text-center">
            <img class="no-data-found-icon" src="{{ asset('img/no-data-found.png') }}">
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

            // 檢查日期是否屬於當前月份
            const isSameMonth =
                currentDate.getFullYear() === attendanceDate.getFullYear() &&
                currentDate.getMonth() === attendanceDate.getMonth();

            // 如果不是同一個月，阻止提交並隱藏提交按鈕
            if (!isSameMonth) {
                $('#form').on('submit', function(e) {
                    e.preventDefault(); // 阻止表單提交
                    alert('Attendance records for previous months cannot be modified.');
                });

                $('.btn-success').hide(); // 隱藏提交按鈕
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
            <i class="fa-solid fa-check save-icon ms-2" data-student-id="${studentId}" style="font-size: 16px;"></i>
            <i class="fa-solid fa-xmark cancel-icon ms-2" data-student-id="${studentId}" style="font-size: 16px;"></i>
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
                <i class="fa-solid fa-check save-icon ms-2" data-student-id="${studentId}"></i>
                <i class="fa-solid fa-xmark cancel-icon ms-2" data-student-id="${studentId}"></i>
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
            });
        });
    </script>
@endpush

<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use App\Enums\Status;
use App\Enums\UserType;
use App\Services\Service;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Repositories\ClassRepository;
use App\Repositories\HolidayRepository;
use App\Repositories\StudentRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Repositories\AttendanceRepository;

class AttendanceAdminService extends Service
{
    protected $_attendanceRepository;
    protected $_classRepository;
    protected $_studentRepository;
    protected $_holidayRepository;

    public function __construct(
        AttendanceRepository $attendanceRepository,
        ClassRepository $classRepository,
        StudentRepository $studentRepository,
        HolidayRepository $holidayRepository,
    ) {
        $this->_attendanceRepository = $attendanceRepository;
        $this->_classRepository = $classRepository;
        $this->_studentRepository = $studentRepository;
        $this->_holidayRepository = $holidayRepository;
    }

    public function createOrUpdateAttendance($classId, $date, $data)
    {
        DB::beginTransaction();
        try {
            $today = Carbon::now();
            $attendanceDate = Carbon::parse($date);
            $holidayDate =  $this->getIsDateHoliday($date);

            if ($holidayDate === true) {
                throw new Exception('Attendance cannot be modified on a holiday.');
            }

            if ($attendanceDate->isFuture()) {
                throw new Exception('Attendance cannot be modified for future dates.');
            }

            if (!Auth::check()) {
                throw new Exception('Unauthorized action.');
            }

            if (Auth::user()->hasRole(UserType::Admin()->key)) {
                if ($attendanceDate->format('Y-m') !== $today->format('Y-m')) {
                    throw new Exception('You can only modify attendance records for the current month. Records from previous months are expired.');
                }
            }

            if (Auth::user()->hasRole(UserType::Monitor()->key)) {
                // 檢查是否在過去 7 天內
                $daysDifference = $attendanceDate->isPast()
                    ? $attendanceDate->diffInDays($today) // 過去幾天的差距
                    : 0; // 如果是未來日期，則視為 0 天差距（已在前面阻擋未來日期）

                if ($daysDifference > 7) {
                    throw new Exception('Monitors can only modify attendance records within the last 7 days.');
                }

                if ($attendanceDate->format('Y-m') !== $today->format('Y-m')) {
                    throw new Exception('You can only modify attendance records for the current month. Records from previous months are expired.');
                }
            }

            $validator = Validator::make($data, [
                'students' => 'nullable|array',
                'students.*.student_id' => 'required|exists:students,id',
                'students.*.file' => 'nullable|file',
                'students.*.details' => 'nullable|string|max:255',
                'students.*.status' => 'required|in:' . implode(",", Status::getKeys()),
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            $class = $this->_classRepository->getByIdWithClassDetails($classId);
            if ($class == null) {
                throw new Exception('Class not found.');
            }

            if (Auth::user()->hasRole(UserType::SuperAdmin()->key)) {
                // SuperAdmin 无需额外验证
            } elseif (Auth::user()->hasRole(UserType::Admin()->key)) {
                if ($class->user_id !== Auth::user()->id) {
                    throw new Exception('You are not authorized to manage attendance for this class');
                }
            } elseif (Auth::user()->hasRole(UserType::Monitor()->key)) {
                $student = $this->_studentRepository->getById(Auth::user()->student_id);
                if (!$student || $student->class_id != $classId) {
                    throw new Exception('You are not authorized to manage attendance for this class');
                }
            } else {
                throw new Exception('You do not have permission to perform this action');
            }

            $existingAttendance = $this->_attendanceRepository->getAttendanceByClassId($classId, $date)->keyBy('student_id') ?? collect();

            $toUpdate = [];
            $toCreate = [];

            foreach ($data['students'] as $studentData) {
                $fileStatus = $studentData['file_status'] ?? null;

                // 如果文件状态为 'edited' 且没有新文件，删除已有文件
                if ($fileStatus === 'edited' && empty($studentData['file'])) {
                    $existingRecord = $existingAttendance->get($studentData['student_id']);
                    if ($existingRecord && $existingRecord->file) {
                        Storage::delete('public/attendance_files/' . $existingRecord->file);
                        $studentData['file'] = null; // 设置数据库字段为 null
                    }
                } elseif (!empty($studentData['file']) && $studentData['file'] instanceof \Illuminate\Http\UploadedFile) {
                    // 上传新的文件
                    $fileName = $this->generateFileName();
                    $fileExtension = $studentData['file']->extension();
                    $finalFileName = $fileName . '.' . $fileExtension;

                    $studentData['file']->storeAs('public/attendance_files', $finalFileName);

                    // 删除旧文件
                    $existingRecord = $existingAttendance->get($studentData['student_id']);
                    if ($existingRecord && $existingRecord->file) {
                        Storage::delete('public/attendance_files/' . $existingRecord->file);
                    }

                    $studentData['file'] = $finalFileName;
                } elseif ($studentData['status'] === 'Present') {
                    // Status is 'Present', set file to null
                    $studentData['file'] = null;
                } else {
                    // 如果没有触发编辑，不修改文件字段
                    unset($studentData['file']);
                }

                if ($existingAttendance->has($studentData['student_id'])) {
                    $toUpdate[] = array_merge($studentData, ['class_id' => $classId]);
                } else {
                    $toCreate[] = array_merge($studentData, ['class_id' => $classId]);
                }
            }

            if (!empty($toUpdate)) {
                $this->_attendanceRepository->bulkUpdate($toUpdate, $attendanceDate);
            }

            if (!empty($toCreate)) {
                $this->_attendanceRepository->bulkSave($toCreate, $attendanceDate);
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            $errorMessage = $e->getMessage() ?: "Fail to update students attendance.";
            array_push($this->_errorMessage, $errorMessage);
            DB::rollBack();
            return null;
        }
    }



    public function getStatusCountsByClassId($classId, $date)
    {
        $statusCounts = $this->_attendanceRepository->getStatusCountsByClassId($classId, $date);
        $result = [];
        foreach (Status::asArray() as $statusKey => $statusValue) {
            $result[$statusKey] = $statusCounts[$statusKey] ?? 0;
        }
        return $result;
    }

    public function getStudentsByStatus($classId, $date)
    {
        try {
            return $this->_attendanceRepository->getStudentsByStatus($classId, $date);
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get students by status.");
            return [];
        }
    }

    public function getLatestAttendanceUpdatedAt($classId, $date)
    {
        try {
            return $this->_attendanceRepository->getLatestUpdatedAt($classId, $date);
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get latest updated_at.");
            return null;
        }
    }

    public function getArrivedCountByClassId($classId, $date)
    {
        try {
            return $this->_attendanceRepository->getArrivedCountByClassId($classId, $date);
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get arrived count.");
            return 0;
        }
    }

    public function getStatusCounts($date)
    {
        try {
            return $this->_attendanceRepository->getStatusCounts($date);
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get status status.");
            return 0;
        }
    }

    public function getByStudentAndClassAndDate($studentId, $classId, $date)
    {
        try {
            return $this->_attendanceRepository->getByStudentAndClassAndDate($studentId, $classId, $date);
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get attendance data.");
            return null;
        }
    }
    public function getIsDateHoliday($date)
    {
        try {
            $formattedDate = Carbon::parse($date)->format('Y-m-d'); // 格式化日期
            $isHoliday = $this->_holidayRepository->isDateHoliday($formattedDate);

            return [
                'is_holiday' => $isHoliday,
            ];
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get holiay details.");

            return null;
        }
    }


    private function generateFileName()
    {
        return Str::random(5) . Str::uuid() . Str::random(5);
    }
}

<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use App\Enums\Status;
use App\Enums\UserType;
use App\Services\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Repositories\ClassRepository;
use App\Repositories\StudentRepository;
use Illuminate\Support\Facades\Validator;
use App\Repositories\AttendanceRepository;

class AttendanceAdminService extends Service
{
    protected $_attendanceRepository;
    protected $_classRepository;
    protected $_studentRepository;

    public function __construct(
        AttendanceRepository $attendanceRepository,
        ClassRepository $classRepository,
        StudentRepository $studentRepository,
    ) {
        $this->_attendanceRepository = $attendanceRepository;
        $this->_classRepository = $classRepository;
        $this->_studentRepository = $studentRepository;
    }

    public function createOrUpdateAttendance($classId, $date, $data)
    {
        DB::beginTransaction();
        try {
            $today = Carbon::now();
            $attendanceDate = Carbon::parse($date);

            if ($attendanceDate->format('Y-m') !== $today->format('Y-m')) {
                throw new Exception('You can only modify attendance records for the current month. Records from previous months are expired.');
            }

            $validator = Validator::make($data, [
                'students' => 'nullable|array',
                'students.*.student_id' => 'required|exists:students,id',
                'students.*.details' => 'nullable|string|max:255',
                'students.*.status' => 'required|in:' . implode(",", Status::getKeys()),
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            if (!Auth::check()) {
                throw new Exception('Unauthorized action.');
            }

            $class = $this->_classRepository->getById($classId);
            if ($class == null) {
                throw new Exception('Class not found.');
            }

            if (Auth::user()->hasRole(UserType::SuperAdmin()->key)) {
                // SuperAdmin 无需额外验证
            } elseif (Auth::user()->hasRole(UserType::Admin()->key)) {
                if ($class->user_id !== Auth::user()->id) {
                    throw new Exception('You are not authorized to manage attendance for this class.');
                }
            } elseif (Auth::user()->hasRole(UserType::Monitor()->key)) {
                $student = $this->_studentRepository->getById(Auth::user()->student_id);
                if (!$student || $student->class_id !== $classId) {
                    throw new Exception('You are not authorized to manage attendance for this class.');
                }
            } else {
                throw new Exception('You do not have permission to perform this action.');
            }

            $existingAttendance = $this->_attendanceRepository->getByClassId($classId, $date)->keyBy('student_id');

            $toUpdate = [];
            $toCreate = [];

            foreach ($data['students'] as $studentData) {
                if ($existingAttendance->has($studentData['student_id'])) {
                    $toUpdate[] = array_merge($studentData, ['class_id' => $classId]);
                } else {
                    $toCreate[] = array_merge($studentData, ['class_id' => $classId]);
                }
            }

            if (!empty($toUpdate)) {
                $this->_attendanceRepository->bulkUpdate($toUpdate, $date);
            }

            if (!empty($toCreate)) {
                $this->_attendanceRepository->bulkSave($toCreate);
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to update students attendance.");
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
}

<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use App\Services\Service;
use App\Repositories\ClassRepository;
use App\Repositories\StudentRepository;
use App\Repositories\AttendanceRepository;

class DashboardAdminService extends Service
{
    private $_classRepository;
    private $_studentRepository;
    private $_attendanceRepository;

    public function __construct(
        ClassRepository $classRepository,
        StudentRepository $studentRepository,
        AttendanceRepository $attendanceRepository
    ) {
        $this->_classRepository = $classRepository;
        $this->_studentRepository = $studentRepository;
        $this->_attendanceRepository = $attendanceRepository;
    }

    public function getDashboardData($date)
    {
        try {
            $formattedDate = Carbon::parse($date)->format('Y-m-d'); // 格式化日期

            // 从 Repository 层获取数据
            $classCount = $this->_classRepository->getClassCount($formattedDate);
            $attendedClasses = $this->_attendanceRepository->getAttendedClassCount($formattedDate);

            $totalStudents = $this->_studentRepository->getStudentCount($formattedDate);
            $attendedStudents = $this->_attendanceRepository->getAttendedStudentCount($formattedDate);
            $unavailableStudents = $this->_attendanceRepository->getUnavailableStudentCount($formattedDate);

            // 返回整理后的数据
            return [
                'date' => $formattedDate,  // 确保返回格式化的日期
                'class_summary' => [
                    'attended' => $attendedClasses,
                    'total' => $classCount,
                ],
                'student_summary' => [
                    'attended' => $attendedStudents,
                    'total' => $totalStudents,
                    'unavailable' => $unavailableStudents,
                ],
            ];
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get dashboard details.");

            return null;
        }
    }
}

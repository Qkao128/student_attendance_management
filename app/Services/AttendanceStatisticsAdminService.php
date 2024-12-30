<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use App\Services\Service;
use App\Repositories\ClassRepository;
use App\Repositories\StudentRepository;
use App\Repositories\AttendanceRepository;
use App\Repositories\HolidayRepository;

class AttendanceStatisticsAdminService extends Service
{
    private $_classRepository;
    private $_studentRepository;
    private $_attendanceRepository;
    protected $_holidayRepository;

    public function __construct(
        ClassRepository $classRepository,
        StudentRepository $studentRepository,
        AttendanceRepository $attendanceRepository,
        HolidayRepository $holidayRepository,
    ) {
        $this->_classRepository = $classRepository;
        $this->_studentRepository = $studentRepository;
        $this->_attendanceRepository = $attendanceRepository;
        $this->_holidayRepository = $holidayRepository;
    }

    public function getDashboardData($date)
    {
        try {
            $formattedDate = Carbon::parse($date)->format('Y-m-d');

            $isHoliday = $this->_holidayRepository->isDateHoliday($formattedDate);

            $classCount = $this->_classRepository->getClassCount();
            $attendedClasses = $this->_attendanceRepository->getAttendedClassCount($formattedDate);

            $totalStudents = $this->_studentRepository->getStudentCount($formattedDate);
            $attendedStudents = $this->_attendanceRepository->getAttendedStudentCount($formattedDate);
            $unavailableStudents = $this->_attendanceRepository->getUnavailableStudentCount($formattedDate);

            return [
                'date' => $formattedDate,
                'is_holiday' => $isHoliday,
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


    public function getMonthlyStatusCounts(string $month, ?int $courseId)
    {
        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth = Carbon::parse($month)->endOfMonth();

        $holidays = $this->_holidayRepository->getHolidaysInRange($startOfMonth, $endOfMonth);
        $holidayDates = $this->getHolidayDates($holidays);

        $attendanceData = $this->_attendanceRepository->getMonthlyAttendanceData($startOfMonth, $endOfMonth, $courseId, $holidayDates);

        return [
            'Present' => $attendanceData['Present'] ?? 0,
            'Absence' => $attendanceData['Absence'] ?? 0,
            'Late' => $attendanceData['Late'] ?? 0,
            'Medical' => $attendanceData['Medical'] ?? 0,
            'LeaveApproval' => $attendanceData['LeaveApproval'] ?? 0,
        ];
    }

    public function getMonthlyUnavailableStudentCount(string $month, ?int $courseId)
    {
        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth = Carbon::parse($month)->endOfMonth();

        $holidays = $this->_holidayRepository->getHolidaysInRange($startOfMonth, $endOfMonth);
        $holidayDates = $this->getHolidayDates($holidays);

        return $this->_attendanceRepository->getMonthlyUnavailableStudentCount($startOfMonth, $endOfMonth, $courseId, $holidayDates);
    }

    public function getPresentRelatedCount(string $month, ?int $courseId)
    {
        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth = Carbon::parse($month)->endOfMonth();

        $holidays = $this->_holidayRepository->getHolidaysInRange($startOfMonth, $endOfMonth);
        $holidayDates = $this->getHolidayDates($holidays);

        return $this->_attendanceRepository->getPresentRelatedCount($startOfMonth, $endOfMonth, $courseId, $holidayDates);
    }

    public function getClassCountByCourse(?int $courseId)
    {
        return $this->_attendanceRepository->getClassCountByCourse($courseId);
    }

    private function getHolidayDates(array $holidays): array
    {
        $holidayDates = [];
        foreach ($holidays as $holiday) {
            $range = Carbon::parse($holiday->date_from)->daysUntil($holiday->date_to);
            foreach ($range as $day) {
                $holidayDates[] = $day->toDateString();
            }
        }
        return $holidayDates;
    }


    public function getAttendanceTable($classId, Carbon $startOfMonth, Carbon $endOfMonth)
    {
        $students = $this->_studentRepository->getByClassId($classId)->sortBy('name');
        $holidays = $this->_holidayRepository->getHolidaysInRange($startOfMonth, $endOfMonth);

        $holidayDates = [];
        foreach ($holidays as $holiday) {
            $range = Carbon::parse($holiday->date_from)->daysUntil($holiday->date_to);
            foreach ($range as $day) {
                $holidayDates[] = $day->toDateString();
            }
        }

        $attendanceRecords = $this->_attendanceRepository->getAttendanceRecords($classId, $startOfMonth, $endOfMonth);

        $attendanceTable = [];
        $nonPresentDetails = []; // 用於存儲非 Present 狀態的詳細數據

        foreach ($students as $student) {
            $row = [
                'student_name' => $student->name,
                'attendance' => [],
            ];

            foreach (Carbon::parse($startOfMonth)->daysUntil($endOfMonth) as $day) {
                $date = $day->toDateString();

                if (in_array($date, $holidayDates)) {
                    $row['attendance'][$date] = 'H'; // 假期標記為 H
                } else {
                    $status = $attendanceRecords[$student->id][$date] ?? '-';
                    $row['attendance'][$date] = $this->getStatusAbbreviation($status);

                    // 如果狀態不是 Present，收集詳細數據
                    if ($status !== 'Present' && $status !== '-') {
                        $details = $attendanceRecords[$student->id]['details'][$date] ?? 'N/A';
                        $nonPresentDetails[] = [
                            'date' => $date,
                            'student_name' => $student->name,
                            'status' => $this->getStatusAbbreviation($status),
                            'reason' => $details,
                        ];
                    }
                }
            }

            $attendanceTable[] = $row;
        }

        $nonPresentDetails = collect($nonPresentDetails)->sortBy('date')->values()->toArray(); // 按日期排序
        return [
            'table' => $attendanceTable,
            'nonPresentDetails' => $nonPresentDetails,
        ];
    }


    private function getStatusAbbreviation($status)
    {
        return match ($status) {
            'Present' => 'P',
            'Absence' => 'A',
            'Late' => 'L',
            'Medical' => 'MC',
            'LeaveApproval' => 'AP',
            default => '-',
        };
    }
}

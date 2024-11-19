<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $class_id
 * @property int $student_id
 * @property string $attendance_date
 * @property string $attendance_status
 * @property string|null $details
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereAttendanceDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereAttendanceStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereUpdatedAt($value)
 */
	class Attendance extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $class_id
 * @property int $total_students
 * @property int $present_count
 * @property int $absent_count
 * @property int $late_count
 * @property string $attendance_rate
 * @property string $late_rate
 * @property string $absent_rate
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance_statistics newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance_statistics newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance_statistics query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance_statistics whereAbsentCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance_statistics whereAbsentRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance_statistics whereAttendanceRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance_statistics whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance_statistics whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance_statistics whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance_statistics whereLateCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance_statistics whereLateRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance_statistics wherePresentCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance_statistics whereTotalStudents($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance_statistics whereUpdatedAt($value)
 */
	class Attendance_statistics extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $class_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassTeacher newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassTeacher newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassTeacher query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassTeacher whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassTeacher whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassTeacher whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassTeacher whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassTeacher whereUserId($value)
 */
	class ClassTeacher extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property int $course_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ClassTeacher|null $classTeacher
 * @property-read \App\Models\Course $course
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classes query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classes whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classes whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classes whereUpdatedAt($value)
 */
	class Classes extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereUpdatedAt($value)
 */
	class Course extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $class_id
 * @property string|null $profile_image
 * @property string $name
 * @property string $gender
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereProfileImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereUpdatedAt($value)
 */
	class Student extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $profile_image
 * @property string $username
 * @property string $email
 * @property string $password
 * @property int|null $teacher_user_id
 * @property int|null $student_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfileImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTeacherUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent {}
}


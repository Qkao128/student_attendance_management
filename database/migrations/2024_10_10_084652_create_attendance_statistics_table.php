<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendance_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes');
            $table->integer('total_students'); // 總學生數
            $table->integer('present_count');  // 出席人數
            $table->integer('absent_count');   // 缺席人數
            $table->integer('late_count');     // 遲到人數
            $table->decimal('attendance_rate', 5, 2); // 出勤率 (百分比, 保留兩位小數)
            $table->decimal('late_rate', 5, 2); // 遲到率 (百分比, 保留兩位小數)
            $table->decimal('absent_rate', 5, 2); // 缺席率 (百分比, 保留兩位小數)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_statistics');
    }
};

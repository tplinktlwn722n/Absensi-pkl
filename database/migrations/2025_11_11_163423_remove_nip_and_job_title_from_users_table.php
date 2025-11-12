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
        Schema::table('users', function (Blueprint $table) {
            // Add nip field
            $table->string('nip')->nullable()->after('name');
            
            // Add division_id (for employee division/department)
            $table->foreignId('division_id')->nullable()->constrained('divisions')->after('major_id');
            
            // Add education_id (for last education level)
            $table->foreignId('education_id')->nullable()->constrained('educations')->after('division_id');
            
            // Add job_title_id (for employee job title/position)
            $table->foreignId('job_title_id')->nullable()->constrained('job_titles')->after('education_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['division_id']);
            $table->dropForeign(['education_id']);
            $table->dropForeign(['job_title_id']);
            $table->dropColumn(['nip', 'division_id', 'education_id', 'job_title_id']);
        });
    }
};

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
        if (!Schema::hasColumn('users', 'employeeNum')) {
            $table->string('employeeNum', 50)->unique()->after('id');
        }
        if (!Schema::hasColumn('users', 'firstName')) {
            $table->string('firstName', 50)->after('email');
        }
        if (!Schema::hasColumn('users', 'lastName')) {
            $table->string('lastName', 50)->after('firstName');
        }
        if (!Schema::hasColumn('users', 'middleName')) {
            $table->string('middleName', 50)->nullable()->after('lastName');
        }
        if (!Schema::hasColumn('users', 'role')) {
            $table->enum('role', ['Employee', 'HR', 'Admin'])->after('middleName');
        }
        if (!Schema::hasColumn('users', 'sex')) {
            $table->enum('sex', ['Male', 'Female'])->after('role');
        }
        if (!Schema::hasColumn('users', 'age')) {
            $table->date('age')->after('sex');
        }
        if (!Schema::hasColumn('users', 'profile_picture')) {
            $table->binary('profile_picture')->nullable()->after('age');
        }
        if (!Schema::hasColumn('users', 'status')) {
            $table->enum('status', ['Active', 'Deactivated'])->default('Active')->after('profile_picture');
        }
    });
}



    /**
     * Reverse the migrations.
     */
  public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        if (Schema::hasColumn('users', 'employeeNum')) {
            $table->dropColumn('employeeNum');
        }
        if (Schema::hasColumn('users', 'firstName')) {
            $table->dropColumn('firstName');
        }
        if (Schema::hasColumn('users', 'lastName')) {
            $table->dropColumn('lastName');
        }
        if (Schema::hasColumn('users', 'middleName')) {
            $table->dropColumn('middleName');
        }
        if (Schema::hasColumn('users', 'role')) {
            $table->dropColumn('role');
        }
        if (Schema::hasColumn('users', 'sex')) {
            $table->dropColumn('sex');
        }
        if (Schema::hasColumn('users', 'age')) {
            $table->dropColumn('age');
        }
        if (Schema::hasColumn('users', 'profile_picture')) {
            $table->dropColumn('profile_picture');
        }
        if (Schema::hasColumn('users', 'status')) {
            $table->dropColumn('status');
        }
    });
}


};

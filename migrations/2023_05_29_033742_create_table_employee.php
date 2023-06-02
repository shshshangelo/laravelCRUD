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
        Schema::create('employee', function (Blueprint $table) {
            $table->string('EMP_ID')->primary();
            $table->string('FNAME')->nullable();
            $table->string('LNAME')->nullable();
            $table->string('BIRTHDATE')->nullable();
            $table->string('AGE')->nullable();
            $table->string('GENDER')->nullable();
            $table->string('PHONENUM')->nullable();
            $table->string('EMAILADD')->nullable();
            $table->string('EDUCLVL')->nullable();
            $table->string('JOBTITLE')->nullable();
            $table->string('EMPSTARTDATE')->nullable();
            $table->string('EMPSTATUS')->nullable();
            $table->string('WORKSCHED')->nullable();
            $table->string('MANAGERSNAME')->nullable();
            $table->string('EMERGENCYCONTACTINFO')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee');
    }
};

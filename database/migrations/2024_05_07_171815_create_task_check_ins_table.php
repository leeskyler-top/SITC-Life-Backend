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
        Schema::create('task_check_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId("task_id")->constrained()->cascadeOnDelete();
            $table->foreignId("user_id")->constrained();
            $table->text("check_in_url")->nullable();
            $table->date("check_in_time")->nullable();
            $table->string("afl_type")->nullable();
            $table->text("afl_url")->nullable();
            $table->enum("afl_status", ['pending', 'rejected', 'agreed'])->default('pending');
            $table->unsignedBigInteger('audit_id')->nullable();
            $table->foreign('audit_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_check_ins');
    }
};

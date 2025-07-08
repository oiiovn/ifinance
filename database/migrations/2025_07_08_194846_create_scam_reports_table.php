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
        Schema::create('scam_reports', function (Blueprint $table) {
            $table->id();
            $table->string('scammer_name');
            $table->string('scammer_account');
            $table->string('bank');
            $table->string('scammer_facebook')->nullable();
            $table->text('content');
            $table->string('reporter');
            $table->string('reporter_zalo');
            $table->enum('confirm_type', ['group', 'victim']);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scam_reports');
    }
};

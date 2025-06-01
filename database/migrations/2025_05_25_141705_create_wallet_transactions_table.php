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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            
            // Cho phép null và thiết lập quan hệ
            $table->unsignedBigInteger('from_wallet_id')->nullable();
            $table->unsignedBigInteger('to_wallet_id')->nullable();

            $table->foreign('from_wallet_id')->references('id')->on('wallets')->onDelete('cascade');
            $table->foreign('to_wallet_id')->references('id')->on('wallets')->onDelete('cascade');

            $table->decimal('amount', 15, 2);
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};

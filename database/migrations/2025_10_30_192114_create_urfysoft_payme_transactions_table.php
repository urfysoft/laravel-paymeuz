<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tpetry\PostgresqlEnhanced\Expressions\Uuid7;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('urfysoft_payme_transactions', function (Blueprint $table) {
            $table->uuid()->default(new Uuid7)->primary();
            $table->string('payme_transaction_id', 25)->unique();
            $table->string('payme_time', 13);
            $table->timestamp('payme_time_datetime');
            $table->timestamp('perform_time')->nullable();
            $table->timestamp('cancel_time')->nullable();
            $table->bigInteger('amount');
            $table->tinyInteger('state');
            $table->tinyInteger('reason')->nullable();
            $table->json('receivers')->nullable();
            $table->string('order_id');
            $table->json('account')->nullable();
            $table->timestamps();

            $table->index('order_id');
            $table->index('state');
            $table->index('payme_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('urfysoft_payme_transactions');
    }
};

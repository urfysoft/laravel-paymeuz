<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tpetry\PostgresqlEnhanced\Expressions\Uuid7;

return new class extends Migration
{
    public function up()
    {
        Schema::create('urfysoft_payme_receipts', function (Blueprint $table) {
            $table->uuid('id')->default(new Uuid7)->primary();
            $table->string('receipt_id')->unique();
            $table->string('order_id');
            $table->bigInteger('amount');
            $table->integer('state');
            $table->timestamp('create_time')->nullable();
            $table->timestamp('pay_time')->nullable();
            $table->timestamp('cancel_time')->nullable();
            $table->json('account')->nullable();
            $table->json('detail')->nullable();
            $table->json('card')->nullable();
            $table->timestamps();

            $table->index('order_id');
            $table->index('state');
            $table->index('create_time');
        });
    }

    public function down()
    {
        Schema::dropIfExists('urfysoft_payme_receipts');
    }
};

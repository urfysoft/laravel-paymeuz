<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tpetry\PostgresqlEnhanced\Expressions\Uuid7;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('urfysoft_payme_cards', function (Blueprint $table) {
            $table->uuid()->default(new Uuid7)->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('token');
            $table->string('card_number', 16);
            $table->string('expire', 4);
            $table->boolean('verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('verified');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('urfysoft_payme_cards');
    }
};

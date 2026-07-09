<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAiGenerationsTable extends Migration
{
    public function up()
    {
        Schema::create('ai_generations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 32);
            $table->string('title')->nullable();
            $table->longText('prompt');
            $table->json('prompt_parameters')->nullable();
            $table->longText('generated_text')->nullable();
            $table->string('status', 32)->default('pending');
            $table->string('provider', 64)->nullable();
            $table->string('model', 128)->nullable();
            $table->string('provider_request_id', 191)->nullable();
            $table->unsignedInteger('prompt_tokens')->nullable();
            $table->unsignedInteger('completion_tokens')->nullable();
            $table->unsignedInteger('total_tokens')->nullable();
            $table->decimal('cost_usd', 10, 6)->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'type', 'created_at']);
            $table->index(['user_id', 'status', 'created_at']);
            $table->index(['provider', 'model']);
            $table->index('provider_request_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ai_generations');
    }
}

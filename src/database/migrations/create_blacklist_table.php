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
        Schema::create('spamguard_blacklist', function (Blueprint $table) {
            $table->id();
            $table->string('text');
            $table->timestamp('created_at')->useCurrent();
        });

        $seeder = new \InigoPascall\SpamGuard\database\seeders\BlackListTableSeeder();
        $seeder->run();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spamguard_blacklist');
    }
};

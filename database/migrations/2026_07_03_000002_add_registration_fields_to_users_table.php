<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('registration_type')->default('individual')->after('role');
            $table->string('nida')->nullable()->after('registration_type');
            $table->foreignId('hub_id')->nullable()->after('nida')->constrained('hub')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['hub_id']);
            $table->dropColumn(['registration_type', 'nida', 'hub_id']);
        });
    }
};

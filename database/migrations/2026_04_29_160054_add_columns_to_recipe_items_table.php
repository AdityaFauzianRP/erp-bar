<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recipe_items', function (Blueprint $table) {
            $table->decimal('hpp', 15, 2)->default(0)->after('unit_id');
            $table->text('kandungan_nutrisi')->nullable()->after('hpp');
        });
    }

    public function down(): void
    {
        Schema::table('recipe_items', function (Blueprint $table) {
            $table->dropColumn(['hpp', 'kandungan_nutrisi']);
        });
    }
};

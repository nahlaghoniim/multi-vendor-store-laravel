<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // First, clear any existing data to avoid geometry errors
        DB::statement('TRUNCATE TABLE deliveries');
        
        // Drop the problematic POINT column
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn('current_location');
        });

        // Add simple decimal columns
        Schema::table('deliveries', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('order_id');
            $table->decimal('longitude', 11, 7)->nullable()->after('latitude');
        });
    }

    public function down()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
            $table->point('current_location')->nullable();
        });
    }
};
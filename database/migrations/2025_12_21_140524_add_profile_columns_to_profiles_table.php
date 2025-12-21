<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('profiles', 'first_name')) {
                $table->string('first_name')->after('user_id');
            }
            if (!Schema::hasColumn('profiles', 'last_name')) {
                $table->string('last_name')->after('first_name');
            }
            if (!Schema::hasColumn('profiles', 'birthday')) {
                $table->date('birthday')->nullable()->after('last_name');
            }
            if (!Schema::hasColumn('profiles', 'gender')) {
                $table->enum('gender', ['male', 'female'])->nullable()->after('birthday');
            }
            if (!Schema::hasColumn('profiles', 'street_address')) {
                $table->string('street_address')->nullable()->after('gender');
            }
            if (!Schema::hasColumn('profiles', 'city')) {
                $table->string('city')->nullable()->after('street_address');
            }
            if (!Schema::hasColumn('profiles', 'state')) {
                $table->string('state')->nullable()->after('city');
            }
            if (!Schema::hasColumn('profiles', 'postal_code')) {
                $table->string('postal_code')->nullable()->after('state');
            }
            if (!Schema::hasColumn('profiles', 'country')) {
                $table->char('country', 2)->default('EG')->after('postal_code');
            }
            if (!Schema::hasColumn('profiles', 'locale')) {
                $table->char('locale', 2)->default('en')->after('country');
            }
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn([
                'first_name','last_name','birthday','gender',
                'street_address','city','state','postal_code',
                'country','locale'
            ]);
        });
    }
};

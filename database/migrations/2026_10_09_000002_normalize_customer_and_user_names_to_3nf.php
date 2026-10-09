<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Normalize customers table to 3NF
        Schema::table('customers', function (Blueprint $table) {
            $table->string('first_name', 100)->nullable()->after('name');
            $table->string('last_name', 100)->nullable()->after('first_name');
        });

        // Split existing customer names into first_name and last_name
        $customers = DB::table('customers')->get();
        foreach ($customers as $c) {
            $rawName = trim($c->name ?? '');
            if (!empty($rawName)) {
                $parts = explode(' ', $rawName);
                if (count($parts) > 1) {
                    $lastName = array_pop($parts);
                    $firstName = implode(' ', $parts);
                } else {
                    $firstName = $rawName;
                    $lastName = '';
                }
                DB::table('customers')->where('id', $c->id)->update([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                ]);
            }
        }

        // 2. Normalize users table to 3NF
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name', 100)->nullable()->after('name');
            $table->string('last_name', 100)->nullable()->after('first_name');
        });

        // Split existing user names into first_name and last_name
        $users = DB::table('users')->get();
        foreach ($users as $u) {
            $rawName = trim($u->name ?? '');
            if (!empty($rawName)) {
                $parts = explode(' ', $rawName);
                if (count($parts) > 1) {
                    $lastName = array_pop($parts);
                    $firstName = implode(' ', $parts);
                } else {
                    $firstName = $rawName;
                    $lastName = '';
                }
                DB::table('users')->where('id', $u->id)->update([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name']);
        });
    }
};

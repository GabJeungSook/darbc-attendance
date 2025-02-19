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
        Schema::table('members', function (Blueprint $table) {
            $table->string('middle_name')->after('last_name')->nullable();
            $table->text('place_of_birth')->after('spa')->nullable();
            $table->string('occupation')->after('place_of_birth')->nullable();
            $table->string('mothers_maiden_name')->after('occupation')->nullable();
            $table->string('sss_number')->after('mothers_maiden_name')->nullable();
            $table->string('cluster')->after('sss_number')->nullable();
            $table->string('gender')->after('cluster')->nullable();
            $table->string('occupation_details')->after('gender')->nullable();
            $table->string('spouse')->after('occupation_details')->nullable();
            $table->string('tin_number')->after('spouse')->nullable();
            $table->string('status')->after('tin_number')->nullable();
            $table->string('blood_type')->after('status')->nullable();
            $table->json('children')->after('blood_type')->nullable();
            $table->string('philhealth_number')->after('children')->nullable();
            $table->decimal('percentage', 12, 8)->after('philhealth_number')->default(100);
            $table->date('date_of_birth')->after('percentage')->nullable();
            $table->string('religion')->after('date_of_birth')->nullable();
            $table->text('address_line')->after('religion')->nullable();
            $table->integer('dependents_count')->after('address_line')->nullable();
            $table->string('contact_number')->after('dependents_count')->nullable();
            $table->date('deceased_at')->after('contact_number')->nullable();
            $table->string('membership_status')->after('deceased_at')->nullable();
            $table->string('civil_status')->after('deceased_at')->nullable();
            $table->date('application_date')->after('civil_status')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('middle_name');
            $table->dropColumn('place_of_birth');
            $table->dropColumn('occupation');
            $table->dropColumn('mothers_maiden_name');
            $table->dropColumn('sss_number');
            $table->dropColumn('cluster');
            $table->dropColumn('gender');
            $table->dropColumn('occupation_details');
            $table->dropColumn('spouse');
            $table->dropColumn('tin_number');
            $table->dropColumn('status');
            $table->dropColumn('blood_type');
            $table->dropColumn('children');
            $table->dropColumn('philhealth_number');
            $table->dropColumn('percentage');
            $table->dropColumn('date_of_birth');
            $table->dropColumn('religion');
            $table->dropColumn('address_line');
            $table->dropColumn('dependents_count');
            $table->dropColumn('contact_number');
            $table->dropColumn('deceased_at');
            $table->dropColumn('membership_status');
            $table->dropColumn('civil_status');
            $table->dropColumn('application_date');
        });
    }
};

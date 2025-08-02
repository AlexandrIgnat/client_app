<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('
            ALTER TABLE appointments
            DROP FOREIGN KEY client_fk_360714,
            ADD CONSTRAINT appointments_client_id_foreign
            FOREIGN KEY (client_id)
            REFERENCES clients(id)
            ON DELETE CASCADE
            ON UPDATE CASCADE
         ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->foreign('client_id')->references('id')->on('clients');
        });
    }
};

<?php

// File: database/migrations/2024_06_22_000000_create_payments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('notrans');
            $table->string('type');
            $table->string('plat');
            $table->decimal('biaya', 15, 2);
            $table->timestamp('masuk');
            $table->string('jenis');
            $table->string('checkout_link');
            $table->string('external_id');
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
}


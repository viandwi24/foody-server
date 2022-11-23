<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_menus', function (Blueprint $table) {
            // foreign key to transactions table
            $table->foreignId('transaction_id')->constrained()->onDelete('cascade')->primary();

            // foreign key to menus table
            $table->foreignId('menu_id')->constrained()->onDelete('cascade')->primary();

            // data
            $table->integer('quantity');
            $table->float('price');
            $table->float('total');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_menus');
    }
};

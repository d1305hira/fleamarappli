<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('shipping_postal_code')->nullable();
            $table->string('shipping_address')->nullable();
            $table->string('shipping_building')->nullable();
            $table->unsignedTinyInteger('payment_method')->nullable();
            $table->timestamp('purchased_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    if (Schema::hasTable('purchases')) {
        Schema::table('purchases', function (Blueprint $table) {
            if (Schema::hasColumn('purchases', 'item_id')) {
                $table->dropForeign(['item_id']);
                }
            if (Schema::hasColumn('purchases', 'user_id')) {
                $table->dropForeign(['user_id']);
                }
            });

      Schema::dropIfExists('purchases');
      }
    }
}

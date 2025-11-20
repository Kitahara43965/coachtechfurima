<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserItemTable extends Migration
{
    public function up()
    {
        Schema::create('user_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();

            // 用途を区別
            $table->enum('type', ['ownership', 'favorite', 'purchase']);

            $table->integer('purchase_quantity')->default(0);
            $table->integer('price_at_purchase')->default(0);
            $table->timestamp('purchased_at')->nullable();

            $table->foreignId('purchase_method_id')
                ->nullable()
                ->constrained('purchase_methods')
                ->nullOnDelete();
            
            $table->boolean('is_filled_with_delivery_address')->default(false);
            $table->string('delivery_postcode')->nullable();
            $table->string('delivery_address')->nullable();
            $table->string('delivery_building')->nullable();

            $table->timestamps();

            // 同じ種類の重複を防ぐ
            $table->unique(['user_id', 'item_id', 'type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_item');
    }
}

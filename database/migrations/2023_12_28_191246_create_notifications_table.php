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
        Schema::create('notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->uuid('from_id'); // user who pushed the notif
            $table->uuid('for_id')->nullable(); // user who receives the notif
            $table->string('type'); // notif type (violation, transaction, etc)
            $table->string('event'); // main gist ()
            $table->text('description')->nullable(); // implications
            $table->string('img')->nullable(); // cover photo
            $table->timestamp('push_date')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->timestamp('is_read')->nullable();
            $table->timestamp('is_received')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};

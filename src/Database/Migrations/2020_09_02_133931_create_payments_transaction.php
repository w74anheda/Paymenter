<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTransaction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */


    private $tbl_name;


    public function __construct() {
        $this->tbl_name   = config( 'm74_paymenter.tbl_prefix' ).'_transaction';
    }


    public function up()
    {
        Schema::create($this->tbl_name, function (Blueprint $table) {
            $table->id();
            $table->string('user_hash', 200)->index();
            $table->string('bill_hash', 200)->index();
            $table->string('portal')->index();
            $table->string('request_link')->nullable();
            $table->double('amount');
            $table->string('resNum');
            $table->string('refNum')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->json('additional')->nullable();
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
        Schema::dropIfExists($this->tbl_name);
    }
}

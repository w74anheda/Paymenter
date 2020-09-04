<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use M74asoud\Paymenter\Services\UserInstance;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    private $tblName;

    public function __construct()
    {
        $this->tblName = UserInstance::TablePrefix() . '_bills';
    }

    public function up()
    {

        Schema::create($this->tblName, function (Blueprint $table) {
            $table->id();
            $table->string('user_hash', 200)->index();
            $table->string('hash', 200)->unique()->index();
            $table->smallInteger('status')->unsigned()->index();
            $table->double('amount')->unsigned();
            $table->smallInteger('actionType')->comment('action is for pay or recharge')->unsigned()->index();
            $table->bigInteger('wallet_id')->nullable()->unsigned();
            $table->bigInteger('paymenterable_id')->nullable()->unsigned();
            $table->string('paymenterable_type')->nullable();
            $table->integer('type');
            $table->text('description')->nullable();
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
        Schema::dropIfExists($this->tblName);
    }
}

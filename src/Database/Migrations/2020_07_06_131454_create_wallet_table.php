<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use M74asoud\Paymenter\Services\UserInstance;

class CreateWalletTable extends Migration
{
    private $tblName;

    public function __construct() {
        $this->tblName = UserInstance::TablePrefix() . '_wallets';
    }

    public function up() {


        Schema::create($this->tblName, function ( Blueprint $table ) {
            $table->bigIncrements( 'id' );
            $table->string( 'user_hash', 200 )->index();
            $table->double( 'before' );
            $table->double( 'amount' );
            $table->double( 'balance' );
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( $this->tblName );
    }
}

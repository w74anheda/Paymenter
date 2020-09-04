<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHashFieldToUser extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */

    private $tbl_name;
    private $filed_name;


    public function __construct() {
        $this->tbl_name   = config( 'm74_paymenter.users_tbl_name' );
        $this->filed_name = config( 'm74_paymenter.users_hash_filed_name' );
    }

    public function up() {

        Schema::table( $this->tbl_name, function ( Blueprint $table ) {
            $table->string( $this->filed_name, 200 )->default( \Illuminate\Support\Str::uuid() )->index();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( $this->tbl_name, function ( Blueprint $table ) {
            $table->dropColumn( $this->filed_name );
        } );
    }
}

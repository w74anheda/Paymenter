<?php


namespace M74asoud\Paymenter\Services;


class UserInstance {


    public static function isInstanceOfUser( $user ) {
        if ( get_class( $user ) !== self::UserModelClass() ) {
            throw new \InvalidArgumentException();
        }
    }

    public static function UserModelInstance() {
        return resolve( config( 'm74_paymenter.user_model' ) );
    }

    public static function UserModelClass() {
        return config( 'm74_paymenter.user_model' );
    }

    public static function UserHashField() {
        return config( 'm74_paymenter.users_hash_filed_name' );
    }

    public static function UserTableName() {
        return config( 'm74_paymenter.users_tbl_name' );
    }
    public static function TablePrefix() {
        return config( 'm74_paymenter.tbl_prefix' );
    }


}

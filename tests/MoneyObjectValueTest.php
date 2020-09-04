<?php


namespace Tests\Unit;


use M74asoud\Paymenter\ObjectValue\Money;
use Tests\TestCase;

class MoneyObjectValueTest extends TestCase {

    public function test_money_actions() {

        $IRR_money   = new Money( 1000 );
        $IRR_money_2 = new Money( 25000 );
        $IRR_money_3 = new Money( 1000 );
        $IRR_money_4 = new Money( 2000 );
        $IRR_money_5 = new Money( 500 );
        $IRR_money_6 = new Money( 0 );
        $USD_Money   = new Money( 10, 'USD' );

        $this->assertTrue( $IRR_money instanceof Money );
        $this->assertTrue( $IRR_money->getValue() === 1000 );
        $this->assertTrue( $IRR_money->getCurrency() === 'IRR' );
        $this->assertFalse( $IRR_money->equals( $USD_Money ) );
        $this->assertFalse( $IRR_money->equals( $IRR_money_2 ) );
        $this->assertFalse( $IRR_money->equals( $IRR_money_6 ) );
        $this->assertTrue( $IRR_money->equals( $IRR_money_3 ) );
        $this->assertFalse( $IRR_money->isFewer( $IRR_money_5 ) );
        $this->assertFalse( $IRR_money->isFewer( $IRR_money_6 ) );
        $this->assertTrue( $IRR_money->isFewer( $IRR_money_4 ) );
        $this->assertTrue( $IRR_money->isMore( $IRR_money_5 ) );
        $this->assertTrue( $IRR_money->isMore( $IRR_money_6 ) );
        $this->assertFalse( $IRR_money->isMore( $IRR_money_2 ) );

        try {
            $IRR_money->isFewer( $USD_Money );
            $IRR_money->isMore( $USD_Money );
            $IRR_money->reduce( $USD_Money );
            $IRR_money->add( $USD_Money );
        } catch ( \Exception $err ) {
            $this->assertEquals( $err->getCode(), 1001 );
        }

        $this->assertTrue( $IRR_money->reduce( $IRR_money_5 )->getValue() === 500 );
        $this->assertTrue( $IRR_money->reduce( $IRR_money_6 )->getValue() === 1000 );
        $this->assertTrue( $IRR_money->reduce( $IRR_money )->getValue() === 0 );

        $this->assertTrue( $IRR_money->add( $IRR_money_5 )->getValue() === 1500 );
        $this->assertTrue( $IRR_money->add( $IRR_money_6 )->getValue() === 1000 );
        $this->assertTrue( $IRR_money->add( $IRR_money )->getValue() === 2000 );


    }

}

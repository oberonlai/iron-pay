<?php

namespace Irp\Tests;

use PHPUnit\Framework\TestCase;
use Irp\Utility;
use phpmock\phpunit\PHPMock;

/**
 * UtilityTest
 *
 * @group group
 */
class UtilityTest extends TestCase {

	use PHPMock;

	protected function setUp(): void {
		parent::setUp();
		// code
	}

	protected function tearDown(): void {
		// code
	}


	public function test_generate_sign() {
		// Arrange.
		$args = array(
			'nonce_str'       => 71669965,
			'orgno'           => 1265,
			'secondtimestamp' => 1489215551,
			'total_fee'       => 8888,
		);

		$secret   = '12345';
		$expected = 'B59951E5AA2E6FCA1596253E2978ED2B';

		// Act.
		$actual = Utility::generate_sign( $args, $secret );

		// Assert.
		$this->assertEquals( $expected, $actual );
	}

	public function test_get_store_id() {
		// Arrange.
		$get_option = $this->getFunctionMock( 'Irp', 'get_option' );
		$get_option->expects( $this->exactly( 4 ) )
					->with( $this->equalTo( 'irp_payment_orgno' ) )
					->willReturnOnConsecutiveCalls(
						'12345678',
						'',
						'abc123',
						'123'
					);
		$expected        = '12345678';
		$expected_empty  = '未輸入商店代號';
		$expected_number = '商店代號限定數字';
		$expected_length = '商店代號須為 8 碼';

		// Act.
		$actual        = Utility::get_store_id();
		$actual_empty  = Utility::get_store_id();
		$actual_number = Utility::get_store_id();
		$actual_length = Utility::get_store_id();

		// Assert.
		$this->assertEquals( $expected, $actual );
		$this->assertEquals( $expected_empty, $actual_empty );
		$this->assertEquals( $expected_number, $actual_number );
		$this->assertEquals( $expected_length, $actual_length );
	}

}

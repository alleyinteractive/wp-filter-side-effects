<?php
/**
 * Class file for Test_Filter_Side_Effects
 *
 * (c) Alley <info@alley.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package wp-filter-side-effects
 */

namespace Alley\WP;

use Mantle\Testing\Mock_Action;
use Mantle\Testkit\Test_Case;

/**
 * Tests for filter side-effects.
 */
class Test_Filter_Side_Effects extends Test_Case {
	/**
	 * Prefixed test filter name.
	 *
	 * @var string
	 */
	protected const TEST_FILTER = 'alley_test_filter';

	/**
	 * A value known to not be the same as DATA2, for comparisons.
	 *
	 * @var string
	 */
	protected const DATA1 = 'data1';

	/**
	 * A value known to not be the same as DATA1, for comparisons.
	 *
	 * @var string
	 */
	protected const DATA2 = 'data2';

	/**
	 * A ready-made Mock_Action instance that might be enough for most tests.
	 *
	 * @var Mock_Action
	 */
	protected Mock_Action $mock_action;

	/**
	 * Set up.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->mock_action = new Mock_Action();
	}

	/**
	 * Test that the callback function passed as a side-effect fires.
	 */
	public function test_side_effect_fires() {
		$method = 'action';

		add_filter_side_effect( self::TEST_FILTER, [ $this->mock_action, $method ] );

		$this->apply_test_filter( self::DATA1 );

		$this->assertTrue( 1 === $this->mock_action->get_call_count( $method ) );
	}

	/**
	 * Test that the callback function passed as a side-effect can receive all
	 * the values passed to the original filter.
	 */
	public function test_side_effect_receives_args() {
		add_filter_side_effect( self::TEST_FILTER, [ $this->mock_action, 'action' ], 10, 2 );

		$args = [ self::DATA1, self::DATA2 ];

		$this->apply_test_filter( $args );

		$actual = $this->mock_action->get_args();

		$this->assertSame( $args, end( $actual ) );
	}

	/**
	 * Test that the filtered value is not affected by the return value of the side-effect.
	 */
	public function test_filtered_value_is_unchanged() {
		add_filter_side_effect( self::TEST_FILTER, '__return_empty_string' );

		$value = self::DATA2;

		$actual = $this->apply_test_filter( $value );

		$this->assertSame( $value, $actual );
	}

	/**
	 * Apply the test filter with the given arguments.
	 *
	 * @param string|array $args Filtered value and additional arguments, if any.
	 * @return mixed Filtered value.
	 */
	protected function apply_test_filter( $args ) {
		$args = (array) $args;

		// This filter name is prefixed.
		return apply_filters( self::TEST_FILTER, ...$args ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
	}
}

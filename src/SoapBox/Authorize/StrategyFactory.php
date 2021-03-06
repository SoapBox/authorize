<?php namespace SoapBox\Authorize;

use Illuminate\Support\Facades\Lang as Lang;
use SoapBox\Authorize\Exceptions\DuplicateStrategyException;
use SoapBox\Authorize\Exceptions\InvalidStrategyException;

/**
 * Used to handle the registration and retrieval of various strategies from
 * Authorize.
 */
class StrategyFactory {

	/**
	 * The strategies that have been registered with the factory.
	 *
	 * @var Strategy[] $strategies Named array of strategies
	 */
	private static $strategies = array();

	/**
	 * Used to register a new strategy with Authorize
	 *
	 * @throws DuplicateStrategyException If the provided strategy has been
	 *	previously registered.
	 * @throws InvalidStrategyException If the provided strategy is not valid,
	 *	it is not implementing the Strategy interface.
	 *
	 * @param string $name The friendly name of the class that is being
	 *	registered
	 * @param string $klass The class with namespace that is being registered
	 */
	public static function register($name, $klass) {
		if (!in_array('SoapBox\Authorize\Strategy', class_implements($klass))) {
			throw new InvalidStrategyException(
				"The provided class does not implement the 'SoapBox\Authorize\Strategy' interface."
			);
		}
		if ( !array_key_exists($name, self::$strategies) ) {
			self::$strategies[$name] = $klass;
		}
	}

	/**
	 * Used to retrieve the specified strategy for authenticating.
	 *
	 * @param string $strategy The name of the strategy. (i.e. facebook)
	 * @param mixed[] $settings The settings the strategy requires to initialize
	 * @param callable $store A callback that will store a KVP (Key Value Pair).
	 * @param callable $load A callback that will return a value stored with the
	 *	provided key.
	 *
	 * @return Strategy An instance of the strategy requested.
	 */
	public static function get($strategy, $settings = array(), $store = null, $load = null) {
		if (!array_key_exists($strategy, self::$strategies)) {
			throw new InvalidStrategyException(
				"$strategy strategy has not been registered."
			);
		}
		return new self::$strategies[$strategy]($settings, $store, $load);
	}

}

<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

/**
 * Inject instance to load_class() function
 *
 * @param string $classname
 * @param object $instance
 */
function load_class_instance($classname, $instance)
{
	load_class($classname, '', NULL, FALSE, $instance);
}

/**
 * Reset CodeIgniter instance
 */
function reset_instance()
{
	// Reset loaded classes
	load_class('', '', NULL, TRUE);
	is_loaded('', TRUE);

	// Reset config functions
	reset_config();

	// Close db connection
	$CI =& get_instance();
	if (isset($CI->db))
	{
		if (
			$CI->db->dsn !== 'sqlite::memory:'
			&& $CI->db->database !== ':memory:'
		)
		{
			$CI->db->close();
			$CI->db = null;
		}
		else
		{
			// Don't close if SQLite in-memory database
			// If we close it, all tables and stored data will be gone
			load_class_instance('db', $CI->db);
		}
	}

	// Load core classes
	$BM =& load_class('Benchmark', 'core');
	CIPHPUnitTestSuperGlobal::set_Global('BM', $BM);
	$EXT =& load_class('Hooks', 'core');
	CIPHPUnitTestSuperGlobal::set_Global('EXT', $EXT);

	$CFG =& load_class('Config', 'core');
	CIPHPUnitTestSuperGlobal::set_Global('CFG', $CFG);
	// Do we have any manually set config items in the index.php file?
	global $assign_to_config;
	if (isset($assign_to_config) && is_array($assign_to_config))
	{
		foreach ($assign_to_config as $key => $value)
		{
			$CFG->set_item($key, $value);
		}
	}

	$UNI =& load_class('URI', 'core');
	CIPHPUnitTestSuperGlobal::set_Global('UNI', $UNI);
//	$URI =& load_class('Utf8', 'core');
//	CIPHPUnitTestSuperGlobal::set_Global('URI', $URI);
	$RTR =& load_class('Router', 'core');
	CIPHPUnitTestSuperGlobal::set_Global('RTR', $RTR);
	$OUT =& load_class('Output', 'core');
	CIPHPUnitTestSuperGlobal::set_Global('OUT', $OUT);
	$SEC =& load_class('Security', 'core');
	CIPHPUnitTestSuperGlobal::set_Global('SEC', $SEC);
	$IN =& load_class('Input', 'core');
	CIPHPUnitTestSuperGlobal::set_Global('IN', $IN);
	$LANG =& load_class('Lang', 'core');
	CIPHPUnitTestSuperGlobal::set_Global('LANG', $LANG);

	CIPHPUnitTest::loadLoader();
	if (CIPHPUnitTest::wiredesignzHmvcInstalled()) {
		CIPHPUnitTest::loadConfig();
	}

	// Remove CodeIgniter instance
	$CI = new CIPHPUnitTestNullCodeIgniter();

	// Reset Logs
	CIPHPUnitTestLogger::resetLogs();
}

/**
 * Set return value of is_cli() function
 *
 * @param bool $return
 */
function set_is_cli($return)
{
	is_cli($return);
}

/**
 * Reset config functions
 */
function reset_config()
{
	get_config([], TRUE);
	config_item(NULL, TRUE);
}

if ( ! function_exists('is_testing_env'))
{
	/**
	 * Testing Environment or not?
	 *
	 * @return bool
	 */
	function is_testing_env()
	{
		if (ENVIRONMENT === 'testing')
		{
			return TRUE;
		}

		return FALSE;
	}
}

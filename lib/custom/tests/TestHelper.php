<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2014
 */

class TestHelper
{
	private static $_aimeos;
	private static $_context = array();


	public static function bootstrap()
	{
		$mshop = self::_getAimeos();

		$includepaths = $mshop->getIncludePaths();
		$includepaths[] = get_include_path();
		set_include_path( implode( PATH_SEPARATOR, $includepaths ) );
	}


	public static function getContext( $site = 'unittest' )
	{
		if( !isset( self::$_context[$site] ) ) {
			self::$_context[$site] = self::_createContext( $site );
		}

		return clone self::$_context[$site];
	}


	private static function _getAimeos()
	{
		if( !isset( self::$_aimeos ) )
		{
			require_once 'Aimeos.php';
			spl_autoload_register( 'Aimeos::autoload' );

			$extdir = dirname( dirname( dirname( __DIR__ ) ) );
			self::$_aimeos = new Aimeos( array( $extdir ), false );
		}

		return self::$_aimeos;
	}


	/**
	 * Creates a new context item.
	 *
	 * @param string $site Unique site code
	 * @return \MShop_Context_Item_Interface Context object
	 */
	private static function _createContext( $site )
	{
		$ctx = new MShop_Context_Item_Default();
		$mshop = self::_getAimeos();


		$paths = $mshop->getConfigPaths( 'mysql' );
		$paths[] = __DIR__ . DIRECTORY_SEPARATOR . 'config';

		$conf = new MW_Config_Array( array(), $paths );
		$ctx->setConfig( $conf );


		$dbm = new MW_DB_Manager_PDO( $conf );
		$ctx->setDatabaseManager( $dbm );


		$logger = new MW_Logger_File( $site . '.log', MW_Logger_Abstract::DEBUG );
		$ctx->setLogger( $logger );


		$session = new MW_Session_None();
		$ctx->setSession( $session );


		$localeManager = MShop_Locale_Manager_Factory::createManager( $ctx );
		$localeItem = $localeManager->bootstrap( $site, '', '', false );

		$ctx->setLocale( $localeItem );

		$ctx->setEditor( 'zend:unittest' );

		return $ctx;
	}
}

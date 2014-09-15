<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Perf_Config_ZendTest extends MW_Unittest_Testcase
{
	public function testZend()
	{
		if( class_exists( 'Zend_Config' ) === false ) {
			$this->markTestSkipped( 'Class Zend_Config not found' );
		}


		$start = microtime( true );

		$paths = array(
			__DIR__ . DIRECTORY_SEPARATOR . 'one',
			__DIR__ . DIRECTORY_SEPARATOR . 'two',
		);

		for( $i = 0; $i < 1000; $i++ )
		{
			$conf = new MW_Config_Zend( new Zend_Config( array(), true ), $paths );

			$conf->get( 'test/db/host' );
			$conf->get( 'test/db/username' );
			$conf->get( 'test/db/password' );
		}

		$stop = microtime( true );
		echo "\n    config zend: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}
}
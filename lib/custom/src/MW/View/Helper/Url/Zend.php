<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MW
 * @subpackage View
 */


/**
 * View helper class for building URLs using Zend Router.
 *
 * @package MW
 * @subpackage View
 */
class MW_View_Helper_Url_Zend
	extends MW_View_Helper_Abstract
	implements MW_View_Helper_Interface
{
	private $_router;
	private $_serverUrl;


	/**
	 * Initializes the URL view helper.
	 *
	 * @param MW_View_Interface $view View instance with registered view helpers
	 * @param Zend_Controller_Router_Interface $router Zend Router implementation
	 * @param string $serverUrl Url of the server including scheme, host and port
	 */
	public function __construct( $view, Zend_Controller_Router_Interface $router, $serverUrl )
	{
		parent::__construct( $view );

		$this->_router = $router;
		$this->_serverUrl = $serverUrl;
	}


	/**
	 * Returns the URL assembled from the given arguments.
	 *
	 * @param string|null $target Route or page which should be the target of the link (if any)
	 * @param string|null $controller Name of the controller which should be part of the link (if any)
	 * @param string|null $action Name of the action which should be part of the link (if any)
	 * @param array $params Associative list of parameters that should be part of the URL
	 * @param array $trailing Trailing URL parts that are not relevant to identify the resource (for pretty URLs)
	 * @param array $config Additional configuration parameter per URL
	 * @return string Complete URL that can be used in the template
	 */
	public function transform( $target = null, $controller = null, $action = null, array $params = array(), array $trailing = array(), array $config = array() )
	{
		$paramList = array( 'controller' => $controller, 'action' => $action );


		foreach( $params as $key => $value )
		{
			// Slashes in URL parameters confuses the router
			$paramList[$key] = str_replace( '/', '_', $value );

			// Arrays are not supported
			if( is_array( $value ) ) {
				$paramList[$key] = implode( ' ', $value );
			}
		}

		if( !empty( $trailing ) ) {
			$paramList['trailing'] = str_replace( '/', '_', join( '_', $trailing ) );
		}

		$url = $this->_router->assemble( $paramList, $target, true );

		if( isset( $config['absoluteUri'] ) ) {
			$url = $this->_serverUrl . $url;
		}

		return $url;
	}
}
<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Engine;


/**
 * Twig view engine implementation
 *
 * @package MW
 * @subpackage View
 */
class Twig implements Iface
{
	private $env;


	/**
	 * Initializes the view object
	 *
	 * @param \Twig_Environment $env Twig environment object
	 */
	public function __construct( \Twig_Environment $env )
	{
		$this->env = $env;
	}


	/**
	 * Renders the output based on the given template file name and the key/value pairs
	 *
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @param string $filename File name of the view template
	 * @param array $values Associative list of key/value pairs
	 * @return string Output generated by the template
	 * @throws \Aimeos\MW\View\Exception If the template isn't found
	 */
	public function render( \Aimeos\MW\View\Iface $view, $filename, array $values )
	{
		$loader = $this->env->getLoader();

		if( ( $content = @file_get_contents( $filename ) ) === false ) {
			throw new \Aimeos\MW\View\Exception( sprintf( 'Template "%1$s" not found', $filename ) );
		}

		$custom = new \Twig_Loader_Array( array( $filename => $content ) );
		$this->env->setLoader( new \Twig_Loader_Chain( array( $custom, $loader ) ) );

		try
		{
			$template = $this->env->loadTemplate( $filename );
			$content = $template->render( $values );

			foreach( $template->getBlocks() as $key => $block ) {
				$view->block()->set( $key, $block );
			}

			$this->env->setLoader( $loader );

			return $content;
		}
		catch( \Exception $e )
		{
			$this->env->setLoader( $loader );
			throw $e;
		}
	}
}

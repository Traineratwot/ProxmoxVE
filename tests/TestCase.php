<?php
/**
 * This file is part of the ProxmoxVE PHP API wrapper library (unofficial).
 * Created on Wed Feb 28 2024
 *
 * Copyright (c) 2024 IT-Dienstleistungen Drevermann - All Rights Reserved
 *
 * @package Triopsi licence manager
 * @author Daniel Drevermann <info@triopsi.com>
 * @copyright Copyright (c) 2024, IT-Dienstleistungen Drevermann, 2014 César Muñoz <zzantares@gmail.com>
 * @license   http://opensource.org/licenses/MIT The MIT License.
 */

namespace ProxmoxVE;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\TestCase as FrameworkTestCase;

/**
 * ProxmoxVE PHPUnits
 *
 * @author César Muñoz <zzantares@gmail.com>
 */
class TestCase extends FrameworkTestCase {

	/**
	 * Get Proxmox Mock.
	 *
	 * @param  string $method method Name.
	 * @param  mixed  $return Return stream.
	 * @return \PHPUnit\Framework\MockObject\MockObject
	 */
	protected function getMockProxmox( $method = null, $return = null) {
		if ($method) {
			$proxmox = $this->getMockBuilder( 'ProxmoxVE\Proxmox' )
				->onlyMethods( array($method) )
				->disableOriginalConstructor()
				->getMock();

			$proxmox->expects( $this->any() )
				->method( $method )
				->will( $this->returnValue( $return ) );
		} else {
			$proxmox = $this->getMockBuilder( 'ProxmoxVE\Proxmox' )
				->disableOriginalConstructor()
				->getMock();
		}

		return $proxmox;
	}


	/**
	 * Get the Proxmox Class.
	 *
	 * @param resource|string|int|float|bool|StreamInterface|callable|\Iterator|null $response Response Mock.
	 * @return \ProxmoxVE\Proxmox
	 */
	protected function getProxmox( $response ) {
		$httpClient = $this->getMockHttpClient( true, $response );

		$credentials = array(
			'hostname' => 'my.proxmox.tld',
			'username' => 'root',
			'password' => 'toor',
		);

		return new Proxmox( $credentials, null, $httpClient );
	}


	/**
	 * Mock HTTP Client.
	 *
	 * @param boolean                                                                $successfulLogin Success or False.
	 * @param resource|string|int|float|bool|StreamInterface|callable|\Iterator|null $response Response Mock.
	 * @return \GuzzleHttp\Client
	 */
	protected function getMockHttpClient( $successfulLogin, $response = null) {
		if ($successfulLogin) {
			$stream = Utils::streamFor( '{"data":{"CSRFPreventionToken":"csrf","ticket":"ticket","username":"random"}}' );
			$login  = new Response( 202, array('Content-Length' => 0), $stream );
		} else {
			$login = new Response( 400, array('Content-Length' => 0) );
		}

		$responseStream = Utils::streamFor( "{$response}" );

		$mock = new MockHandler(
			array(
			$login,
			new Response( 202, array('Content-Length' => 0), $responseStream ),
			)
		);

		$handler    = HandlerStack::create( $mock );
		$httpClient = new Client( array('handler' => $handler) );

		return $httpClient;
	}
}

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

use GuzzleHttp\Exception\ClientException;
use ProxmoxVE\Exception\MalformedCredentialsException;

/**
 * ProxmoxVE PHPUnits
 *
 * @author César Muñoz <zzantares@gmail.com>
 */
class ProxmoxTest extends TestCase {

	/**
	 * Test bad credentials.
	 */
	public function testExceptionIsThrownIfBadParamsPassed() {
		$this->expectException( MalformedCredentialsException::class );
		new Proxmox( 'bad param' );
	}

	/**
	 * Test bad credentials.
	 */
	public function testExceptionIsThrownWhenNonAssociativeArrayIsGivenAsCredentials() {
		$this->expectException( MalformedCredentialsException::class );
		new Proxmox(
			array(
			'root', 'So Bruce Wayne is alive? or did he died in the explosion?',
			)
		);
	}

	/**
	 * Test bad credentials.
	 */
	public function testExceptionIsThrownWhenIncompleteCredentialsArrayIsPassed() {
		$this->expectException( MalformedCredentialsException::class );
		new Proxmox(
			array(
			'username' => 'root',
			'password' => 'The NSA is watching us! D=',
			)
		);
	}

	/**
	 * Proxmox Test with right credentials.
	 *
	 * @return void
	 */
	public function testGetCredentialsWithAllValues() {
		$data = array(
			'hostname' => 'some.proxmox.tld',
			'username' => 'root',
			'password' => 'I was here',
		);

		$fakeAuthToken = new AuthToken( 'csrf', 'ticket', 'username' );
		$proxmox       = $this->getMockProxmox( 'login', $fakeAuthToken );
		$proxmox->setCredentials( $data );

		$credentials = $proxmox->getCredentials();

		$this->assertEquals( $credentials->getHostname(), $data['hostname'] );
		$this->assertEquals( $credentials->getUsername(), $data['username'] );
		$this->assertEquals( $credentials->getPassword(), $data['password'] );
		$this->assertEquals( $credentials->getRealm(), 'pam' );
		$this->assertEquals( $credentials->getPort(), '8006' );
		$this->assertEquals( $credentials->getSystem(), 'pve' );
	}


	/**
	 * Proxmox Test with right credentials via api
	 *
	 * @return void
	 */
	public function testGetApiCredentialsWithAllValues() {
		$data        = array(
			'hostname' => 'some.proxmox.tld',
			'tokenName' => 'root',
			'tokenKey' => '2543b2f7-bbd9-4013-a972-42fc5b644899',
		);
		$proxmox     = new Proxmox( $data );
		$credentials = $proxmox->getCredentials();

		$this->assertEquals( $credentials->getHostname(), $data['hostname'] );
		$this->assertEquals( $credentials->getUsername(), '' );
		$this->assertEquals( $credentials->getTokenKey(), 'PVEAPIToken=root=2543b2f7-bbd9-4013-a972-42fc5b644899' );
		$this->assertTrue( $credentials->isApi() );
		$this->assertEquals( $credentials->getSystem(), 'pve' );
	}

	/**
	 * Proxmox Mail Gateway Test with right credentials.
	 *
	 * @return void
	 */
	public function testCredentialsWithMailGatewaySystem() {
		$data = array(
			'hostname' => 'some.proxmox.tld',
			'username' => 'root',
			'password' => 'I was here',
			'system' => 'pmg',
		);

		$fakeAuthToken = new AuthToken( 'csrf', 'ticket', 'username' );
		$proxmox       = $this->getMockProxmox( 'login', $fakeAuthToken );
		$proxmox->setCredentials( $data );

		$credentials = $proxmox->getCredentials();

		$this->assertEquals( $credentials->getSystem(), 'pmg' );
	}

	/**
	 * Proxmox backup Server Test with right credentials.
	 *
	 * @return void
	 */
	public function testCredentialsWithBackupServerSystem() {
		$data = array(
			'hostname' => 'some.proxmox.tld',
			'username' => 'root',
			'password' => 'I was here',
			'system' => 'pbs',
		);

		$fakeAuthToken = new AuthToken( 'csrf', 'ticket', 'username' );
		$proxmox       = $this->getMockProxmox( 'login', $fakeAuthToken );
		$proxmox->setCredentials( $data );

		$credentials = $proxmox->getCredentials();

		$this->assertEquals( $credentials->getSystem(), 'pbs' );
	}

	/**
	 * Test Unresolved Hostname.
	 */
	public function testUnresolvedHostnameThrowsException() {
		$this->expectException( \Exception::class );
		$credentials = array(
			'hostname' => 'proxmox.example.tld',
			'username' => 'user',
			'password' => 'pass',
		);

		new Proxmox( $credentials );
	}

	/**
	 * Test Failed Login.
	 */
	public function testLoginErrorThrowsException() {
		$this->expectException( ClientException::class );
		$credentials = array(
			'hostname' => 'proxmox.server.tld',
			'username' => 'are not',
			'password' => 'valid folks!',
		);

		// Simulate failed login.
		$httpClient = $this->getMockHttpClient( false );

		new Proxmox( $credentials, null, $httpClient );
	}

	/**
	 * Test all response types.
	 */
	public function testGetAndSetResponseType() {
		$proxmox = $this->getProxmox( null );
		$this->assertEquals( $proxmox->getResponseType(), 'array' );

		$proxmox->setResponseType( 'json' );
		$this->assertEquals( $proxmox->getResponseType(), 'json' );

		$proxmox->setResponseType( 'html' );
		$this->assertEquals( $proxmox->getResponseType(), 'html' );

		$proxmox->setResponseType( 'extjs' );
		$this->assertEquals( $proxmox->getResponseType(), 'extjs' );

		$proxmox->setResponseType( 'text' );
		$this->assertEquals( $proxmox->getResponseType(), 'text' );

		$proxmox->setResponseType( 'png' );
		$this->assertEquals( $proxmox->getResponseType(), 'png' );

		$proxmox->setResponseType( 'pngb64' );
		$this->assertEquals( $proxmox->getResponseType(), 'pngb64' );

		$proxmox->setResponseType( 'object' );
		$this->assertEquals( $proxmox->getResponseType(), 'object' );

		$proxmox->setResponseType( 'other' );
		$this->assertEquals( $proxmox->getResponseType(), 'array' );
	}

	/**
	 * Test get wrong resources.
	 */
	public function testGetResourceWithBadParamsThrowsException() {
		$this->expectException( \InvalidArgumentException::class );
		$proxmox = $this->getProxmox( null );
		$proxmox->get( '/someResource', 'wrong params here' );
	}

	/**
	 * Test create wrong resources.
	 */
	public function testCreateResourceWithBadParamsThrowsException() {
		$this->expectException( \InvalidArgumentException::class );
		$proxmox = $this->getProxmox( null );
		$proxmox->create( '/someResource', 'wrong params here' );
	}

	/**
	 * Test set wrong resources.
	 */
	public function testSetResourceWithBadParamsThrowsException() {
		$this->expectException( \InvalidArgumentException::class );
		$proxmox = $this->getProxmox( null );
		$proxmox->set( '/someResource', 'wrong params here' );
	}

	/**
	 * Test delete wrong resources.
	 */
	public function testDeleteResourceWithBadParamsThrowsException() {
		$this->expectException( \InvalidArgumentException::class );
		$proxmox = $this->getProxmox( null );
		$proxmox->delete( '/someResource', 'wrong params here' );
	}

	/**
	 * Test Get Resource succeffull.
	 */
	public function testGetResource() {
		$fakeResponse = <<<'EOD'
{"data":[{"disk":940244992,"cpu":0.000998615325210486,"maxdisk":5284429824,"maxmem":1038385152,"node":"office","maxcpu":1,"level":"","uptime":3296027,"id":"node/office","type":"node","mem":311635968}]}
EOD;
		$proxmox      = $this->getProxmox( $fakeResponse );

		$this->assertEquals( $proxmox->get( '/nodes' ), json_decode( $fakeResponse, true ) );
	}
}

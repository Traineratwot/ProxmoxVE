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

use ProxmoxVE\Exception\MalformedCredentialsException;

/**
 * Credentials class. Handles all related data used to connect to a Proxmox
 * server.
 *
 * @author César Muñoz <zzantares@gmail.com>
 */
class Credentials {

	/**
	 * Hostname.
	 *
	 * @var string The Proxmox hostname (or IP address) to connect to.
	 */
	private $hostname;

	/**
	 * Username.
	 *
	 * @var string The credentials username used to authenticate with Proxmox.
	 */
	private $username;

	/**
	 * Password.
	 *
	 * @var string The credentials password used to authenticate with Proxmox.
	 */
	private $password;

	/**
	 * Realm.
	 *
	 * @var string The authentication realm (defaults to "pam" if not provided).
	 */
	private $realm;

	/**
	 * Port.
	 *
	 * @var string The Proxmox port (defaults to "8006" if not provided).
	 */
	private $port;

	/**
	 * System.
	 *
	 * @var string The Proxmox system being used (defaults to "pve" if not provided).
	 */
	private $system;

	/**
	 * Token Name.
	 *
	 * @var string The token name like root@pve!api Format: <user>@<realm>!<name>
	 */
	private $tokenName;

	/**
	 * API Token Secret.
	 *
	 * @var string The secret key.
	 */
	private $tokenKey;

	/**
	 * Is API?.
	 *
	 * @var boolen The secret key.
	 */
	private $isApi = false;

	/**
	 * Create new Obejct of Proxmox.
	 *
	 * @param array|object $credentials This needs to have 'hostname', 'username' and 'password' or 'hostname', 'tokenName' and 'tokenKey' defined.
	 * @throws MalformedCredentialsException Exception for bad credentials.
	 */
	public function __construct( $credentials) {

		// Get credentials object in valid array form.
		$credentials = $this->parseCustomCredentials( $credentials );

		if (!$credentials) {
			throw new MalformedCredentialsException( 'PVE API needs a credentials object or an array.' );
		}

		$this->hostname  = $credentials['hostname'] ?? '';
		$this->username  = $credentials['username'] ?? '';
		$this->password  = $credentials['password'] ?? '';
		$this->realm     = $credentials['realm'] ?? '';
		$this->port      = $credentials['port'] ?? '';
		$this->system    = $credentials['system'] ?? '';
		$this->tokenName = $credentials['tokenName'] ?? '';
		$this->tokenKey  = $credentials['tokenKey'] ?? '';
		if (!empty( $credentials['tokenName'] ) && !empty( $credentials['tokenKey'] )) {
			$this->setIsApi( true );
		}

	}

	/**
	 * Gives back the string representation of this credentials object.
	 *
	 * @return string Credentials data in a single string.
	 */
	public function __toString() {
		return sprintf(
			'[Host: %s:%s], [Username: %s@%s].',
			$this->hostname,
			$this->port,
			$this->username,
			$this->realm
		);
	}

	/**
	 * Returns the base URL used to interact with the ProxmoxVE API.
	 *
	 * @return string The proxmox API URL.
	 */
	public function getApiUrl() {
		return 'https://' . $this->hostname . ':' . $this->port . '/api2';
	}

	/**
	 * Gets the hostname configured in this credentials object.
	 *
	 * @return string The hostname in the credentials.
	 */
	public function getHostname() {
		return $this->hostname;
	}

	/**
	 * Gets the username given to this credentials object.
	 *
	 * @return string The username in the credentials.
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * Gets the password set in this credentials object.
	 *
	 * @return string The password in the credentials.
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * Gets the realm used in this credentials object.
	 *
	 * @return string The realm in this credentials.
	 */
	public function getRealm() {
		return $this->realm;
	}

	/**
	 * Gets the port configured in this credentials object.
	 *
	 * @return string The port in the credentials.
	 */
	public function getPort() {
		return $this->port;
	}

	/**
	 * Gets the system configured in this credentials object.
	 *
	 * @return string The port in the credentials.
	 */
	public function getSystem() {
		return $this->system;
	}


	/**
	 * Get the secret key.
	 *
	 * @return  boolean
	 */
	public function isApi() {
		return $this->isApi;
	}

	/**
	 * Set the secret key.
	 *
	 * @param boolean $isApi The secret key.
	 *
	 * @return self
	 */
	public function setIsApi( bool $isApi) {
		$this->isApi = $isApi;
		return $this;
	}

	/**
	 * Get the secret key. In Format like PVEAPIToken=USER@REALM!TOKENID=UUID.
	 *
	 * @see https://pve.proxmox.com/wiki/Proxmox_VE_API
	 * @return string
	 */
	public function getTokenKey() {
		switch ($this->getSystem()) {
			case 'pbs':
				$suffix = 'PBSAPIToken';
				break;
			default:
				$suffix = 'PVEAPIToken';
				break;
		}
		return $suffix . '=' . $this->tokenName . '=' . $this->tokenKey;
	}


	/**
	 * Set the secret key.
	 *
	 * @param  string $tokenKey  The secret key.
	 *
	 * @return  self
	 */
	public function setTokenKey( string $tokenKey) {
		$this->tokenKey = $tokenKey;

		return $this;
	}

	/**
	 * Get the token name like root@pve!api Format: <user>@<realm>!<name>
	 *
	 * @return  string
	 */
	public function getTokenName() {
		return $this->tokenName;
	}

	/**
	 * Given the custom credentials object it will try to find the required
	 * values to use it as the proxmox credentials, this can be an object with
	 * accessible properties, getter methods or an object that uses '__get' to
	 * access properties dynamically.
	 *
	 * @param mixed $credentials Array or Object of Credentials.
	 *
	 * @return array|null If credentials are found they are returned as an
	 *                    associative array, returns null if object can not be
	 *                    used as a credentials provider.
	 */
	public function parseCustomCredentials( $credentials) {
		if (is_array( $credentials ) || is_object( $credentials )) {
			// Required Keys.
			$requiredLoginKeys = array('hostname', 'username', 'password');
			$requiredApiKeys   = array('hostname', 'tokenName', 'tokenKey');

			// Check if keys exist.
			$credentialsKeys = is_array( $credentials ) ? array_keys( $credentials ) : array();

			// Check if all required keys are present.
			if (count( array_intersect( $requiredLoginKeys, $credentialsKeys ) ) === count( $requiredLoginKeys ) ||
			count( array_intersect( $requiredApiKeys, $credentialsKeys ) ) === count( $requiredApiKeys )) {

				// Map properties or method calls to corresponding keys.
				$properties = array(
					'tokenName' => null,
					'tokenKey' => null,
					'hostname' => null,
					'username' => null,
					'password' => null,
					'realm' => 'pam',
					'port' => '8006',
					'system' => 'pve',
				);

				foreach ($properties as $key => &$value) {
					if (is_array( $credentials ) && array_key_exists( $key, $credentials )) {
						$value = $credentials[$key];
					} elseif (is_object( $credentials )) {
						$methodName = 'get' . ucfirst( str_replace( '-', '', $key ) );
						if (method_exists( $credentials, $methodName )) {
							$value = $credentials->$methodName();
						} elseif (property_exists( $credentials, $key )) {
							$value = $credentials->$key;
						}
					}
				}
				unset( $value ); // unset reference.

				return $properties;
			}
		}

		return null;
	}

}

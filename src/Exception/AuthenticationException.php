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

namespace ProxmoxVE\Exception;

/**
 * AuthenticationException class. Is the exception thrown when credentials
 * passed are not correct, thus the ProxmoxVE API client can not be used.
 *
 * @author César Muñoz <zzantares@gmail.com>
 */
class AuthenticationException extends \RuntimeException {

}

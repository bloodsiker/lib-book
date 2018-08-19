<?php

namespace AdminBundle\Exception;

/**
 * Class FTPClientException
 * @package AdminBundle\Exception
 */
class FTPClientException extends \Exception
{
    const ERROR_NOT_CONNECTED = 'FTP_NOT_CONNECTED';
    const ERROR_CONNECT = 'FTP_CONNECT';
    const ERROR_LOGIN = 'FTP_LOGIN';
}

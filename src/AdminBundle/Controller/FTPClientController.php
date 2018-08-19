<?php

namespace AdminBundle\Controller;

use AdminBundle\Exception\FTPClientException;

/**
 * Class FTPClientController
 * @package AdminBundle\Controller
 */
class FTPClientController
{
    /**
     * Store FTP stream
     *
     * @var resource
     */
    private $connection;

    /**
     * Constructor
     */
    public function __construct()
    {

    }

    /**
     * Perform connection and authorization on FTP
     *
     * @param string $host     The FTP server address
     * @param string $port     Port to connect to
     * @param string $user     The username
     * @param string $password The password
     * @return bool
     * @throws FTPClientException
     */
    public function connect($host, $port, $user, $password)
    {
        $this->connection = ftp_connect($host, $port);
        if (!$this->connection) {
            throw new FTPClientException(FTPClientException::ERROR_CONNECT);
        }

        $login = ftp_login($this->connection, $user, $password);
        if (!$login) {
            throw new FTPClientException(FTPClientException::ERROR_LOGIN);
        }

        ftp_pasv($this->connection, true);

        return $this->connected();
    }

    /**
     * Disconnect from FTP
     *
     * @return bool
     */
    public function disconnect()
    {
        if ($this->connected()) {
            ftp_close($this->connection);
            $this->connection = null;

            return true;
        }

        return false;
    }

    /**
     * Check if connection established
     *
     * @return bool
     */
    public function connected()
    {
        return is_resource($this->connection);
    }

    /**
     * Creates a directory
     *
     * @param string $dir
     * @throws FTPClientException
     */
    public function makeDir($dir)
    {
        if (!$this->connected()) {
            throw new FTPClientException(FTPClientException::ERROR_NOT_CONNECTED);
        }

        if ($dir and $dir != '.') {
            $dirs = explode('/', $dir);
            if ($dirs) {
                foreach ($dirs as $d) {
                    if (!empty($d)) {
                        $list = ftp_nlist($this->connection, '.');
                        if (is_array($list) and in_array($d, $list)) {
                            ftp_chdir($this->connection, $d);
                        } else {
                            ftp_mkdir($this->connection, $d);
                            ftp_chdir($this->connection, $d);
                        }
                    }
                }
            }
        }
    }

    /**
     * Downloads a file from FTP server
     *
     * @param string $source
     * @param string $destination
     * @return bool
     * @throws FTPClientException
     */
    public function download($source, $destination)
    {
        if (!$this->connected()) {
            throw new FTPClientException(FTPClientException::ERROR_NOT_CONNECTED);
        }

        return ftp_get($this->connection, $destination, $source, FTP_BINARY);
    }

    /**
     * Uploads a file to FTP server
     *
     * @param string $source
     * @param string $destination
     * @throws FTPClientException
     */
    public function upload($source, $destination)
    {
        if (!$this->connected()) {
            throw new FTPClientException(FTPClientException::ERROR_NOT_CONNECTED);
        }
    }
}

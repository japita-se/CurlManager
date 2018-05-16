<?php
/**
 * Created by PhpStorm.
 * User: antox
 * Date: 21/04/18
 * Time: 15:44
 */

class CurlManager
{
    public $response;
    public $code;
    public $errors;
    private $handle;
    private $verbose = false;
    private $session;
    private $curlopts;

    public function __construct()
    {
        $this->handle = curl_init();
        //$cookiejar = tempnam(sys_get_temp_dir(), 'session');

        $headers = array(
            //'Accept: application/json',
            'Content-Type: application/json',
        );

        $this->curlopts = array(
            CURLOPT_HTTPHEADER=>$headers,
            //CURLINFO_HEADER_OUT=>true,
            CURLOPT_RETURNTRANSFER=>true,
            CURLOPT_SSL_VERIFYHOST=>false,
            CURLOPT_SSL_VERIFYPEER=>false,
        );
    }

    /**
     * @return (object) cURL handle
     */
    public function getHandle()
    {
        return $this->handle;
    }

    public function setVerbose($verbose){
        $this->verbose = $verbose;
    }

    /**
     * @param (array) $headers Array of header values
     * @return (array) Headers
     */
    public function setHeaders($headers)
    {
        $this->curlopts[CURLOPT_HTTPHEADER] = $headers;
curl_setopt_array($this->handle, $this->curlopts);

        $this->getHeaders();
    }

    /**
     * @return (array) Headers
     */
    public function getHeaders()
    {
        return $this->curlopts[CURLOPT_HTTPHEADER];
    }

    /**
     * @param string $method GET/PUT/POST/DELETE
     * @param string $url Request URL
     * @param array $data JSON data (optional)
     * @return bool TRUE
     */
    public function setUp($method, $url, $data='')
    {
        curl_setopt_array($this->handle, $this->curlopts);
        curl_setopt($this->handle, CURLOPT_URL, $url);

        if ($this->session)
            curl_setopt($this->handle, CURLOPT_COOKIE, $this->session);

        if($this->verbose){
            curl_setopt($this->handle, CURLOPT_VERBOSE, true);
            $this->errors = fopen('php://temp', 'w+');
            curl_setopt($this->handle, CURLOPT_STDERR, $this->errors);
        }
        switch(strtoupper($method)) {
            case 'GET':
                break;

            case 'POST':
                curl_setopt($this->handle, CURLOPT_CONNECTTIMEOUT, 0);
                curl_setopt($this->handle, CURLOPT_TIMEOUT, 30); //timeout in seconds
                curl_setopt($this->handle, CURLOPT_POST, true);
                curl_setopt($this->handle, CURLOPT_POSTFIELDS, $data);
                curl_setopt($this->handle, CURLOPT_SSLVERSION, 1);
                break;

            case 'PUT':
                curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($this->handle, CURLOPT_POSTFIELDS, $data);
                break;

            case 'DELETE':
                curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }

        return true;
    }
  /**
     * @return bool Success of HTTP request
     */
    public function send()
    {
        $this->response = curl_exec($this->handle);
        $this->code = curl_getinfo($this->handle, CURLINFO_HTTP_CODE);

        if (!$this->session)
            $this->session = session_id() .'='. session_id() .'; path=' . session_save_path();

        if( $this->verbose ){
            rewind($this->errors);
            $this->errors = stream_get_contents($this->errors);
        }


        session_write_close();

        return !!$this->response;
    }
}


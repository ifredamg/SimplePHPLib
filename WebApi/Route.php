<?php
    class Route
    {
        private $config = array();

        public function __construct()
        {
            
        }

        public function getMapping()
        {
            return $this->config;
        }

        public function getEndpointInfo($controller, $method)
        {
            $endpoint = $this->getEndpoint($controller, $method);
            foreach($this->config as $key => $value)
            {
                if(strtoupper($key) == strtoupper($endpoint))
                {
                    return $value;
                }
            }
        }

        public function getEndpoint($controller, $method)
        {
            return $controller."/".$method;
        }

        public function post($endpoint, $params = null)
        {
            $this->setEndpoint($endpoint, "POST", $params);
        }

        public function get($endpoint, $params = null)
        {
            $this->setEndpoint($endpoint, "GET", $params);
        }

        public function put($endpoint, $params = null)
        {
            $this->setEndpoint($endpoint, "PUT", $params);
        }

        public function patch($endpoint, $params = null)
        {
            $this->setEndpoint($endpoint, "PATCH", $params);
        }

        public function delete($endpoint, $params = null)
        {
            $this->setEndpoint($endpoint, "DELETE", $params);
        }

        public function copy($endpoint, $params = null)
        {
            $this->setEndpoint($endpoint, "COPY", $params);
        }

        public function head($endpoint, $params = null)
        {
            $this->setEndpoint($endpoint, "HEAD", $params);
        }

        public function options($endpoint, $params = null)
        {
            $this->setEndpoint($endpoint, "OPTIONS", $params);
        }

        public function link($endpoint, $params = null)
        {
            $this->setEndpoint($endpoint, "LINK", $params);
        }

        public function unlink($endpoint, $params = null)
        {
            $this->setEndpoint($endpoint, "UNLINK", $params);
        }

        public function purge($endpoint, $params = null)
        {
            $this->setEndpoint($endpoint, "PURGE", $params);
        }

        public function lock($endpoint, $params = null)
        {
            $this->setEndpoint($endpoint, "LOCK", $params);
        }

        public function unlock($endpoint, $params = null)
        {
            $this->setEndpoint($endpoint, "UNLOCK", $params);
        }

        public function propfind($endpoint, $params = null)
        {
            $this->setEndpoint($endpoint, "PROPFIND", $params);
        }

        public function view($endpoint, $params = null)
        {
            $this->setEndpoint($endpoint, "VIEW", $params);
        }

        private function setEndpoint($endpoint, $method, $params = null)
        {
            if($params == null)
                $this->config[$endpoint] = ["METHOD" => $method];
            else
                $this->config[$endpoint] = ["METHOD" => $method, "PARMS" => $params];
        }
    }
?>
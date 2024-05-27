<?php
    class WebApiClient
    {
        //API
        private $apiBaseURL;

        //Autenticação
        protected $authType = "WithoutAuth"; //BasicAuth, JWT, OAuth, ...
        protected $basicAuthUser;
        protected $basicAuthPassword;

        private $bodyData;

        public $sucesso = true;
        public $hasError = false;
        public $mensagemDetalhe;
        public $resultado = null;

        public $response_code;

        public function __construct($apiBaseURL)
        {
            $this->apiBaseURL = $apiBaseURL;
        }

        public function useBasicAuth($user, $psw)
        {
            $this->authType = "BasicAuth";
            $this->basicAuthUser = $user;
            $this->basicAuthPassword = $psw;
        }

        public function setBody($data)
        {
            if($data == null) return;

            if($this->bodyData == null)
            {
                $this->bodyData = $data;
            }
            else
            {
                $this->bodyData = array_merge($this->bodyData, $data);
            }
        }

        public function getResult() { return $this->resultado; }

        public function GET($url, $bodyFields = null, $timeout = 30)
        {
            $this->setBody($bodyFields);
            return $this->REQUEST('GET', $url, $timeout);
        }

        public function POST($url, $fields, $timeout = 30)
        {
            $this->setBody($fields);
            return $this->REQUEST('POST', $url, $timeout);
        }

        private function REQUEST($method, $url, $timeout)
        {
            $curl = curl_init($this->apiBaseURL . $url);

            if($this->authType == "BasicAuth")
                curl_setopt($curl, CURLOPT_USERPWD, $this->basicAuthUser . ":" . $this->basicAuthPassword);
            curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            if($method == "POST")
            {
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($this->bodyData));
            }
            else if($method == "GET")
            {
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET"); 
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($this->bodyData));
            }

            //var_dump(json_encode($this->bodyData));

            $curl_response = curl_exec($curl);

            if ($curl_response === false) 
            {
                $info = curl_getinfo($curl);
                curl_close($curl);
                die('error occured during curl exec. Additioanl info: ' . var_export($info));
            }

            //var_dump($curl_response);

            curl_close($curl);
            $decoded = json_decode($curl_response, false);
            
            if($decoded != null)
            {
                $this->sucesso = $decoded->sucesso;
                $this->hasError = $decoded->hasError;
                $this->mensagemDetalhe = $decoded->mensagemDetalhe;
                $this->resultado = $decoded->resultado;
                $this->response_code = $decoded->response_code;
            }

            return $this;
        }
    }
?>
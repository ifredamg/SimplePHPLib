<?php
    class WebApi 
    {
        private $debug;
        private $encoding;
        public $bodyPedido;
        public $resposta; //WebApiResponse
        public $auth; //WebApiAuth

        public function __construct($debug = false, $encoding = "JSON")
        {
            $this->setHeaders();
            $this->debug = $debug;
            $this->encoding = $encoding;
            $this->bodyPedido = json_decode(file_get_contents('php://input'), false);
            if($this->bodyPedido == null)
                $this->bodyPedido = (object)$_POST;
            $this->resposta = new WebApiResponse($this->encoding);
            $this->auth = new WebApiAuth();
        }

        #region Set's

        private function setHeaders()
        {
            switch($this->encoding)
            {
                default:
                    {
                        header("Content-Type: application/json; charset=UTF-8");
                    }
                    break;
            }

            //header("Access-Control-Max-Age: 3600");
            //header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        }

        #endregion

        #region Get's

        public function getBody()
        {
            return $this->bodyPedido;
        }

        #endregion

        public function finalizar()
        {
            if($this->resposta->response_code != null)
                http_response_code($this->resposta->getResponseCode());

            echo $this->resposta != null ? $this->resposta->getResponseFormated() : null;
        }
    }

    class WebApiAuth
    {
        private $authType = "WithoutAuth"; //BasicAuth, JWT, OAuth, ...

        private $basicAuthUser;
        private $basicAuthPassword;

        private $invalidAccess;
        private $invalidMessage;

        public function __construct()
        {
            $this->invalidAccess = false;
        }

        public function useBasicAuth($user, $psw)
        {
            $this->authType = "BasicAuth";
            $this->basicAuthUser = $user;
            $this->basicAuthPassword = $psw;
        }

        public function useJWT()
        {
            $this->authType = "JWT";
        }

        public function useOAuth()
        {
            $this->authType = "OAuth";
        }

        public function validateAccess()
        {
            switch($this->authType)
            {
                case "BasicAuth":
                    {
                        if (!isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) 
                        {
                            $this->invalidAccess = true;
                            $this->invalidMessage = "Access denied. You did not enter user and password.";
                        }
                        else
                        {
                            if (password_verify($_SERVER['PHP_AUTH_PW'], $this->basicAuthPassword))
                            {
                                $this->invalidAccess = false;
                            } 
                            else 
                            {
                                $this->invalidAccess = true;
                                $this->invalidMessage = "Access denied! You do not know the password.";
                            }
                        }
                    }
                    break;
            }

            if($this->invalidAccess)
            {
                header('WWW-Authenticate: Basic realm="My Website"');
                header('HTTP/1.0 401 Unauthorized');
            }
        }

        #region Get's
        public function getInvalidMessage() { return $this->invalidMessage; }
        public function isInvalidAccess() { return $this->invalidAccess; }
        #endregion
    }

    class WebApiResponse 
    {
        private $encoding;

        public $sucesso = false;   //bool
        public $hasError = false; //bool
        public $mensagemDetalhe;  //WebApiResponseMensagemDetalhe
        public $resultado = null; //object

        public $response_code;

        public function __construct($encoding)
        {
            $this->encoding = $encoding;
            $this->response_code = 200;
        }

        #region Get's

        public function getResponseFormated()
        {
            $response = null;

            switch($this->encoding)
            {
                default:
                    {
                        $response = json_encode($this, JSON_UNESCAPED_UNICODE);
                    }
                    break;
            }

            return $response;
        }

        public function getResponseCode()
        {
            return $this->response_code;
        }

        #endregion

        #region Set's

        public function setResponseCode($code)
        {
            $this->response_code = $code;
        }

        public function setResultado($resultado)
        {
            $this->resultado = $resultado;
        }

        public function setMensagemSucesso($titulo, $descricao, $stackTrace = null)
        {
            $this->sucesso = true;
            $this->mensagemDetalhe = new WebApiResponseMensagemDetalhe();
            $this->mensagemDetalhe->setSucesso($titulo, $descricao, $stackTrace);
        }

        public function setMensagemAviso($titulo, $descricao, $stackTrace = null)
        {
            $this->sucesso = true;
            $this->mensagemDetalhe = new WebApiResponseMensagemDetalhe();
            $this->mensagemDetalhe->setAviso($titulo, $descricao, $stackTrace);
        }

        public function setMensagemInfo($titulo, $descricao, $stackTrace = null)
        {
            $this->sucesso = true;
            $this->mensagemDetalhe = new WebApiResponseMensagemDetalhe();
            $this->mensagemDetalhe->setInfo($titulo, $descricao, $stackTrace);
        }

        public function setMensagemErro($titulo, $descricao, $stackTrace = null)
        {
            $this->sucesso = false;
            $this->hasError = true;
            $this->mensagemDetalhe = new WebApiResponseMensagemDetalhe();
            $this->mensagemDetalhe->setError($titulo, $descricao, $stackTrace);
        }

        public function setMensagemErroAplicacao($titulo, $descricao, $codigo = 0, $stackTrace = null)
        {
            $this->sucesso = false;
            $this->hasError = true;
            $this->mensagemDetalhe = new WebApiResponseMensagemDetalhe();
            $this->mensagemDetalhe->setErrorAplicacao($titulo, $descricao, $codigo, $stackTrace);
            $this->response_code = 500;
        }

        #endregion
    }

    class WebApiResponseMensagemDetalhe
    {
        public $titulo;
        public $descricao;
        public $stackTrace; //Caso de um erro
        public $tipo;  //$message.type.sucesso, $message.type.info, $message.type.aviso, $message.type.erro
        public $codigo;

        public function __construct($titulo = null, $descricao = null, $stackTrace = null)
        {
            $this->titulo = $titulo;
            $this->descricao = $descricao;
            $this->stackTrace = $stackTrace;
        }

        #region Set's

        public function setSucesso($titulo, $descricao, $stackTrace = null)
        {
            $this->titulo = $titulo;
            $this->descricao = $descricao;
            $this->stackTrace = $stackTrace;
            $this->tipo = '$message.type.sucesso';
        }

        public function setError($titulo, $descricao, $stackTrace = null)
        {
            $this->titulo = $titulo;
            $this->descricao = $descricao;
            $this->stackTrace = $stackTrace;
            $this->tipo = '$message.type.erro';
        }

        public function setErrorAplicacao($titulo, $descricao, $codigo = 0, $stackTrace = null)
        {
            $this->titulo = $titulo;
            $this->descricao = $descricao;
            $this->codigo = $codigo;
            $this->stackTrace = $stackTrace;
            $this->tipo = '$message.type.erroAplicacao';
        }

        public function setAviso($titulo, $descricao, $stackTrace = null)
        {
            $this->titulo = $titulo;
            $this->descricao = $descricao;
            $this->stackTrace = $stackTrace;
            $this->tipo = '$message.type.aviso';
        }

        public function setInfo($titulo, $descricao, $stackTrace = null)
        {
            $this->titulo = $titulo;
            $this->descricao = $descricao;
            $this->stackTrace = $stackTrace;
            $this->tipo = '$message.type.info';
        }
        
        #endregion
    }
?>
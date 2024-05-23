<?php
    /*
     * Funções gerais utilitárias
     * 
     * @Autor: Frederico Santos
     * @Versão: 2.8.26
     * 
     */

    // ==================================================
    // Função para iníciar sessão de forma segura
    // ==================================================
    function sec_session_start() {
        $session_name = 'sec_session_id';
        $secure = false;
        $httponly = true;
        // ==================================================
        ini_set('session.use_only_cookies', 1); 
        $cookieParams = session_get_cookie_params();
        session_cache_expire(30);
        session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly); 
        session_name($session_name);
        session_start();
        session_regenerate_id(true);
    }

    // ==================================================
    // Funções de datas
    // ==================================================
    function encortaAno($ano)
    {
        return substr($ano, 2, 4);
    }

    function meses($mes)
    {
        $mes = abs($mes);

        if($mes == 1) {
            $mes = "Janeiro";
        } else if ($mes == 2) {
            $mes = "Fevereiro";
        } else if ($mes == 3) {
            $mes = "Março";
        } else if($mes == 4) {
            $mes = "Abril";
        } else if($mes == 5) {
            $mes = "Maio";
        } else if($mes == 6) {
            $mes = "Junho";
        } else if($mes == 7) {
            $mes = "Julho";
        } else if($mes == 8) {
            $mes = "Agosto";
        } else if($mes == 9) {
            $mes = "Setembro";
        } else if($mes == 10) {
            $mes = "Outubro";
        } else if($mes == 11) {
            $mes = "Novembro";
        } else if($mes == 12) {
            $mes = "Dezembro";
        }
        return $mes;
    }
    
    // Função para converter a data para o formato usado em Portugal
    function ptdate($date) {
        return date("d/m/Y",strtotime($date));
    }

    // Função para converter a data e hora para o formato usado em Portugal
    function ptdatetime($date) {
        return date("d/m/Y H:i",strtotime($date));
    }

    // Função para converter a hora para o formato usado em Portugal
    function pttime($hora) {
        return date("H:i",strtotime($hora));
    }
    // ==================================================

    // ==================================================
    // Funções de currency
    // ==================================================
    function preco($preco, $moeda = "&euro;") {
        return number_format($preco, 2, ',', '.').$moeda;
    }

    // ==================================================
    // Função que retira todos os acentos das letras
    // ==================================================
    function sanitizeString($string)
    {
        $what = array('ç','ä','ã','à','á','â','ê','ë','è','é','ï','ì','í','ö','õ','ò','ó','ô','ü','ù','ú','û','À','Á','É','Í','Ó','Ú','ñ','Ñ','ç','Ç',' ','-','(',')',',',';',':','|','!','"','#','$','%','&','/','=','?','~','^','>','<','ª','º' );
    
        $by = array('c','a','a','a','a','a','e','e','e','e','i','i','i','o','o','o','o','o','u','u','u','u','A','A','E','I','O','U','n','n','c','C','-','-','-','-','-','-','-','-','','-','','-','-','-','-','','','','','','','','' );
    
        return str_replace($what, $by, $string);
    }

    // ==================================================
    // Função para encortar um texto
    // ==================================================
    function encortarTexto($texto, $limite, $terminar)
    {
        return substr($texto, 0, $limite).$terminar;
    }

    // ==================================================
    // Função para criar uma mascara de telefone do tipo XXX XXX XXX
    // ==================================================
    function mascaraTelefone($telefone, $digitos)
    {
        $telefone = chunk_split($telefone,$digitos," ");
        return $telefone;
    }
    
    // ==================================================
    // Cria um Serial Code do Tipo 0000-0000-0000-0000
    // ==================================================
    function criaSerial() {
        $cr = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $max = strlen($cr)-1;
        $parteSerial = null;
        for($i=0; $i < 16; $i++) {
            $parteSerial .= $cr[mt_rand(0, $max)];
        }
        $parteSerial = str_split($parteSerial, 4);
        $parteSerial = "$parteSerial[0]-$parteSerial[1]-$parteSerial[2]-$parteSerial[3]";
        
        return $parteSerial;
    }

    // ==================================================
    // Tempo de carregamento da página
    // ==================================================
    $theTime = array_sum(str_split(microtime()));
    echo "<!-- Tempo: ".$theTime." ms -->";

    // ==================================================
    // Função que adquire o IP do cliente
    // ==================================================
    function get_client_ip() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else $ipaddress = 'UNKNOWN';

        if($ipaddress == "::1")
            $ipaddress = "LOCALHOST";
        
        return $ipaddress;
    }

    // ==================================================
    // Função que verifica se o dispositivo é mobile
    // ==================================================
    function verificaMobile() {
        $mobile = false;
        $user_agents = array("iPhone","iPad","iPod","Android","webOS","BlackBerry","Symbian","IsGeneric");
  
        foreach($user_agents as $user_agent) {
            if (strpos($_SERVER['HTTP_USER_AGENT'], $user_agent) !== false) {
                $mobile = true;
            } else {
                $mobile = false;
            }
        }
    }
?>
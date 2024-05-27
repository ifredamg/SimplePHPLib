<?php 
    namespace Lib\MySQL;
    require_once __DIR__.'/../Ex/Ex.php';

    /**
     * Classe de entidade SmartBD
     */
    class SmartBD
    {
        public $fields;

        /**
         * Construtor SmartBD
         */
        public function __construct()
        {
            $fields = array();
        }

        /**
         * Adicionar uma nova definição de campo na SmartBD
         * @param string Nome da Propriedade
         * @param string Nome do Campo
         * @param string Alias
         * @param bool Permite valores nulos
         * @param bool Permite valores vazios
         * @param int Cumprimento minímo
         * @param int Cumprimento máximo
         * @param bool Ignorar no insert
         * @param bool Ignorar no updade
         * @param bool Ignorar no select
         */
        public function addRow($property, $fieldname, $caption, 
                               $allowNull = false, $allowEmpty = false, 
                               $minLength = null, $maxLength = null, 
                               $minValue = null, $maxValue = null, 
                               $ignoreInsert = false, $ignoreUpdate = false, $ignoreSelect = false)
        {
            $field = new SmartBDRow();
            $field->property = $property;
            $field->fieldname = $fieldname != null ? $fieldname : $property;
            $field->caption = $caption != null ? $caption : $property;
            $field->allowNull = (bool)$allowNull;
            $field->allowEmpty = (bool)$allowEmpty;
            $field->minLength = $minLength;
            $field->maxLength = $maxLength;
            $field->minValue = $minValue;
            $field->maxValue = $maxValue;
            $field->ignoreInsert = (bool)$ignoreInsert;
            $field->ignoreUpdate = (bool)$ignoreUpdate;
            $field->ignoreSelect = (bool)$ignoreSelect;
            //TODO: Validar valores numa lista

            $this->fields[$fieldname] = (array)$field;
        }

        /**
         * Validade se os dados da entidade correspondem com a configuração da SmartBD
         * @param object Entidade
         */
        public function validate($entityValues)
        {
            $tempArr = (array)$entityValues;
            if($tempArr != null && count($tempArr) > 0)
            {
                foreach($this->fields as $key => $validateField)
                {
                    if(array_key_exists($key, $tempArr))
                    {
                        $valor = $tempArr[$key];
                        
                        if(!$validateField["allowNull"] && is_null($valor))
                        {
                            throw new ExCancel(sprintf("O campo '%s' é de preenchimento obrigatório!", $validateField["caption"]));
                        }
                        
                        if(!$validateField["allowEmpty"] && is_string($valor) && empty($valor))
                        {
                            throw new ExCancel(sprintf("O campo '%s' não pode ser vazio!", $validateField["caption"]));
                        }

                        if($validateField["minValue"] != null)
                        {
                            if($valor < $validateField["minValue"])
                            {
                                throw new ExCancel(sprintf("O campo '%s' não permite valores inferiores a %s!", $validateField["caption"], $validateField["minValue"]));
                            }
                        }

                        if($validateField["maxValue"] != null)
                        {
                            if($valor > $validateField["maxValue"])
                            {
                                throw new ExCancel(sprintf("O campo '%s' não permite valores superiores a %s!", $validateField["caption"], $validateField["maxValue"]));
                            }
                        }

                        if($validateField["minLength"] != null)
                        {
                            if(strlen($valor) < $validateField["minLength"])
                            {
                                throw new ExCancel(sprintf("O campo '%s' precisa de ter pelo menos %s %s!", $validateField["caption"], $validateField["minLength"], ($validateField["minLength"] == 1 ? "caracter" : "caracteres")));
                            }
                        }

                        if($validateField["maxLength"] != null)
                        {
                            if(strlen($valor) > $validateField["maxLength"])
                            {
                                throw new ExCancel(sprintf("O campo '%s' excede o limite de %s caracteres!", $validateField["caption"], $validateField["maxLength"]));
                            }
                        }
                    }
                    else
                    {
                        //Se não existir o campo nos valores e na smartBD estiver como obrigatório
                        //throw new ExCancel(sprintf("O campo '%s' excede o limite de %s caracteres!", $validateField["caption"], $validateField["maxLength"]));
                    }
                }
            }
            else
            {
                throw new ExCancel('Não foram enviados dados...');
            }
        }

        /**
         * Gerar uma query de select
         * @param string Query
         * @return string Query preparada
         */
        public function generateSelect($query)
        {
            $strReplaceQuery = "";
            foreach($this->fields as $field)
            {
                if(!$field["ignoreSelect"])
                {
                    if($strReplaceQuery != "")
                        $strReplaceQuery .= ", ";
                    $strReplaceQuery .= $field["fieldname"] . " " . $field["property"];
                }
            }

            return str_replace("{0}", $strReplaceQuery, $query);
        }

        /**
         * Gerar uma query de insert
         * @param string Query
         * @return string Query preparada
         */
        public function generateInsert($query)
        {
            $strReplaceQueryFields = "";
            $strReplaceQueryValuesParam = "";
            foreach($this->fields as $field)
            {
                if(!$field["ignoreInsert"])
                {
                    if($strReplaceQueryFields != "")
                        $strReplaceQueryFields .= ", ";
                    $strReplaceQueryFields .= $field["fieldname"];

                    if($strReplaceQueryValuesParam != "")
                    {
                        $strReplaceQueryValuesParam .= ", ";
                    }

                    //$strReplaceQueryValuesParam .= "':" . $field["fieldname"] . "'";
                    $strReplaceQueryValuesParam .= ":" . $field["fieldname"];
                }
            }

            $query = str_replace("{0}", $strReplaceQueryFields, $query);
            $query = str_replace("{1}", $strReplaceQueryValuesParam, $query);
            return $query;
        }

        /**
         * Gerar uma query de update
         * @param string Query
         * @return string Query preparada
         */
        public function generateUpdate($query)
        {
            $strReplaceQuery = "";
            foreach($this->fields as $field)
            {
                if(!$field["ignoreUpdate"])
                {
                    if($strReplaceQuery != "")
                        $strReplaceQuery .= ", ";
                    $strReplaceQuery .= $field["fieldname"] . " = :" . $field["fieldname"];
                }
            }

            return str_replace("{0}", $strReplaceQuery, $query);
        }

        /**
         * Gerar parâmetros apartir de uma entidade
         * @param object Entidade
         * @return array Parâmetros extra
         * @return SQLParameters Parâmetros
         */
        public function setParametersFromEntity($entity, $extraParam = null)
        {
            $paramResult = array();

            if(is_array($entity))
            {
                foreach($this->fields as $field)
                {
                    if(!$field["ignoreInsert"])
                    {
                        if(array_key_exists($field["property"], $entity))
                        {
                            $paramResult[":" . $field["property"]] = $entity[$field["property"]];
                        }
                        else
                        {
                            $paramResult[":" . $field["property"]] = null;
                        }
                    }
                }
            }
            else if(is_object($entity))
            {
                $paramResult = $this->setParametersFromEntity((array) $entity, $extraParam); 
            }
            else
            {
                return null;
            }

            if($extraParam != null && is_array($extraParam))
            {
                $paramResult = array_merge($paramResult, $extraParam);
            }

            // echo "<pre>";
            // var_dump($paramResult);
            // echo "</pre>";
            return $paramResult;
        }
    }

    /**
     * Classe de entidade SmartBDRow
     */
    class SmartBDRow
    {
        public $property;
        public $fieldname;
        public $caption;
        public $allowNull;
        public $allowEmpty;
        public $minValue;
        public $maxValue;
        public $minLength;
        public $maxLength;
        public $ignoreInsert;
        public $ignoreUpdate;
        public $ignoreSelect;

        /**
         * Construtor SmartBDRow
         */
        public function __construct()
        {
            $this->ignoreInsert = false;
            $this->ignoreUpdate = false;
            $this->ignoreSelect = false;
        }
    }

    //==========================================================================
    //TESTES
    //==========================================================================
    function execTest()
    {
        $smartBD = new SmartBD();
        $smartBD->addRow("Descricao", "Descrição", $minLength = 3, $maxLength = 20);
        $smartBD->addRow("Idade", "Idade", false, false, 1, 18);

        echo "<pre>";
        var_dump($smartBD->fields);
        echo "</pre>";

        $postData = array();
        $postData["Descricao"] = "Descrição de teste";
        $postData["Idade"] = 18;

        $smartBD->validate($postData);

        $sqlSelect = "SELECT {0} FROM Utilizadores WHERE Utilizador = :Utilizador";
        $sqlUpdate = "UPDATE Utilizadores SET {0} WHERE Utilizador = :Utilizador";
        $sqlInsert = "INSERT INTO Utilizadores ({0}, IdTransacao) VALUES ({1}, '12312421')";
        echo "<p><b>" . $smartBD->generateSelect($sqlSelect) . "</b></p>";
        echo "<p><b>" . $smartBD->generateInsert($sqlInsert) . "</b></p>";
        echo "<p><b>" . $smartBD->generateUpdate($sqlUpdate) . "</b></p>";

        $smartBD->setParametersFromEntity($postData, [":IsDebug" => true]);
    }

    //execTest();
?>
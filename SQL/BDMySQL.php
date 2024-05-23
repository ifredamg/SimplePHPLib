<?php
    namespace Lib\MySQL;

    /**
     * Gestão de Base de dados
     *
     * Class para facilitar a gestão de consultas e inserção de registos na base de dados
     *
     * @copyright  2021 Frederico Santos
     */ 
    class BDMySQL
    {
        /**
         * Get the current connection
         * @return PDO
         */
        public static function getConnection($server, $bd_name, $bd_user, $bd_user_password, $charset = "")
        {
            $connection = null;
            if($server != null && $bd_name != null && $bd_user != null)
            {
                try
                {
                    $connection = new \PDO('mysql:
                    host=' . $server . ';
                    dbname=' . $bd_name,
                    $bd_user, 
                    $bd_user_password, 
                    array(
                        \PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES " . $charset
                    ));

                    $bd_driver = $connection->getAttribute(\PDO::ATTR_DRIVER_NAME);
                    if($bd_driver != 'mysql')
                    {
                        throw new \Ex("Database driver '{$bd_driver}' is not supported", 001);
                    }
                }
                catch(\Exception $ex)
                {
                    //TODO: Tratamento de erros de SQL
                    throw new \Ex('It was not possible to connect to the database: {' . $ex->getMessage() . '}', 002);
                }

                $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $connection->setAttribute(\PDO::ATTR_ORACLE_NULLS, \PDO::NULL_EMPTY_STRING);
            }
            else
            {
                throw new \Ex("Oops... Database configuration incomplete!", 101);
            }

            return $connection;
        }

        /**
         * Get a list of results
         * @param string SQL Query to be executed
         * @param SQLConnection SQL Connection
         * @param array Parameters
         * @return array Result
         */
        public static function getList($sqlQuery, $connection, $params = null)
        {
            $result = null;

            $result = BDMySQL::execute_query($sqlQuery, $connection, $params);

            return $result;
        }

        /**
         * Obtains an entity
         * @param string SQL Query to be executed
         * @param SQLConnection SQL Connection
         * @param array Parameters
         * @return T Result
         */
        public static function getEntity($sqlQuery, $connection, $params = null)
        {
            $result = null;

            $data = BDMySQL::execute_query($sqlQuery, $connection, $params);
            if($data != null && count($data) > 0)
                $result = $data[0];

            return $result;
        }

        /**
         * Obtains a Boolean variable
         * @param string SQL Query to be executed
         * @param SQLConnection SQL Connection
         * @param array Parameters
         * @return bool Result
         */
        public static function getBool($sqlQuery, $connection, $params = null)
        {
            $result = false;

            $data = BDMySQL::execute_query($sqlQuery, $connection, $params);
            if($data != null && count($data) > 0 && count($data[0]) > 0)
            {
                $result = filter_var($data[0][key($data[0])], FILTER_VALIDATE_BOOLEAN);
            }

            return $result;
        }

        /**
         * Obtains a string variable
         * @param string SQL Query to be executed
         * @param SQLConnection SQL Connection
         * @param array Parameters
         * @return string Result
         */
        public static function getString($sqlQuery, $connection, $params = null)
        {
            $result = false;

            $data = BDMySQL::execute_query($sqlQuery, $connection, $params);
            if($data != null && count($data) > 0 && count($data[0]) > 0)
            {
                $result = $data[0][key($data[0])];
            }

            return $result;
        }

        /**
         * Obtains a int variable
         * @param string SQL Query to be executed
         * @param SQLConnection SQL Connection
         * @param array Parameters
         * @return int Result
         */
        public static function getInt($sqlQuery, $connection, $params = null)
        {
            $result = false;

            $data = BDMySQL::execute_query($sqlQuery, $connection, $params);
            if($data != null && count($data) > 0 && count($data[0]) > 0)
            {
                $result = filter_var($data[0][key($data[0])], FILTER_VALIDATE_INT);
            }

            return $result;
        }

        /**
         * Obtains a float variable
         * @param string SQL Query to be executed
         * @param SQLConnection SQL Connection
         * @param array Parameters
         * @return float Result
         */
        public static function getFloat($sqlQuery, $connection, $params = null)
        {
            $result = false;

            $data = BDMySQL::execute_query($sqlQuery, $connection, $params);
            if($data != null && count($data) > 0 && count($data[0]) > 0)
            {
                $result = filter_var($data[0][key($data[0])], FILTER_VALIDATE_FLOAT);
            }

            return $result;
        }

        /**
         * Run the query
         * @param string SQL Query to be executed
         * @param SQLConnection SQL Connection
         * @param array Parameters
         * @return array Result
         */
        private static function execute_query($sqlQuery, $connection, $params = null)
        {
            $result = null;

            try
            {
                BDMySQL::ValidateConnection($connection);
                $exec = $connection->prepare($sqlQuery);
                $exec->execute($params);
                $result = $exec->fetchAll(\PDO::FETCH_ASSOC);
            }
            catch(\Exception $ex)
            {
                throw new \Ex("Oops... Ocorreu um erro ao executar a query\n" . $ex->getMessage()); //TODO: Fazer sistema de códigos que grave no log o codigo do erro e devolva para o utilizador apenas o código
            }
            catch(\Error $ex)
            {
                throw new \Ex("Oops... Ocorreu um erro ao executar a query\n" . $ex->getMessage()); //TODO: Fazer sistema de códigos que grave no log o codigo do erro e devolva para o utilizador apenas o código
            }
            return $result;
        }

        /**
         * Run the query
         * @param string SQL Command to be executed
         * @param SQLConnection SQL Connection
         * @param array Parameters
         * @return bool Result
         */
        private static function execute_non_query($sqlQuery, $connection, $params = null)
        {
            $success = false;
            
            try
            {
                BDMySQL::ValidateConnection($connection);
                $exec = $connection->prepare($sqlQuery);
                $exec->execute($params);
                $success = true;
            }
            catch(\Exception $ex)
            {
                throw new \Ex("Oops... Ocorreu um erro ao executar a query\n" . $ex->getMessage()); //TODO: Fazer sistema de códigos que grave no log o codigo do erro e devolva para o utilizador apenas o código
            }
            catch(\Error $ex)
            {
                throw new \Ex("Oops... Ocorreu um erro ao executar a query\n" . $ex->getMessage()); //TODO: Fazer sistema de códigos que grave no log o codigo do erro e devolva para o utilizador apenas o código
            }

            return $success;
        }

        /**
         * Run the query (INSERT, UPDATE, DELETE)
         * @param string SQL Command to be executed
         * @param SQLConnection SQL Connection
         * @param array Parameters
         * @return bool Result
         */
        public static function execute($sqlQuery, $connection, $params = null)
        {
            return BDMySQL::execute_non_query($sqlQuery, $connection, $params);
        }

        public static function ValidateConnection($connection)
        {
            if($connection == null)
            {
                throw new \Ex('There is no open connection');
            }

            if(!$connection instanceof \PDO)
            {
                throw new \Ex('The connection is not of the PDO type. Check the used connection!');
            }
        }
    }
?>
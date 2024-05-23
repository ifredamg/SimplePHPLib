<?php
    /**
     * Utils
     * https://www.php.net/manual/en/language.exceptions.php
     */

    interface IException
    {
        public function getMessage(); // Exception message
        public function getCode(); // User-defined Exception code
        public function getFile(); // User-defined Exception code
        public function getLine(); // Source line
        public function getTrace(); // An array of the backtrace()
        public function getTraceAsString(); // Formated string of trace

        public function __toString(); // formated string for display
        public function __construct($message = null, $code = 0);
    }
    
    abstract class CustomException extends Exception implements IException
    {
        protected $message = 'Unknown exception'; // Exception message
        private $string; // Unknown
        protected $code = 0; // User-defined exception code
        protected string $file; // Source filename of exception
        protected int $line; // Source line of exception
        private $trace; // Unknown

        public function __construct($message = null, $code = 0, $gravarLog = true)
        {
            if($message == null)
            {
                throw new $this('Unknown error');
            }

            $this->message = $message;

            parent::__construct($message, $code);

            if($gravarLog)
                LogToFile($this->__toStringFile());
        }

        public function getStringMessage()
        {
            return strval($this->getMessage());
        }

        public function __toString()
        {
            return get_class($this) . " '{$this->getMessage()}' in {$this->getFile()}({$this->getLine()})\n" . "{$this->getTraceAsString()}" . " {ErrorCode: {$this->getCode()}}";
        }

        public function __toStringFile()
        {
            $txt = '';
            $txt .= 'Message: ' . $this->getMessage() . PHP_EOL;
            $txt .= 'ErrorCode: ' . $this->getCode() . PHP_EOL;
            $txt .= 'ErrorFile: ' . $this->getFile() . PHP_EOL;
            $txt .= 'ErrorLine: ' . $this->getLine() . PHP_EOL;
            $txt .= 'ErrorDate: ' . date('d/m/Y H:i:s') . PHP_EOL;
            $txt .= 'StackTrace: ' . get_class($this) . " '{$this->getMessage()}' in {$this->getFile()}({$this->getLine()})\n" . "{$this->getTraceAsString()}" . " {ErrorCode: {$this->getCode()}}";
            return $txt;
        }
    }

    class Ex extends CustomException
    {

    }

    class ExCancel extends CustomException
    {
        public function __construct($message = null, $code = 0)
        {
            parent::__construct($message, 999, false);
        }
    }

    function LogToFile($txt)
    {
        try 
        {
            $logFile = fopen("log.txt", "a");
            fwrite($logFile, $txt . PHP_EOL . PHP_EOL);
            fclose($logFile);
        }
        catch(Exception $ex) {}
    } 
?>
<?php
    //translate based on module path /moduleName/src/route/language
    //filenames are the languages
    class Translate {

        private static $data = array();
        
        public function __construct($path,$files)
        {

            
            foreach ($files as $file) {
                //making a readable directory link from file array
                $file = $path.$file;
                if(is_dir($file)){
                    throw new Exception("translation file not found");
                }

                $key = substr($file, 0, strpos($file, "."));

                $output = fopen($path."/".$file, 'r');
                while (($line = fgets($output)) !== false) {
                    $data = explode(",", trim($line),2);
                    if (count($data) < 1) {
                        throw new Exception("Error Processing Request", 1);
                    }
                    self::$data[$key][$data[0]] = $data[1];
                }
        
            }

        }

        public static function getTranslation($key,$lang = null){
            $lang = $_GET["lang"] ?? "en";//throw ? if it doesnt exist
            return self::$data[$lang][$key] ?? "error";
        }
    }


    function t($key,$language = null){
        return Translate::getTranslation($key,$language);
    }
?>
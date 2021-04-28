<?php
    //translate based on module path /moduleName/src/route/language
    //filenames are the languages
    class Translate {

        private static $dictionary = array();
        private static $type = ".txt";
        
        public static function init($path,$languages)
        {

            if (!empty($languages)){
                foreach ($languages as $language) {
                    //making a readable directory link from file array

                    $file = $path.DIRECTORY_SEPARATOR."languages".DIRECTORY_SEPARATOR.$language.self::$type;
                    if(is_dir($file)){
                        throw new Exception("translation file not found");
                    }

                    $output = fopen($file, 'r');
                    while (($line = fgets($output)) !== false) {
                        $tmp = explode(",", trim($line),2);
                        list($target,$translation) = $tmp;

                        //if malformed move to the next line
                        if (count($tmp) == 0) {
                            continue;
                        }
                        self::$dictionary[$language][$target] = $translation;
                    }
            
                }
            }
        }

        public static function getTranslation($target,$lang = null){
            $lang = $_GET["lang"] ?? "en";//throw ? if it doesnt exist
            return self::$dictionary[$lang][$target] ?? "no translation found for $target";
        }
    }


    function t($target,$language = null){
        return Translate::getTranslation($target,$language);
    }
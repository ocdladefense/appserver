<?php
    //translate based on module path /moduleName/src/route/language
    //filenames are the languages
    //remove core code for language files
    class Translate {

        private static $dictionary = array();
        private static $type = ".txt";
        private static $modulePath;
        
        public static function init($modulePath,$languages)
        {
            self::$modulePath = $modulePath;


            if (!empty($languages)){
                foreach ($languages as $language) {
                    //making a readable directory link from file array

                    $file = $modulePath.DIRECTORY_SEPARATOR."languages".DIRECTORY_SEPARATOR.$language.self::$type;
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
            
            return self::$dictionary[$lang][$target] ?? "no translation found for $target";
        }
        public static function getTranslationFromFile($target,$language,$ext = "html"){
            $filePath = self::$modulePath.DIRECTORY_SEPARATOR."languages".DIRECTORY_SEPARATOR."content".DIRECTORY_SEPARATOR.$target.".".$language.".".$ext;
            return file_get_contents($filePath);

        }
    }

    function t($target, $language = null,$loadFromFile = false){
        $language = getDefaultLanguage();
        return $loadFromFile ? Translate::getTranslationFromFile($target,$language) :Translate::getTranslation($target,$language);
    }
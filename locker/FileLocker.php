<?php

/**
 * SHEPHERD FILE LOCKER
 * @author SteveSplash <stevesplash4@gmail.com>
 * @description: THIS SCRIPT ENCRYPT ANY WEB FILE GIVEN AND MAKE IT IMPOSSIBLE FOR HACKERS
 * AND INTRUDERS TO VIEW, EDIT OR MANIPULATE THE FILE
 * @version v0.0.1
 * @
 */

class FileLocker
{
    // make sure this files are protected
    protected $file2lock, $file2output, $lmode;
    public function __construct($lock_mode, $file_to_lock, $file_to_output)
    {
        $this->file2lock = $file_to_lock;
        $this->file2output = $file_to_output;
        $this->lmode = $lock_mode;
        // stop if file(s) doesn't exists
            if (!file_exists($this->file2lock)) {
                echo "\n$this->file2lock doesn't exists\n";
                exit(1);
            }
        // know the mode user is running
        if ($this->lmode == 1) {
            $this->runEncoder(); // encoder
        } else if ($this->lmode == 0) {
            $this->runDecoder(); // decoder
        } else {
            echo "\nError: 'lock_mode' must be 0 or 1\n"; // else throw an error
            exit(1);
        }
    }

    // read all file content function
    private function readFileContent($fname)
    {
        $fh = fopen($fname, 'r');
        while (!feof($fh)) {
            $content = fread($fh, 9999999);
        }
        fclose($fh);
        return $content;
    }

    // write encoded data to file function
    private function writeFile($fname, $data)
    {
        $fileHandler = fopen($fname, 'w');
        fwrite($fileHandler, $data);
        fclose($fileHandler);
    }
    private function encodeThisData($filecontent)
    {
        return strrev(base64_encode(base64_encode($filecontent)));
    }

    private function decodeThisData($encoded_data)
    {
        return base64_decode(base64_decode(strrev($encoded_data)));
    }

    private function exportFile($file_content)
    {
        return "<?php \$data = base64_decode(base64_decode(strrev(\"" . $file_content . "\"))); echo \$data; ?>";
    }
    private function runEncoder()
    {
        // foreach ($files as $file) {
        //     # code...
        // }
        $prepared_encoded_data = $this->encodeThisData($this->readFileContent($this->file2lock));
        $this->writeFile($this->file2output, $this->exportFile($prepared_encoded_data));
    }
    private function runDecoder()
    {
        function runPreDecoder($data)
        {
            if ($data !== '') {
                $firstData = explode("<?php \$data = base64_decode(base64_decode(strrev(\"", $data);
                unset($firstData[0]);
                $secondData = explode("\"))); echo \$data; ?>", $firstData[1]);
                return $secondData[0];
            } else {
                echo "\n[Error]: Predecoder has received empty data! make sure you entered all required data by the Decoder\n";
            }
        }
        //read in a file and store it in a var freshData
        $freshData = $this->readFileContent($this->file2lock);

        //now runPreDecoder
        $prepared_decoded_data = runPreDecoder($freshData);

        // now run decoder
        $decoded_data = $this->decodeThisData($prepared_decoded_data);

        // now write plain data to the filename provided by the user
        $this->writeFile($this->file2output, $decoded_data);
    }
}


/**
 * 
 * HOW TO USE:
 * $FileEncoder = new FileLocker(1, "hey.txt", "enchey.php");
 * $FileDecoder = new FileLocker(0, "enchey.php", "dechey.php");
 */



/**
 * FIX:
 *      1. File path problems
 * TODO:
 *      1. turn filenames into arrays so that multiple files can be processed 
 *      2. introducing real encryption method
 *      3. integrate gzip and many more obfuscation methods
 *      4. release to public domain: Github
 */
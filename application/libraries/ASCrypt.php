<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ASCrypt class for ci-ascrypt library.
 *
 * @author Ale Mohamad <hello@alemohamad.com>
 * @version 1.0
 * @link http://github.com/alemohamad/ci-ascrypt
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */

class ASCrypt
{

    private $str_crypt = 'e7NjchMCEGgTpsx3mKXbVPiAqn8DLzWo_6.tvwJQ-R0OUrSak954fd2FYyuH~1lIBZ';
    private $str_key;
    private $debug = FALSE;

    function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->load->config('ascrypt');

        $this->set_key($this->ci->config->item('str_key'));
        $this->set_debug($this->ci->config->item('debug'));
    }

    /**
     * Set the cryptphrase
     *
     * @param string $str
     *
     * @return void
     */
    public function set_phrase($str)
    {
        $this->str_crypt = $str;
    }

    /**
     * Set the keyphrase
     *
     * @param string $str
     *
     * @return void
     */
    public function set_key($str)
    {
        $this->str_key = $str;
    }

    /**
     * Set if the text is to be shown or not (debug option)
     *
     * @param string $state
     *
     * @return void
     */
    public function set_debug($state)
    {
        $this->debug = $state;
    }

    /**
     * Verify if the key is setted or not in the config file
     *
     * @return boolean
     */
    private function isset_key()
    {
        if( empty( $this->str_key ) ) {
            die("You must first set the key to use encryption.");
        } else {
            return true;
        }
    }

    /**
     * Encrypt string method
     *
     * @param string $string
     *
     * @return string
     */
    public function encrypt($string)
    {
        if ( $this->debug == TRUE ) {
            return $string;
        }

        $this->isset_key();

        $returnString = "";
        $charsArray = str_split($this->str_crypt);
        $charsLength = count($charsArray);
        $stringArray = str_split($string);
        $keyArray = str_split(hash('sha256', $this->str_key));
        $randomKeyArray = array();

        while ( count($randomKeyArray) < $charsLength ) {
            $randomKeyArray[] = $charsArray[ rand(0, $charsLength-1) ];
        }

        for ( $a = 0; $a < count($stringArray); $a++ ) {
            $numeric = ord($stringArray[$a]) + ord($randomKeyArray[$a%$charsLength]);
            $returnString .= $charsArray[floor($numeric/$charsLength)];
            $returnString .= $charsArray[$numeric%$charsLength];
        }

        $randomKeyEnc = '';

        for ( $a = 0; $a < $charsLength; $a++ ) {
            $numeric = ord($randomKeyArray[$a]) + ord($keyArray[$a%count($keyArray)]);
            $randomKeyEnc .= $charsArray[floor($numeric/$charsLength)];
            $randomKeyEnc .= $charsArray[$numeric%$charsLength];
        }

        return $randomKeyEnc . hash('sha256', $string) . $returnString;
    }

    /**
     * Decrypt string method
     *
     * @param string $string
     *
     * @return string
     */
    public function decrypt($string)
    {
        if ($this->debug == TRUE) {
            return $string;
        }

        $this->isset_key();

        $returnString = "";
        $charsArray = str_split($this->str_crypt);
        $charsLength = count($charsArray);
        $keyArray = str_split(hash('sha256', $this->str_key));
        $stringArray = str_split(substr($string,($charsLength*2)+64));
        $sha256 = substr($string,($charsLength*2),64);
        $randomKeyArray = str_split(substr($string,0,$charsLength*2));

        $randomKeyDec = array();

        for ( $a = 0; $a < $charsLength*2; $a+=2 ) {
            $numeric = array_search($randomKeyArray[$a],$charsArray) * $charsLength;
            $numeric += array_search($randomKeyArray[$a+1],$charsArray);
            $numeric -= ord($keyArray[floor($a/2)%count($keyArray)]);
            $randomKeyDec[] = chr($numeric);
        }

        for ( $a = 0; $a < count($stringArray); $a+=2 ) {
            $numeric = array_search($stringArray[$a],$charsArray) * $charsLength;
            $numeric += array_search($stringArray[$a+1],$charsArray);
            $numeric -= ord($randomKeyDec[floor($a/2)%$charsLength]);
            $returnString .= chr($numeric);
        }

        if( hash('sha256', $returnString) != $sha256 ) {
            return false;
        } else {
            return $returnString;
        }
    }

}

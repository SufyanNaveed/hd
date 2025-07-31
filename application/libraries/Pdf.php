<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * CodeIgniter PDF Library
 *
 * Generate PDF in CodeIgniter applications.
 *
 * @package            CodeIgniter
 * @subpackage        Libraries
 * @category        Libraries
 * @author            CodexWorld
 * @license            https://www.crowdforgeeks.com/license/
 * @link            https://www.crowdforgeeks.com
 */

// reference the Dompdf namespace
use Dompdf\Dompdf;
use Dompdf\Options;
class Pdf
{
    public function __construct(){

        // include autoloader
       // require_once dirname(__FILE__).'/dompdf/autoload.inc.php';
       require_once APPPATH . 'third_party/dompdf/vendor/autoload.php';

        // instantiate and use the dompdf class
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $pdf = new DOMPDF($options);

        $CI =& get_instance();
        $CI->dompdf = $pdf;

    }
}
?>
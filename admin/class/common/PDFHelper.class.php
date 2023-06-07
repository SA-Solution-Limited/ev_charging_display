<?php
use iio\libmergepdf\Merger;
class PDFHelper {
	
	/**
	 * Using dompdf, convert html to pdf
	 * 
	 * @param string $html
	 *        	Html content
	 * @param string $base_path        	
	 * @param string $paper        	
	 * @param string $orientation        	
	 * @param string $stream_filename
	 *        	Optional. Null to obtain file content in return value
	 * @return NULL Ambigous NULL>
	 */
	public static function genPdfByHtml($html, $base_path = ".", $paper = "a4", $orientation = "portrait", $stream_filename = null) {
		if (get_magic_quotes_gpc ())
			$html = stripslashes ( $html );
		require_once ("lib/dompdf/dompdf_config.inc.php");
		$dompdf = new DOMPDF ();
		$dompdf->load_html ( $html, 'UTF-8' );
		$dompdf->set_base_path ( $base_path );
		$dompdf->set_paper ( $paper, $orientation );
		$dompdf->render ();
		if ($stream_filename != null) {
			$dompdf->stream ( $stream_filename );
			return null;
		} else
			return $dompdf->output ();
	}
	
	/**
	 * Generate pdf using template and parameters
	 * 
	 * @param string $template_name
	 *        	Template filename
	 * @param array $params
	 *        	Key value pair
	 * @param string $stream_out_filename
	 *        	Optional. Null to obtain file content in return value
	 * @return Ambigous <NULL, Ambigous, string>
	 */
	public static function genPdfByTemplate($template_filename, $params, $stream_out_filename = null) {
		$template_path = SITE_ROOT . globalSetting::$template_path;
		$html = file_get_contents ( $template_path . '/' . $template_filename );
		$html = str_replace ( array_keys ( $params ), array_values ( $params ), $html );
		
		return self::genPDFByHtml ( $html, $template_path, "a4", "portrait", $stream_out_filename );
	}
	
	/**
	 * Using dompdf, convert html to pdf
	 *
	 * @param string $html
	 *        	Html content
	 * @param string $stream_filename
	 *        	Optional. Null to obtain file content in return value
	 * @return NULL Ambigous NULL>
	 */
	public static function genLandscapeA4PdfByHtml($html, $stream_filename = null) {
		$base_path = ".";
		$paper = 'A4';
		$orientation = 'landscape';
		if (get_magic_quotes_gpc ())
			$html = stripslashes ( $html );
		require_once ("lib/dompdf/dompdf_config.inc.php");
		$dompdf = new DOMPDF ();
		$dompdf->load_html ( $html, 'UTF-8' );
		$dompdf->set_base_path ( $base_path );
		$dompdf->set_paper ( $paper, $orientation );
		$dompdf->render ();
	
		if ($stream_filename != null) {
			$dompdf->stream ( $stream_filename );
			return null;
		} else
			return $dompdf->output ();
	}
	
	public static function genPortraitA4PdfByHtml($html, $stream_filename = null) {
		$base_path = ".";
		$paper = 'A4';
		$orientation = 'portrait';
		if (get_magic_quotes_gpc ())
			$html = stripslashes ( $html );
		require_once ("lib/dompdf/dompdf_config.inc.php");
		$dompdf = new DOMPDF ();
		$dompdf->load_html ( $html, 'UTF-8' );
		$dompdf->set_base_path ( $base_path );
		$dompdf->set_paper ( $paper, $orientation );
		$dompdf->render ();
	
		if ($stream_filename != null) {
			$dompdf->stream ( $stream_filename );
			return null;
		} else
			return $dompdf->output ();
	}
	
	/**
	 * Bind given data model to tempalte to generate pdf
	 * @param string $templatePhpFile Path to php
	 * @param object $dataModel Object data model to be referenced by template using $model
	 * @param string $outputFile Path to file
	 */
	public static function genPdfByDataModel($templatePhpFile, $dataModel, $outputFile) {
		ob_start();
		$model = &$dataModel;
		require $templatePhpFile;
		$html = ob_get_clean();
		//file_put_contents(SITE_ROOT . '/test/testTpl.html', $html);
		self::genPdfByHtml2($html, $outputFile);
	}
	
	/**
	 * Convert html to pdf and write to file
	 * @param string $html HTML content
	 * @param string $outputFile Path to file
	 */
	public static function genPdfByHtml2($html, $outputFile) {
		// http://www.mpdf1.com/repos/example61_new_mPDF_v6-0_features.pdf
		/**
		 * Ensure that you have write permissions set for the following folders:
		 * /ttfontdata/
		 * /tmp/
		 * /graph_cache/
		 */
		require_once 'lib/vendor/autoload.php';
		$pdf = new mPDF();
		$pdf->autoScriptToLang = true;
		$pdf->baseScript = 1;
		$pdf->autoLangToFont = true;
		$pdf->WriteHTML($html);
		$pdf->Output($outputFile, 'F');
	}
	
	/**
	 * Convert html to pdf and write to file
	 * @param string $html HTML content
	 * @param string $outputFile Path to file
	 */
	public static function genPdfByHtml2Horizontal($html, $outputFile) {
		// http://www.mpdf1.com/repos/example61_new_mPDF_v6-0_features.pdf
		/**
		 * Ensure that you have write permissions set for the following folders:
		 * /ttfontdata/
		 * /tmp/
		 * /graph_cache/
		 */
		require_once 'lib/vendor/autoload.php';
		$pdf = new mPDF('utf-8', "A4-L", 0, '', 0,0,0,0,0,0,'L');
		$pdf->autoScriptToLang = true;
		$pdf->baseScript = 1;
		$pdf->autoLangToFont = true;
		$pdf->WriteHTML($html);
		$pdf->Output($outputFile, 'F');
	}
	
	public static function mergePdfs($importPdfs, $outputPdf) {
		require_once 'lib/vendor/autoload.php';
		
		$m = new Merger();
		foreach ($importPdfs as $importPdf) {
			$m->addFromFile($importPdf);
		}
		file_put_contents($outputPdf, $m->merge());
	}
}
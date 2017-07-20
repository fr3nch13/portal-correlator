<?php
// Extracts vectors from a defined source
// very similar to plugins/utilities ExtractorBehavior

class ImporterBehavior extends ModelBehavior 
{
	// model/instance specific settings
	// some settings to be overwritten by the import_types table
	public $settings = array();
	
	public $defaults = array(
		'source_key' => false,
		'parser' => 'csv',
		// defines where to find the vectors
		// see Set::extract(); from the cakephp core for ones like csv and other arrays
		'vector_fields' => array(),
	);
	
	// mime types of files that we can parse
	// map a mimetype to a function that can extract the string
	public $parsableMimeTypes = array(
		'text/plain' => 'fileContent_text',
		'application/pdf' => 'fileContent_pdf',
	);
	
	// Used to hold the external php class for pdf tools
	public $Pdftools = false;
	
	public function setup(Model $Model, $config = array()) 
	{
	/*
	 * Set everything up
	 */
		// merge the default settings with the model specific settings
		$this->settings[$Model->alias] = array_merge($this->defaults, $config);
	}
	
////// public functions
	public function Importer_setConfig(Model $Model, $settings = array())
	{
		$this->settings[$Model->alias] = array_merge($this->defaults, $settings);
		return $this->settings[$Model->alias];
	}
	
	public function Importer_extractItemsFromFile(Model $Model, $file_path = false, $mimetype = false)
	{
	/*
	 * Extracts a string from a file and looks for vectors in that string
	 */
		
		if(!trim($file_path)) return false;
		if(!is_file($file_path)) return false;
		if(!is_readable($file_path)) return false;
		
		if(!$mimetype)
		{
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mimetype = finfo_file($finfo, $file_path);
			finfo_close($finfo);
		}
		if(!in_array($mimetype, array_keys($this->parsableMimeTypes))) return false;
		
		$function = $this->parsableMimeTypes[$mimetype];
		
		$string = $this->$function($Model, $file_path);
		
		$items = $this->Importer_extractItems($Model, $string, $this->settings[$Model->alias]);
		
		return $items;
	}
	
	public function Importer_extractItems(Model $Model, $string = false, $parser_settings = false)
	{
		if(!trim($string)) return false;
		if(!$parser_settings) return false;
		
		if(isset($parser_settings['parser']) and $parser_settings['parser'] == 'csv')
		{
			return $this->Importer_csvToArray($Model, $string, $parser_settings['vector_fields']);
		}
		return false;
	}
	
////// internal functions
	
	//// parsers
	public function Importer_csvToArray(Model $Model, $string = array(), $columns = array())
	{
		if(!$columns) return false;
		
		$headers = array();
		$csv_data = array();
		$lines = explode("\n", $string);
		
		$i=0;
		foreach($lines as $line)
		{
			$csv_line = str_getcsv($line);
			if(empty($headers)) { $headers = $csv_line; continue; }
			
			$i++;
			foreach($csv_line as $k => $v)
			{
				$column_name = $headers[$k];
				$csv_data[$i][$column_name] = $v;
			}
		}
		
		$out = array();
		foreach($csv_data as $i => $items)
		{
			foreach($columns as $column_name)
			{
				if(isset($items[$column_name])) 
				{
					$vector = $items[$column_name];
					if(trim($vector))
					{
						$out[$items[$column_name]] = array(
							'vector' => $vector,
							'column' => $column_name,
						);
					}
				}
			}
		}
		unset($csv_data);
		ksort($out);
		return $out;
	}

	//
	public function Importer_objectToArray(Model $Model, $obj) 
	{
		if(!$obj) return '';
		$arrObj = is_object($obj) ? get_object_vars($obj) : $obj;
		$arr = '';
		foreach ($arrObj as $key => $val) 
		{
			$val = (is_array($val) || is_object($val)) ? $this->Importer_objectToArray($Model, $val) : $val;
			$arr[$key] = $val;
		}
		return $arr;
	}
	
///// support functions
	//
	protected function cleanString(Model $Model, $string = '')
	{
	/*
	 * Cleans the text up
	 */
		if($string)
		{
			// replaces: [.], [. ], [ .], [ . ] - with a period '.'
			$string = preg_replace('/[\s+]?\[[\s+]?(dot|\.|\:)[\s+]?\][\s+]?/i', '.', $string);
			// replaces: {.}, {. }, { .}, { . } - with a period '.'
			$string = preg_replace('/[\s+]?\{[\s+]?(dot|\.|\:)[\s+]?\}[\s+]?/i', '.', $string);
			// changes hxxp to http
			$string = preg_replace('/(hxxp|httx)(\s+)?/i', 'http', $string);
			// removes html break tags (<br />)
			$string = preg_replace('/\<br\/?\>/i', ' ', $string);
			$string = preg_replace('/\/\/\]/i', '//', $string);
			$string = $this->utf8toISO8859_1($Model, $string);
			$string = preg_replace('/([\200-\277])/e', '', $string);
			
			if($this->settings[$Model->alias]['normalize_dot_variants'])
			{
				$string = str_replace($this->settings[$Model->alias]['dot_variants'], '.', $string);
			}
			
			// normalize it to be within utf8
		}
		return $string;
	}
	
	//
	protected function fileContent_text(Model $Model, $file_path = false)
	{
	/*
	 * Extract the string for a plain text file
	 */
		return file_get_contents($file_path);
	}
	
	//
	protected function fileContent_pdf(Model $Model, $file_path = false)
	{
	/*
	 * Extract the string for a plain text file
	 */
		if(!$this->Pdftools)
		{
			App::import('Vendor', 'pdftools');
			$this->Pdftools = new Pdftools();
		}
		
		$this->Pdftools->filePath($file_path);
		
		return $this->Pdftools->getContent();
	}
	
	// stolen from Sanitize::paranoid
	// instead of removing the item, it replaces it with a space
	protected function paranoidSpace(Model $Model, $string, $allowed = array()) 
	{
		$allow = null;
		if (!empty($allowed)) 
		{
			foreach ($allowed as $value) 
			{
				$allow .= "\\$value";
			}
		}
		
		if (is_array($string)) 
		{
			$cleaned = array();
			foreach ($string as $key => $clean) 
			{
				$cleaned[$key] = preg_replace("/[^{$allow}a-zA-Z0-9]/", ' ', $clean);
			}
		} 
		else 
		{
			$cleaned = preg_replace("/[^{$allow}a-zA-Z0-9]/", ' ', $string);
		}
		return $cleaned;
	}
	
	//
	protected function utf8toISO8859_1(Model $Model, $string)
	{
	/*
	 * replaces characters wirh their Western types
	 * removes the rest of the utf8 characters
	 */
		$accented = array(
			'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'A', 'A',
			'Ç', 'C', 'C', 'Œ',
			'D', 'Ð',
			'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'a', 'a',
			'ç', 'c', 'c', 'œ',
			'd', 'd',
			'È', 'É', 'Ê', 'Ë', 'E', 'E',
			'G',
			'Ì', 'Í', 'Î', 'Ï', 'I',
			'L', 'L', 'L',
			'è', 'é', 'ê', 'ë', 'e', 'e',
			'g',
			'ì', 'í', 'î', 'ï', 'i',
			'l', 'l', 'l',
			'Ñ', 'N', 'N',
			'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'O',
			'R', 'R',
			'S', 'S', 'Š',
			'ñ', 'n', 'n',
			'ò', 'ó', 'ô', 'ö', 'ø', 'o',
			'r', 'r',
			's', 's', 'š',
			'T', 'T',
			'Ù', 'Ú', 'Û', 'U', 'Ü', 'U', 'U',
			'Ý', 'ß',
			'Z', 'Z', 'Ž',
			't', 't',
			'ù', 'ú', 'û', 'u', 'ü', 'u', 'u',
			'ý', 'ÿ',
			'z', 'z', 'ž',
			'?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?',
			'?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?',
			'?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?',
			'?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?'
			);

		$replace = array(
			'A', 'A', 'A', 'A', 'A', 'A', 'AE', 'A', 'A',
			'C', 'C', 'C', 'CE',
			'D', 'D',
			'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'a', 'a',
			'c', 'c', 'c', 'ce',
			'd', 'd',
			'E', 'E', 'E', 'E', 'E', 'E',
			'G',
			'I', 'I', 'I', 'I', 'I',
			'L', 'L', 'L',
			'e', 'e', 'e', 'e', 'e', 'e',
			'g',
			'i', 'i', 'i', 'i', 'i',
			'l', 'l', 'l',
			'N', 'N', 'N',
			'O', 'O', 'O', 'O', 'O', 'O', 'O',
			'R', 'R',
			'S', 'S', 'S',
			'n', 'n', 'n',
			'o', 'o', 'o', 'o', 'o', 'o',
			'r', 'r',
			's', 's', 's',
			'T', 'T',
			'U', 'U', 'U', 'U', 'U', 'U', 'U',
			'Y', 'Y',
			'Z', 'Z', 'Z',
			't', 't',
			'u', 'u', 'u', 'u', 'u', 'u', 'u',
			'y', 'y',
			'z', 'z', 'z',
			'A', 'B', 'B', 'r', 'A', 'E', 'E', 'X', '3', 'N', 'N', 'K', 'N', 'M', 'H', 'O', 'N', 'P',
			'a', 'b', 'b', 'r', 'a', 'e', 'e', 'x', '3', 'n', 'n', 'k', 'n', 'm', 'h', 'o', 'p',
			'C', 'T', 'Y', 'O', 'X', 'U', 'u', 'W', 'W', 'b', 'b', 'b', 'E', 'O', 'R',
			'c', 't', 'y', 'o', 'x', 'u', 'u', 'w', 'w', 'b', 'b', 'b', 'e', 'o', 'r'
			);
		$string = str_replace($accented, $replace, $string);
		$string = utf8_decode($string);
		
		return $string;
	}
}
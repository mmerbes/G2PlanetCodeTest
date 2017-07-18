<?php
class deviceInfo {

    public $result = [
        "min" => null,
        "max" => null,
        "mean" => 0,
        "count" => 0,
        "stddev" => 0,
        "weights" => []
    ];

    public $xmlArr;
  /**
     * Returns an array with the 'count', 'min', 'max', 'average' and 'stddev' for the device
     * weights obtained from the data document described by the schema.
     * 
     * @param string $url the URL of an XML document containing the device-list
     *
     * @return array with keys: 'count' has an integer value equal to the number of individual devices in
     *               the device list (number of device element times the quantity of each); and 'min', 'max',
     *               'average' and 'stddev' are the minimum, maximum, arithmetic mean (average) and
     *               standard deviation of the weights of all devices. All weight statistics should be returned
     *               in US ounces.
     *               Note the Stddev is returned using "Population Standard Deviation" instead of 
     *               "Sample Standard Deviation"
     *
     * @throws InvalidArgumentException if the URL is invalid
     * @throws RuntimeException on networking or document processing error
  */
    public function __construct($url) {

        $file = file_get_contents($url, true);
        if(!$file) {
           throw new InvalidArgumentException('Invalid file ULR ' .$url);
        } else {
            $domObj = new xmlToArrayParser($file);
        }

        if($domObj->parse_error) {
            echo $domObj->get_xml_error();
            throw new RuntimeException('Parsing Error');
        }
        $this->xmlArr = $domObj->array;
    }

    public function deviceListInfoSummary() {
        $result = $this->result;
        $domArr = $this->xmlArr;
        foreach($domArr["f:device-list"]["f:device"] as $child) {
            $result["count"] += $child["attrib"]["quantity"];
            $weight = $child["f:weight"]["cdata"]; 
            if($child["f:weight"]["attrib"]["units"] == "pounds") {
                $weight = $weight * 16; 
            }
            if($result["min"] === null) {
                $result["min"] = $weight;    
            }
            if($result["max"] === null) {
                $result["max"] = $weight;    
            }
            array_push($result["weights"], $weight);
        }

        $result["min"] = min($result["weights"]);
        $result["max"] = max($result["weights"]);
        $size = count($result["weights"]);
        $result["mean"] = (array_sum($result["weights"]) / $size);
        for($i = 0; $i < $size; ++$i) {
            $temp = pow(($result["weights"][$i] - $result["mean"]), 2);
            $result["stddev"] += $temp;
        }
        $result["stddev"] = sqrt(($result["stddev"] / $size));
        $this->result = $result;
        return $this->result;
    }
  
}


$url = "https://main.g2planet.com/codetest/example.xml";
$device = new deviceInfo($url);
print_r($device->deviceListInfoSummary());

class xmlToArrayParser { 
  /** The array created by the parser can be assigned to any variable: $anyVarArr = $domObj->array.*/ 
  public  $array = array(); 
  public  $parse_error = false; 
  private $parser; 
  private $pointer; 
  /** Constructor: $domObj = new xmlToArrayParser($xml); */ 
  public function __construct($xml) { 
    $this->pointer =& $this->array; 
    $this->parser = xml_parser_create("UTF-8"); 
    xml_set_object($this->parser, $this); 
    xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false); 
    xml_set_element_handler($this->parser, "tag_open", "tag_close"); 
    xml_set_character_data_handler($this->parser, "cdata"); 
    $this->parse_error = xml_parse($this->parser, ltrim($xml))? false : true; 
  } 
  
  /** Free the parser. */ 
  public function __destruct() { xml_parser_free($this->parser);} 

  /** Get the xml error if an an error in the xml file occured during parsing. */ 
  public function get_xml_error() { 
    if($this->parse_error) { 
      $errCode = xml_get_error_code ($this->parser); 
      $thisError =  "Error Code [". $errCode ."] \"<strong style='color:red;'>" . xml_error_string($errCode)."</strong>\", 
                            at char ".xml_get_current_column_number($this->parser) . " 
                            on line ".xml_get_current_line_number($this->parser).""; 
    }else $thisError = $this->parse_error; 
    return $thisError; 
  } 
  
  private function tag_open($parser, $tag, $attributes) { 
    $this->convert_to_array($tag, 'attrib'); 
    $idx=$this->convert_to_array($tag, 'cdata'); 
    if(isset($idx)) { 
      $this->pointer[$tag][$idx] = Array('@idx' => $idx,'@parent' => &$this->pointer); 
      $this->pointer =& $this->pointer[$tag][$idx]; 
    }else { 
      $this->pointer[$tag] = Array('@parent' => &$this->pointer); 
      $this->pointer =& $this->pointer[$tag]; 
    } 
    if (!empty($attributes)) { $this->pointer['attrib'] = $attributes; } 
  } 

  /** Adds the current elements content to the current pointer[cdata] array. */ 
  private function cdata($parser, $cdata) { $this->pointer['cdata'] = trim($cdata); } 

  private function tag_close($parser, $tag) { 
    $current = & $this->pointer; 
    if(isset($this->pointer['@idx'])) {unset($current['@idx']);} 
    
    $this->pointer = & $this->pointer['@parent']; 
    unset($current['@parent']); 
    
    if(isset($current['cdata']) && count($current) == 1) { $current = $current['cdata'];} 
    else if(empty($current['cdata'])) {unset($current['cdata']);} 
  } 
  
  /** Converts a single element item into array(element[0]) if a second element of the same name is encountered. */ 
  private function convert_to_array($tag, $item) { 
    if(isset($this->pointer[$tag][$item])) { 
      $content = $this->pointer[$tag]; 
      $this->pointer[$tag] = array((0) => $content); 
      $idx = 1; 
    }else if (isset($this->pointer[$tag])) { 
      $idx = count($this->pointer[$tag]); 
      if(!isset($this->pointer[$tag][0])) { 
        foreach ($this->pointer[$tag] as $key => $value) { 
            unset($this->pointer[$tag][$key]); 
            $this->pointer[$tag][0][$key] = $value; 
    }}}else $idx = null; 
    return $idx; 
  } 
}
<?php

/*
A simple function, which parses the werx-validation ( https://github.com/werx/validation  ) ruleset
into html element tags usable for jQuery Form Validator http://www.formvalidator.net 

Examples:
Ruleset: startswith[abc]|required|minlength[4]|maxlength[8]
Resulting html control tags: 
data-validation="custom required length" data-validation-regexp="^abc" data-validation-length="4-8"

Ruleset: contains[abc]|required|minlength[4]|maxlength[13]
Resulting html control tags: 
data-validation="custom required length" data-validation-regexp="abc" data-validation-length="4-13"

Ruleset: required|maxlength[13]|integer
Resulting html control tags: 
data-validation="required length number" data-validation-length="0-13" data-validation-allowing="range[-2147483647,2147483647,negative]"

Ruleset: required|greaterthan[-314]|numeric
Resulting html control tags: 
data-validation="required number" data-validation-allowing="range[-314,2147483647,negative]"

The resulting string can be simply added to any control... for example:

$result = getFormvalidatorString('startswith[abc]|required|minlength[4]|maxlength[8]');
$control='<input type="text" id="mytextid" name="mytext" ' . $result . ' />';
which will generate the following:
 
<input type="text" id="mytextid" name="mytext" data-validation="custom required length" data-validation-regexp="^abc" data-validation-length="4-8" />

So after including the js file <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>
you can check whether the input value is valid or not

$('#mytextid' ).validate(function(valid, elem) {
          if (valid) {
              console.log('it is valid');
         } else {
              console.log('It is Invalid');
         }
} );

*/


///////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getFormvalidatorString($validationstring,$preset='',$onlypreset=''){
  $vallist=array();
  $optlist=array();
  if (strlen($validationstring)<5)return "";  
  $allvals = explode('|',$validationstring);
  for ($x = 0; $x < count($allvals);$x++){
        $val = $allvals[$x];
            $r = $val;
			$rule_name = $r;
			$rule_params = [];

			// For each rule in the list, see if it has any parameters. Example: minlength[5].
			if (preg_match('/\[(.*?)\]/', $r, $matches)) {
				// This one has parameters. Split out the rule name from it's parameters.
				$rule_name = substr($r, 0, strpos($r, '['));
				// There may be more than one parameters.
				$rule_params = explode(',', $matches[1]);
			} elseif (preg_match('/\{(.*?)\}/', $r, $matches)) {
				// This one has an array parameter. Split out the rule name from it's parameters.
				$rule_name = substr($r, 0, strpos($r, '{'));
				// There may be more than one parameter.
				$rule_params = array(explode(',', $matches[1]));
			}
			$return[$rule_name] = $rule_params;
  }
  
$lengthset = false;
$voptlist['length']['min'] = 0;
$voptlist['length']['max'] = 4096;
$numberrangeset=false;
$voptlist["number"]['min'] = -2147483647;
$voptlist["number"]['max'] = 2147483647;

  foreach($return as $key => $valueArr){
            if (count($valueArr)> 0) $value = $valueArr[0];
            switch($key){
                case "required":
                    $vallist[]="required";
                break;
                case "minlength":
                    $vallist[]="length";
                    $lengthset=true;
                    $voptlist["length"]['min'] = $value; // 'data-validation-length="min'.$value.'"'; 
  
                    /*data-validation-length="min9999"*/
                break;
                case "maxlength":
                    $vallist[]="length";
                    $lengthset=true;  
                    /*data-validation-length="max9999"*/
                    $voptlist["length"]['max'] = $value; //'data-validation-length="max'.$value.'"';                    
                break;
                case "exactlength":
                    $vallist[]="length";
                    $lengthset=true;
                    $voptlist["length"]['min'] = $value;   
                    $voptlist["length"]['max'] = $value;
                    /*data-validation-length="9999-9999"*/
                break;
                case "float":
                    $vallist[]="number"; 
                    $optlist["number"] = 'data-validation-allowing="float"';
                    /*data-validation-allowing="float"*/
                break;
                case "numeric":
                    $vallist[]="number";  
                break;
                case "integer":
                    $vallist[]="number";
                    $numberrangeset=true;
                      
                break;
                case "greaterthan":
                    $vallist[]="number";
                    $numberrangeset=true; 
                    $voptlist["number"]['min'] = $value;
                    /*data-validation-allowing="range[9999,9007199254740991,negative]"*/
                break;
                case "lessthan":
                    $vallist[]="number";
                    $numberrangeset=true;
                    $voptlist["number"]['max']= $value;                      
                    /*data-validation-allowing="range[-9007199254740991,9999,negative]"*/
                break;
                case "alpha":
                    $vallist[]="custom";
                    $optlist['custom'] = 'data-validation-regexp="^([a-z, A-Z]+)$"';  
                    /*data-validation-regexp="^([a-z, A-Z]+)$"*/
                break;
                case "alphanumeric":
                    $vallist[]="alphanumeric";  
                break;
                case "email":
                    $vallist[]="email";  
                break;
                case "url":
                    $vallist[]="url";  
                break;
                case "startswith":
                    $vallist[]="custom";
                    $optlist['custom'] = 'data-validation-regexp="^'.$value.'"';  
                    /*data-validation-regexp="^9999"*/
                break;
                case "endswith":
                    $vallist[]="custom";
                    $optlist['custom'] = 'data-validation-regexp="'.$value.'$"';    
                    /*data-validation-regexp="9999$"*/
                break;
                case "contains":
                    $vallist[]="custom";
                    $optlist['custom'] = 'data-validation-regexp="'.$value.'"';    
                    /*data-validation-regexp="9999"*/
                break;
                case "regex":
                    $vallist[]="custom";  
                    $optlist['custom'] = 'data-validation-regexp="'.$value.'"';
                    /*data-validation-regexp="9999"*/
                break;
                case "date":
                    $vallist[]="date"; 
                    $optlist['date'] = 'data-validation-format="'.strtolower($value).'"';                      
                    /*data-validation-format="yyyymmdd" 9999 -> lcase 9999*/
                break;
            }
  }
    if (count($vallist)<1)return "";
    $sets = array_unique($vallist);
    $outt1 = ' data-validation="' . implode(" ",$sets) . '" ';
        if ($lengthset){
             $optlist["length"] = 'data-validation-length="'.$voptlist['length']['min'].'-'.$voptlist['length']['max'].'"';
        }
        if ($numberrangeset){
            $optlist["number"] = 'data-validation-allowing="range['.$voptlist["number"]['min'].','.$voptlist["number"]['max'].',negative]"';
        }    
    foreach($optlist as $key => $value){
        $outt1 .= " " . $value;
    }
        return $outt1;
}
//////////////////////////////////////////////

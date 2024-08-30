<?php

namespace TSBTAM\SnowstormTerminologyServerByTAGSExternalModule;

use ExternalModules\AbstractExternalModule;
use ExternalModules\ExternalModules;
class SnowstormTerminologyServerByTAGSExternalModule extends AbstractExternalModule implements \OntologyProvider
{

    public function redcap_every_page_before_render($project_id)
    {
        $manager = \OntologyManager::getOntologyManager();
        $manager->addProvider($this);
    }


	//Config in settings var, so validate correct params

    public function validateSettings($settings)
    {
        $errors = '';

        $siteCategory = $settings['site-category'];
        foreach ($siteCategory as $category) {
            if ($category != strip_tags($category)
                || strpos($category, "'") !== false
                || strpos($category, '"') !== false
            ) {
                $errors .= "Category has illegal characters - " . $category . "\n";
            }
        }
        $projectCategory = $settings['project-category'];
        foreach ($projectCategory as $category) {
            if ($category != strip_tags($category)
                || strpos($category, "'") !== false
                || strpos($category, '"') !== false
            ) {
                $errors .= "Category has illegal characters - " . $category . "\n";
            }
        }
        // el nombre no tiene etiquetas html
        foreach ($settings['site-name'] as $name) {
            if ($name != strip_tags($name)) {
                $errors .= "Name has illegal characters - " . $name . "\n";
            }
        }
        foreach ($settings['project-name'] as $name) {
            if ($name != strip_tags($name)) {
                $errors .= "Name has illegal characters - " . $name . "\n";
            }
        }

        foreach ($settings['site-return-no-result'] as $key => $returnNoResult) {
        $siteCNRCode = $settings['site-no-result-code'];
        $siteCNRLabel = $settings['site-no-result-label'];
            if ($returnNoResult) {
                // check error codes
                $label = trim($siteCNRLabel[$key]);
                $code = trim($siteCNRCode[$key]);
                if ($label === '') {
                    $errors .= "No Result Label is required [" . $siteCategory[$key] . "]\n";
                } else if ($label != strip_tags($label)) {
                    $errors .= "No Results Label has illegal characters -[" . $siteCategory[$key] . "] " . $label . "\n";
                }
                if ($code === '') {
                    $errors .= "No Result Code is required [" . $siteCategory[$key] . "]\n";
                } else if ($code != strip_tags($code)
                    || strpos($code, "'") !== false
                    || strpos($code, '"') !== false
                ) {
                    $errors .= "No Results Code has illegal characters [" . $siteCategory[$key] . "]- " . $code . "\n";
                }
            }
        }

        $projectCNRCode = $settings['project-no-result-code'];
        $projectCNRLabel = $settings['project-no-result-label'];

        foreach ($settings['project-return-no-result'] as $key => $returnNoResult) {
            if ($returnNoResult) {
                // check we have a code and label
                $label = trim($projectCNRLabel[$key]);
                $code = trim($projectCNRCode[$key]);
                if ($label === '') {
                    $errors .= "No Result Label is required [" . $projectCategory[$key] . "]\n";
                } else if ($label != strip_tags($label)) {
                    $errors .= "No Results Label has illegal characters [" . $projectCategory[$key] . "]- " . $label . "\n";
                }

                if ($code === '') {
                    $errors .= "No Result Code is required [" . $projectCategory[$key] . "]\n";
                } else if ($code != strip_tags($code)
                    || strpos($code, "'") !== false
                    || strpos($code, '"') !== false
                ) {
                    $errors .= "No Results Code has illegal characters [" . $projectCategory[$key] . "]- " . $code . "\n";
                }
            }
        }


        return $errors;
    }

    /**
     * Name in listing
     */
    public function getProviderName()
    {
        return 'Access SnowStorm Server by Action Tags';
    }


   
    public function getServicePrefix()
    {
        return 'SIMPLE';
    }

    function getSystemCategories()
    {
        $key = 'site-category-list';
        $keys = ['site-category' => 'category',
            'site-name' => 'name',
			'server_url' => 'ss_url',
			'modify_server_url_by_actiontag_allowed' => 'modify_ss_url_by_actiontag_allowed',
			'server_method' => 'ss_method',
			'modify_server_method_by_actiontag_allowed' => 'modify_ss_method_by_actiontag_allowed',
            'site-search-type' => 'search-type',
            'site-return-no-result' => 'return-no-result',
            'site-no-result-label' => 'no-result-label',
            'site-no-result-code' => 'no-result-code',
            'site-limit-results' => 'limit-results',
			'site-rest-service-token' => 'rest_service_token'];
        $subSettings = [];
        $rawSettings = $this->getSubSettings($key);
        foreach ($rawSettings as $data) {
            $subSetting = [];
            foreach ($keys as $k => $nk) {
                $subSetting[$nk] = $data[$k];
            }
            $subSettings[] = $subSetting;
        }
        return $subSettings;
    }

    function getProjectCategories()
    {
        $key = 'project-category-list';
        $keys = ['project-category' => 'category',
            'project-name' => 'name',
            'project-search-type' => 'search-type',
			'project_url' => 'ss_url',
			'modify_project_url_by_actiontag_allowed' => 'modify_ss_url_by_actiontag_allowed',
			'project_method' => 'ss_method',
			'modify_project_method_by_actiontag_allowed' => 'modify_ss_method_by_actiontag_allowed',
            'project-return-no-result' => 'return-no-result',
            'project-no-result-label' => 'no-result-label',
            'project-no-result-code' => 'no-result-code',
            'project-limit-results' => 'limit-results',
			'project-rest-service-token' => 'rest_service_token'];
        $subSettings = [];
        $rawSettings = $this->getSubSettings($key);
        foreach ($rawSettings as $data) {
            $subSetting = [];
            foreach ($keys as $k => $nk) {
                $subSetting[$nk] = $data[$k];
            }
            $subSettings[] = $subSetting;
        }
        return $subSettings;
    }

    /**
     * Returns a string in the online designer to select an onthology
	 * When  selected it calls javascript to update_ontology_selection($service, $category)
     * REDCap includes a javascript function   <service>_ontology_changed(service, category) 
	 *  .This function updates any element if matches  and will delete value if it does not
     */
    public function getOnlineDesignerSection()
    {
        $systemCategories = $this->getSystemCategories();
        $projectCategories = $this->getProjectCategories();

        $categories = [];
        foreach ($systemCategories as $cat) {
            $categories[$cat['category']] = $cat;
        }
        foreach ($projectCategories as $cat) {
            $categories[$cat['category']] = $cat;
        }

        $categoryList = '';
        foreach ($categories as $cat) {
            $category = $cat['category'];
            $name = $cat['name'];
            $categoryList .= "<option value='{$category}'>{$name}</option>\n";
        }

        $onlineDesignerHtml = <<<EOD
		<script type="text/javascript">
		  function SIMPLE_ontology_changed(service, category){
			var newSelection = ('SIMPLE' == service) ? category : '';
			$('#simple_ontology_category').val(newSelection);
		  }
		  
		</script>
		<div style='margin-bottom:3px;'>
		  Select Local Ontology to use:
		</div>
		<select id='simple_ontology_category' name='simple_ontology_category' 
					onchange="update_ontology_selection('SIMPLE', this.options[this.selectedIndex].value)"
					class='x-form-text x-form-field' style='width:330px;max-width:330px;'>
				{$categoryList}
		</select>
		EOD;
        return $onlineDesignerHtml;
    }

    /**
     * Search API with a search term for a given ontology
     * Returns array of results with Notation as key and PrefLabel as value.
     */
    public function searchOntology($category, $search_term, $result_limit)
    {
		 $field = $_GET['field'];
            if (isset($Proj->metadata[$_GET['field']])) {
                $annotations = $Proj->metadata[$_GET['field']]['misc'];
         }
           else if (isset($_GET['pid'])){
              //  $project_id = $_GET['pid'];
                $dd_array = \REDCap::getDataDictionary($_GET['pid'], 'array', false, array($_GET['field']));
                $annotations = $dd_array[$_GET['field']]['field_annotation'];

            }
			$tags = explode(';', $annotations);
			foreach ($tags as $tag) {
				list($k, $v) = explode('=', $tag);
				global $anotaciones;
				$anotaciones[trim($k)] = $v;
			}

			/*foreach ($tags as $tag) {
				list($k, $v) = explode('=', $tag);
				global $anotaciones;
				$anotaciones[trim($k)] = $v;
			}*/


		$systemCategories = $this->getSystemCategories();
        $projectCategories = $this->getProjectCategories();
        /*$hideChoice = $this->getHideChoice();*/
        $categories = [];
        foreach ($systemCategories as $cat) {
            $categories[$cat['category']] = $cat;
        }
        foreach ($projectCategories as $cat) {
            $categories[$cat['category']] = $cat;
        }

        $values = array();
        $categoryData = $categories[$category];
        if ($categoryData) {
			$rest_service_token = $categoryData['rest_service_token'];
			$limit =  $categoryData['limit-results'];


    
				$search_term_encoded = rawurlencode ($search_term);
				//Dentro de cada bucle voy a poner la opcion de si hay token, y si lo hay lo meto en la cadena
				
				
				//SSL access can be forced, or not
				$arrContextOptions=array(
					"ssl"=>array(
						"verify_peer"=>false,
						"verify_peer_name"=>false,
					),
				);  


				if (strlen($anotaciones['@TERMSERVERURL'])>0 && $categoryData['modify_ss_url_by_actiontag_allowed']== "true") {
					$termserverurl = $anotaciones['@TERMSERVERURL'];
				}
				else {
					$termserverurl = $categoryData['ss_url'];
				}

				if (strlen($anotaciones['@METHOD'])>0 && $categoryData['modify_ss_method_by_actiontag_allowed']== "true") {
					$termservermethod = $anotaciones['@METHOD'];
				}
				else {
					$termservermethod = $categoryData['ss_method'];
				}
				if (strlen($anotaciones['@BRANCH'])>0) {
					$branch  = $anotaciones['@BRANCH'];
				}
				else {$branch  = "MAIN";}

				$urlconsulta = $termserverurl.'/'.$branch.'/'.$termservermethod.'?';


				if (strlen($anotaciones['@ACTIVEFILTER'])>0) {
					$parms['activeFilter']  = $anotaciones['@ACTIVEFILTER'];
					//Only active concepts
				}
				if (strlen($anotaciones['@SEMANTICTAG'])>0) {
					$parms['semanticTag']  = $anotaciones['@SEMANTICTAG'];
					//Semantic tag (category....)
				}
				if (strlen($anotaciones['@GROUPBYCONCEPT'])>0) {
					$parms['groupbyconcept'] = $anotaciones['@GROUPBYCONCEPT'];
					//Group by concept (Only one result if multiple descriptions points to same code)
				}
				if (strlen($anotaciones['@OFFSET'])>0) {
					$parms['offset'] = $anotaciones['@OFFSET'];
					//Offset en los resultados
				}
				if (strlen($anotaciones['@DEFINITIONSTATUSFILTER'])>0) {
					$parms['definitionstatusfilter'] = $anotaciones['@DEFINITIONSTATUSFILTER'];
				}

				if (strlen($anotaciones['@TERMACTIVE'])>0) {
					$parms['termactive'] = $anotaciones['@TERMACTIVE'];
					//Solo termibnos activos
				}
				if (strlen($anotaciones['@LANGUAGE'])>0) {
					$parms['language'] = $anotaciones['@LANGUAGE'];
					//lan
				}
				if (strlen($anotaciones['@PREFERREDIN'])>0) {
					$parms['preferredin'] = $anotaciones['@PREFERREDIN'];
				}
				if (strlen($anotaciones['@ACCEPTABLEIN'])>0) {
					$parms['acceptablein'] = $anotaciones['@ACCEPTABLEIN'];
				}
				if (strlen($anotaciones['@PREFERREDORACCEPTABLEIN'])>0) {
					$parms['preferredoracceptablein'] = $anotaciones['@PREFERREDORACCEPTABLEIN'];
				}
				if (strlen($anotaciones['@STATEDECL'])>0) {
					$parms['statedecl'] = $anotaciones['@STATEDECL'];
				}
				if (strlen($anotaciones['@CONCEPTIDS'])>0) {
					$parms['conceptids'] = $anotaciones['@CONCEPTIDS'];
				}
				if (strlen($anotaciones['@SEARCHMODE'])>0) {
					$parms['searchmode'] = $anotaciones['@SEARCHMODE'];
				}
				if (strlen($anotaciones['@SEARCHAFTER'])>0) {
					$parms['searchafter'] = $anotaciones['@SEARCHAFTER'];
				}
				if (strlen($anotaciones['@ACCEPT-LANGUAGE'])>0) {
					$parms['accept-language'] = $anotaciones['@ACCEPT-LANGUAGE'];
				}
				if (strlen($anotaciones['@INCLUDEDESCENDANTCOUNT'])>0) {
					$parms['includedescendantcount'] = $anotaciones['@INCLUDEDESCENDANTCOUNT'];
				}
				if (strlen($anotaciones['@FORM'])>0) {
					$parms['form'] = $anotaciones['@FORM'];
				}
				if ($anotaciones['@LIMIT']>0) {
					$parms['limit'] = $anotaciones['@LIMIT'];
					$result_limit = $anotaciones['@LIMIT'];

					//Limite de resultados
				}
				if (strlen($anotaciones['@ECL'])>0) {
					$parms['ecl'] = $anotaciones['@ECL'];
					//$ecl = $anotaciones['@ecl'];

				}
				elseif ($limit>0) {
					$parms['limit'] = $limit;
					$result_limit = $limit;
					//If no limit is established it get a default value
				}
				
	


				//TODO: Aqui hay que construir un arbol de decisiones de algun modo para solo poder lanzar consultas coherentes.
				
				//Text to search for cames from RedCAP as $search_term ,  rawurlencode to pass via URL
				
				$parms['term'] = $search_term_encoded;
				
				
				//Finally, URL, branch , method and action tangs compose a  call to terminology server, an it will return a JSON string

				$rawValues = file_get_contents($urlconsulta.http_build_query($parms), false, stream_context_create($arrContextOptions));

				//error_log($urlconsulta.http_build_query($parms));

				if ($anotaciones['@SAVE_LOGS'] == "true") {
					$this->log($urlconsulta.http_build_query($parms),  $parms);
				}

    /* Log what was done*/
				//error_log($rawValues);

			

				//json return must be decodad. From action tags the path is especified in JSON for code and description

				$list = json_decode($rawValues, true);
               
                    foreach ($list['items'] as $item) {
						//first, check if a code is returned
						$code1 = $this->get('concept.conceptId',$item);
						$code2 = $this->get($anotaciones['@CODES_SUB_PATH'],$item);

						//If i have a code (description, active or not and synonim must be set)
                        if (isset($code1) || isset($code2)) {
							if (strlen($anotaciones['@CODES_SUB_PATH']) > 0 && strlen($anotaciones['@DESCRIPTIONS_SUB_PATH']) > 0 ) {
								$values[] = ['code' => $this->get($anotaciones['@CODES_SUB_PATH'],$item), 'display' => $this->get($anotaciones['@DESCRIPTIONS_SUB_PATH'],$item), 'active' => $item['active'], 'synonyms' => $item['pt']['term']];
							}
							else {
								$values[] = ['code' => $this->get('concept.conceptId',$item), 'display' => $this->get('concept.fsn.term',$item), 'active' => $item['active'], 'synonyms' => $item['pt']['term']];
							}
                        }
                    }


				
        }


		//Function to highlightthe substring matching in the complete result string


        $wordResults = array();
        $strippedSearchTerm = $this->skip_accents($search_term);
        if ($categoryData['search-type'] == 'full') {
            $searchWords = [$strippedSearchTerm];
        } else {
            if (strlen($strippedSearchTerm) > 0 && ($strippedSearchTerm[0] == "'" || $strippedSearchTerm[0] == '"')) {
                $searchWords = [substr($strippedSearchTerm, 1)];
            } else {
                $searchWords = explode(' ', $strippedSearchTerm);
            }
        }

        foreach ($values as $val) {
            if ($val['active'] === false) {
                // marked as inactive
                continue;
            }
            $code = $val['code'];
            if (in_array($code, $hideChoice)){
                // in hide choice list
                continue;
            }
            $desc = $val['display'];
            $synonyms = $val['synonyms'];
            $strippedDesc = $this->skip_accents($desc);
            $foundCount = 0;
            $minPos = 99;
            foreach ($searchWords as $word) {
                $pos = stripos($strippedDesc, $word);
                if ($pos !== FALSE) {
                    $foundCount++;
                    if ($pos < $minPos) {
                        $minPos = $pos;
                    }
                }
            }
            if ($foundCount > 0) {
                $wordResults[] = array('foundCount' => $foundCount, 'minPos' => $minPos, 'value' => $val);
            }
        }
        $fcColumn = array_column($wordResults, 'foundCount');
        $posColumn = array_column($wordResults, 'minPos');

        // sort on word match count then on closest to start of string
        array_multisort($fcColumn, SORT_DESC, $posColumn, SORT_ASC, $wordResults);
        $mresults = array_column($wordResults, 'value');

        $results = array();
        foreach ($mresults as $val) {
            // make sure result is escaped..
            $code = \REDCap::escapeHtml($val['code']);
            $desc = \REDCap::escapeHtml($val['display']);
            $results[$code] = $desc;
        }

        $result_limit = (is_numeric($result_limit) ? $result_limit : 20);

        if (count($results) < 1) {
            // add no results found
            $return_no_result = $categoryData['return-no-result'];
            if ($return_no_result) {
                $no_result_label = $categoryData['no-result-label'];
                $no_result_code = $categoryData['no-result-code'];
                $results[$no_result_code] = $no_result_label;
            }
        }

        // Return array of results
        return array_slice($results, 0, $result_limit, true);
    }


    /**
     *  A value and corresponding label
     */
    public function getLabelForValue($category, $value)
    {
        $systemCategories = $this->getSystemCategories();
        $projectCategories = $this->getProjectCategories();
        $categories = [];
        foreach ($systemCategories as $cat) {
            $categories[$cat['category']] = $cat;
        }
        foreach ($projectCategories as $cat) {
            $categories[$cat['category']] = $cat;
        }

        $values = array();
        $categoryData = $categories[$category];
        if ($categoryData) {

                $list = json_decode($rawValues, true);
                if (is_array($list)) {
                    foreach ($list as $item) {
                        if (isset($item['code']) and isset($item['display'])) {
                            $values[] = ['code' => $item['code'], 'display' => $item['display']];
                        }
                    }
                }
            /*}*/
            if (array_key_exists($value, $values)) {
                return $values[$value];
            }
        }
        return $value;
    }

    /*
     * Function taken from Blog posting :
     *
     *   Fonction PHP pour supprimer les accents - Murviel Info
     *   https://murviel-info-beziers.com/fonction-php-supprimer-accents/
     */
    function skip_accents($str, $charset = 'utf-8')
    {

        $str = htmlentities($str, ENT_NOQUOTES, $charset);

        $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
        $str = preg_replace('#&[^;]+;#', '', $str);

        return $str;
    }




    /*
     * 
     *
     *   Dot notation traslation for JSON paths
     *  
     */

    public function get($path, $array)
    {
        //$array = $this->values;

        if (!empty($path)) {
            $keys = explode('.', $path);
            foreach ($keys as $key) {
                if (isset($array[$key])) {
                    $array = $array[$key];
                } else {
                    return $default;
                }
            }
        }

        return $array;
    }
}





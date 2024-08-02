<?php

namespace TSBTAM\SnowstormTerminologyServerByTAGSExternalModule;

use ExternalModules\AbstractExternalModule;
use ExternalModules\ExternalModules;

class SnowstormTerminologyServerByTAGSExternalModule extends AbstractExternalModule implements \OntologyProvider
{

    public function __construct()
    {
        parent::__construct();
        // Tomamos OntologyManager
        $manager = \OntologyManager::getOntologyManager();
        $manager->addProvider($this);
	
		//Y aqui comienza la parte que toma las action tags y las mete como variables para llamar al servidor, metodo etc etc.... 
		if (isset($_GET['field'])){
            $field = $_GET['field'];
            if (isset($Proj->metadata[$_GET['field']])) {
                $annotations = $Proj->metadata[$field]['field_annotation'];
            }
            else if (isset($_GET['pid'])){
                $project_id = $_GET['pid'];
                $dd_array = \REDCap::getDataDictionary($project_id, 'array', false, array($field));
                $annotations = $dd_array[$field]['field_annotation'];

            }

			$tags = explode(';',$dd_array[$field]['field_annotation']);
			foreach ($tags as $tag) {
				list($k, $v) = explode('=', $tag);
				global $anotaciones;
				$anotaciones[trim($k)] = $v;
			}
		//Aqui termina la captura de action tags

        }
    }




    public function redcap_every_page_before_render($project_id)
    {
		 global $Proj;
    }


	//Tenemos la configuración en la variable settings, y en este punto paso a validarlas para que no haya errores

    public function validateSettings($settings)
    {
        $errors = '';
        // La categoría no tiene html tags o comillas
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
                // chequeamos que tenemos mensaje para el fallo en código y etiqueta
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
     * nombre de la ontologia en el desplegable de seleccion
     */
    public function getProviderName()
    {
        return 'Access SnowStorm Server by Action Tags';
    }


    /**
     * return the prefex used to denote ontologies provided by this provider.
     */
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
     * Devuelve un string que aparecerá en el online designer para seleccionar una ontología.
	 * Cuando se selecciona una ontología hace una llamada javascript a update_ontology_selection($service, $category)
     * REDCap incluye una función javascript que  <service>_ontology_changed(service, category) 
	 * será llamasa .Esta función actualiza cualquier elemento UI si s eproduce un matc y lo borrará si no lo hace
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
		global $anotaciones;


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
				
				
				//Aqui mucho ojo que se ha desactivado cualquier validacion SSL para que funcione el acceso a exponente que no tiene certificado
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
					//Solo conceptos activos
				}
				if (strlen($anotaciones['@SEMANTICTAG'])>0) {
					$parms['semanticTag']  = $anotaciones['@SEMANTICTAG'];
					//Semantic tag (categoría como hallazgp, procedimiento....)
				}
				if ($anotaciones['@GROUPBYCONCEPT']>0) {
					$parms['groupbyconcept'] = $anotaciones['@GROUPBYCONCEPT'];
					//Agrupar por conceptos (solo un resultado por concepto aunque haya varios descriptores que apunten al mismo)
				}
				if ($anotaciones['@OFFSET']>0) {
					$parms['offset'] = $anotaciones['@OFFSET'];
					//Offset en los resultados
				}
				if ($anotaciones['@LIMIT']>0) {
					$parms['limit'] = $anotaciones['@LIMIT'];
					$result_limit = $anotaciones['@LIMIT'];

					//Limite de resultados
				}
				elseif ($limit>0) {
					$parms['limit'] = $limit;
					$result_limit = $limit;
					//si no se establece limite por action tags toma el limite de la configuración para evitar sobrecargas
				}
				
				
				//Aqui hay que construir un arbol de decisiones de algun modo para solo poder lanzar consultas coherentes.
				//Tabien decidir desde configuración si se puede modificar por action tags la url, el método y si se quiere limitar algo.
				
				//El texto por el que se busca viebne desde RedCAP como $search_term y lo hemos modificado con rawurlencode para poder pasarlo con seguridad como  una URL
				
				$parms['term'] = $search_term_encoded;
				
				
				//Finalmente, con la URL, branch y método y las action tangs compongo una llamada al servidor de terminologias, que nos devolverá una cadena json

				$rawValues = file_get_contents($urlconsulta.http_build_query($parms), false, stream_context_create($arrContextOptions));

			

				//Hemos de descifrar el retorno json sabiendo su estructura. Desde action tags le hemos dicho en que punto del arbol JSON esta el código y la descripción

				$list = json_decode($rawValues, true);
               
                    foreach ($list['items'] as $item) {
						//lo primero, verifico que haya retornado un código
						$code1 = $this->get('concept.conceptId',$item);
						$code2 = $this->get($anotaciones['@CODES_SUB_PATH'],$item);

						//Y si tengo un código debo de tener el resto de datos (descripcion, activo o no y sinónimo)
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


		//Una vez que tengo los resultados en el array values pasa por una función que detecta la subcadena que ha hecho match para destacarla en caso de que la búsqueda no sea de tipo full


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

        if (count($results) < $result_limit) {
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
     *  Toma un valor y retorna la etqueta que corresponde
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
     * Function que devuelve el valor de un elemento de un array :
     *
     *   Basado en dot notation
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





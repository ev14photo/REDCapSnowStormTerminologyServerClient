# Módulo de acceso a Servidor de Terminologías SnowStorm
![logo](/logo.png "SnowStorm Terminooly Client")



## Configuración del módulo
Este módulo permite acceder a un servicio SnowStorm como servidor de terminologías configurando, en cada caso, todos los parámetros de las consultas, a traves de actiontags, desde el campo del formulario implicado.

Hay dos parámetros que pueden establecerse a niveles de configuración para cada una de los sietemas de acceso a la terminologia: La URL del servidor (@TERMSERVERURL) (https://snowstorm-training.snomedtools.org/snowstorm/snomed-ct/) y el servicio al que se desea acceder (@METHOD).

Tabién es posible establecer una limitación para no poder alterar esos parámetros desde action tags.
![logo](/config_server.png "Server config")

### Parámetros de la consulta

Dependiendo del servicio SnowStorm al que se consulte los parámetros son unos u otros. Se muestra a continuación el caso para buscar un término 












@TERMSERVERURL=https://snowstorm-training.snomedtools.org/snowstorm/snomed-ct;

@BRANCH=MAIN;

@METHOD=concepts;

@ACTIVEFILTER=true;

@DEFINITIONSTATUSFILTER=; *

@TERMACTIVE=true; *

@LANGUAGE; *

@PREFERREDIN; *

@ACCEPTABLEIN; *

@PREFERREDORACCEPTABLEIN; *

@ECL *; 

@STATEDECL; *

@CONCEPTIDS; *

@GROUPBYCONCEPT=false;

@SEARCHMODE=STANDARD; *

@SEMANTICTAG=disorder;

@LIMIT=50;

@OFFSET=150; 

@SEARCHAFTER; *

@ACCEPT-LANGUAGE; *

@INCLUDEDESCENDANTCOUNT; *

@FORM; *

Los campos indicados con * no se encuntran disponibles en el proceso de validación del programa.


## Registro de la URL a la que se hace la consulta

Como valor de configuración añadido, es posible establecer el action tag @SAVE_LOGS a true. En ese caso quedará registro en los logs del módulo (visibles desde el panel menú, sección External Modules --> View Logs) de la URL completa a la que se están haciendo las consultas.


![logo](/action_tags.png "SnowStorm Terminooly Client")

## Ruta del código y descripción en la respuesta

Del mismo modo, a traves de action tags, especificamos la ruta del resultado que tomaremos como código y como descripción, mediante notación con puntos dentro del elemento items de la estructura JSON devuelta dentro de "items"

@DESCRIPTIONS_SUB_PATH=concept.fsn.term;

@CODES_SUB_PATH=concept.conceptId;


## Soporte 

Este módulo se proporciona libremente y sin ninguna garantía. Los parámetros que se pueden establecer y su funcionamiento dependen del servicio SnowStorm al que se conecte, y es a su documantación a la que hay que referirse para consultar los sistemas de consulta y sus limitaciones.


## Proveedor de ontología

El proveedor de la ontología será una instancia de SnowStorm




# SnowStorm Terminology Server access module
![logo](/logo.png "SnowStorm Terminooly Client")



## Module configuration
This module allows access to a SnowStorm service as a terminology server by configuring, in each case, all the parameters of the queries, through actiontags, from the field of the form involved.

There are two parameters that can be set at configuration levels for each of the terminology access sevens: The server URL (@TERMSERVERURL) (https://snowstorm-training.snomedtools.org/snowstorm/snomed-ct/) and the service you wish to access (@METHOD).

It is also possible to establish a limitation so that these parameters cannot be altered from action tags.
![logo](/config_server.png "Server config")

### Query parameters

Depending on the SnowStorm service being consulted, the parameters are one or another. The case to search for a term is shown below 





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

@ECL; *

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

The fields indicated with * are not available in the program validation process.

## Record the SnowStorm URL to which the query is made

As an added configuration value, it is possible to set the @SAVE_LOGS action tag to true. In that case, there will be a record in the module logs (visible from the menu panel, External Modules --> View Logs section) of the complete URL to which the queries are being made.


![logo](/action_tags.png "SnowStorm Terminooly Client")

## Code path and description in response

Similarly, through action tags, we specify the path of the result that we will take as code and as a description, using dot notation within the items element of the JSON structure returned within "items"

@DESCRIPTIONS_SUB_PATH=concept.fsn.term;

@CODES_SUB_PATH=concept.conceptId;


## Medium 

This module is provided freely and without any warranty. The parameters that can be established and their operation depend on the SnowStorm service to which you connect, and it is its documentation that must be referred to to consult the query systems and their limitations.


## Ontology provider

The ontology provider will be a SnowStorm instance
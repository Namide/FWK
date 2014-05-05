<?php


include_once _SYSTEM_DIRECTORY.'plugin/admin/pages/includes/csvGenerator.php';

$csvName = _TEMP_DIRECTORY.'init-utf8.csv';

generateCsv($csvName);
header( 'Location: '._ROOT_URL.$csvName );

exit;
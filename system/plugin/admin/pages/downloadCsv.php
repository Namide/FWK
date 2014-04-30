<?php


include_once _SYSTEM_DIRECTORY.'plugin/admin/pages/includes/csvGenerator.php';

$csvName = 'init.csv';

generateCsv($csvName);
header( 'Location: '._ROOT_URL.$csvName );

exit;
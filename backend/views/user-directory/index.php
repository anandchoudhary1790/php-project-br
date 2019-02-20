<?php

use common\models\User;


\moonland\phpexcel\Excel::widget([
	'models' => $allModels,
	'mode' => 'export', //default value as 'export'
	'columns' => ['id','name',''], //without header working, because the header will be get label from attribute label. 
	'header' => ['column1' => 'Header Column 1','column2' => 'Header Column 2', 'column3' => 'Header Column 3'], 
]);

/*
$data = \moonland\phpexcel\Excel::widget([
		'mode' => 'import', 
		'fileName' => $fileName, 
		'setFirstRecordAsKeys' => true, // if you want to set the keys of record column with first record, if it not set, the header with use the alphabet column on excel. 
		'setIndexSheetByName' => true, // set this if your excel data with multiple worksheet, the index of array will be set with the sheet name. If this not set, the index will use numeric. 
		'getOnlySheet' => 'sheet1', // you can set this property if you want to get the specified sheet from the excel data with multiple worksheet.
	]);
*/

?>
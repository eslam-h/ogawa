<?php 

$query['pavmegamenu'][]  = "DELETE FROM `".DB_PREFIX ."setting` WHERE `code`='pavmegamenu' and `key` = 'pavmegamenu_module'";

$query['pavmegamenu'][]  = "DELETE FROM `".DB_PREFIX ."setting` WHERE `code`='pavmegamenu_params' and `key` = 'params'";
$query['pavmegamenu'][] =  " 
INSERT INTO `".DB_PREFIX ."setting` (`setting_id`, `store_id`, `code`, `key`, `value`, `serialized`) VALUES
(0, 0, 'pavmegamenu_params', 'pavmegamenu_params', '[{\"submenu\":1,\"subwidth\":850,\"id\":2,\"align\":\"aligned-left\",\"group\":0,\"cols\":3,\"rows\":[{\"cols\":[{\"widgets\":\"wid-19\",\"colwidth\":12}]},{\"cols\":[{\"widgets\":\"wid-20\",\"colwidth\":4},{\"widgets\":\"wid-21\",\"colwidth\":4},{\"widgets\":\"wid-22\",\"colwidth\":4}]}]},{\"id\":5,\"group\":0,\"cols\":1,\"subwidth\":1000,\"submenu\":1,\"align\":\"aligned-fullwidth\",\"rows\":[{\"cols\":[{\"widgets\":\"wid-10|wid-13\",\"colwidth\":3},{\"widgets\":\"wid-15\",\"colwidth\":3},{\"widgets\":\"wid-18\",\"colwidth\":6}]},{\"cols\":[{\"widgets\":\"wid-23\",\"colwidth\":12}]}]},{\"id\":23,\"group\":0,\"cols\":1,\"submenu\":1,\"align\":\"aligned-left\",\"rows\":[{\"cols\":[{\"type\":\"menu\",\"colwidth\":12}]}]},{\"id\":11,\"group\":0,\"cols\":1,\"submenu\":1,\"align\":\"aligned-left\",\"rows\":[{\"cols\":[{\"type\":\"menu\",\"colwidth\":12}]}]},{\"id\":12,\"group\":0,\"cols\":1,\"submenu\":1,\"align\":\"aligned-left\",\"rows\":[{\"cols\":[{\"type\":\"menu\",\"colwidth\":12}]}]}]', 0);

";

$query['pavblog'][] ="
INSERT INTO `".DB_PREFIX ."layout_route` (`layout_route_id`, `layout_id`, `store_id`, `route`) VALUES
(0, 14, 0, 'pavblog/%');
";
$query['pavblog'][] ="
INSERT INTO `".DB_PREFIX ."layout` (`layout_id`, `name`) VALUES
(0, 'Pav Blog');
";
?>
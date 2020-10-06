<?php
if(!isset($_GET['api'])) {
    return;
}


/*****************
** SETUP
*****************/
header('Content-Type: application/json');
$db = JFactory::getDbo();
$query = $db->getQuery(true);
$page = 1;
$nrows = 10;

/*****************
** SELECT
*****************/

$selectStmt = "id, catid, title, introtext, created AS created_date, images";
if(isset($_GET['command'])) {
  if ($_GET['command'] == 'count') {
    $selectStmt = "COUNT(*) as contagem";
  }
} 
$query->select($selectStmt);

/*****************
** FROM
*****************/
$query->from($db->quoteName('#__content'));

/*****************
** WHERE
*****************/
$whereClause = "state = '1'";
$query->where($whereClause);



function get_subcats($catID) {
  $categories = JCategories::getInstance('Content');
  $cat = $categories->get($catID);
  if (!$cat) return [];
  $children = $cat->getChildren();
  $children = array_map(function($c){return $c->id;}, $children);
  if (!$children) return [];
  return $children;
}

// Categories
if(isset($_GET['catid']) && $_GET['catid'] != 'undefined') {
  $cats = json_decode($_GET['catid']);
  $cats = array_map(intval, $cats);
  $allCats = array_merge([],$cats);
  foreach ($cats as $cat) {
    $allCats = array_merge($allCats, get_subcats($cat));
  }
  
  $whereClause = 'catid IN (' . implode(',', $allCats) . ')';
  $query->where($whereClause); 
}


//Imagem
if(isset($_GET['somenteImagem'])) {
    if ($_GET['somenteImagem'] == '1') {
        $query->where("images NOT LIKE '%image_intro\":\"\"%'");
    }
}

//Destaque
if(isset($_GET['destaque'])) {
    switch ($_GET['destaque']) {
        case '1':
            $query->where("featured = 0");
            break;
        case '2':
            $query->where("featured = 1");
            break;
    }
}


/*****************
** ORDER
*****************/
$ordemColuna = "created";
if(isset($_GET['ordem'])) {
  $ordemColuna = $db->quoteName($_GET['ordem']);
}
$ordemDirection = " DESC";
if(isset($_GET['ordem_direction'])) {
  if ($_GET['ordem_direction'] == "ASC") {
    $ordemDirection = " ASC";
  }
    
}

/*****************
** PAGINATION
*****************/
if(isset($_GET['page'])) {
    $page = (int)$_GET['page'];
}

if(isset($_GET['nrows'])) {
    $nrows = (int)$_GET['nrows'];
}
$query->setLimit($nrows, $nrows*($page-1));
$query->order($ordemColuna . $ordemDirection);

$result = $db->setQuery($query);
$articles = $result->loadObjectList();
echo json_encode($articles);
die;
?>

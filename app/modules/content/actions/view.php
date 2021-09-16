<?php
$Main->user->PagePrivacy();

$content=$ContentClass->GetContentByUrl($Main->GPC['content_url'],1,1);


if ($content==false or $content['content_status']==0) {
    $Main->error->PageNotFound();
}

$ContentClass->UpdateContentViews($content['content_id']);

if ($content['content_type']!='pages' and $content['content_type']!=$Main->GPC['content_type'] ) {
	$Main->error->PageNotFound();
}

$meta_title_last_part='';
if ($content['content_type']=='news') {
    $news_cat=$ContentClass->GetNewsCat($content['content_cat']);
    $meta_title_last_part=' - '.$news_cat['title'].' - Новости';
}

$meta_title=$content['content_title'].$meta_title_last_part;
if ($content['head_title']!='') {
    $meta_title=$content['head_title'].$meta_title_last_part;
}
$meta_desc='';
if ($content['content_short']) {
    $meta_desc = CutHeadText($content['content_short']);
}
elseif ($content['head_desc']) {
    $meta_desc = CutHeadText($content['head_desc']);
}
elseif ($content['content_text']) {
	$meta_desc = CutHeadText(strip_tags($content['content_text']));
}
if ($content['head_desc']!='') {
    $meta_desc=$content['head_desc'];
}

$breadcrumbs=array();

$breadcrumbs[]=array(
    'title'=>'Статьи',
    'link'=>BASE_URL.'/blog/'
);
$a_name = "Статьи";
$header_image = $Main->global_data["header_images"][$content["content_url"]];
if ($header_image){$header_image=$content["content_url"];}
    else{
    $header_image = '/blog/';
}
$Main->template->SetPageAttributes(
    array(
        'title'=>$meta_title,
        'keywords'=>$content['head_keywords'],
        'desc'=>$meta_desc,
        "header_image_url"=>$header_image
    ),
    array(
        'breadcrumbs'=>array(
            array(
                'title'=>'Статьи',
                'link'=>BASE_URL.'/blog/'
            ),
        ),
        'title'=>$content['content_title'],
        'content_type'=>$content['content_type'],
	    'date'=>$content['content_time'],
        'views'=>$content['content_views']
    )
);

$where_go = $Taxi->cities->getPlacesPublicSortByViews();
$prices = 0;

if ($content['content_template']) {
	$Main->template->DisplayCore('content/custom_pages/'.$content['content_template'],
		array(
		    'where_go'=>$where_go,
			'info'=>$content,
			'prices'=>$prices,
			'articles'=>$ContentClass->getLastArticles(),
		));
}
else {
	$Main->template->Display(
		array(

            'where_go'=>$where_go,
			'info'=>$content,
			'articles'=>$ContentClass->getLastArticles()
		));

}

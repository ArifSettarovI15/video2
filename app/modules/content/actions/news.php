<?php
$Main->user->PagePrivacy();
$Main->input->clean_array_gpc('r', array(
    'filters'=>TYPE_ARRAY
));


$page_name='Блог';
$meta_title='Блог';
if ($Main->GPC['content_type']=='news') {
    $page_name='Новости';
    $meta_title='Новости';
}

$last_b=$page_name;

$breadcrumbs=array();


    $breadcrumbs[] = array(
        'title' => 'Блог'
    );

$keywords='';
$desc='';

if ($Main->GPC['page']) {
    $keywords.=', cтраница '.$Main->GPC['page'];
    $desc.=', cтраница '.$Main->GPC['page'];
}

$Main->input->clean_array_gpc('r', array(
    'order' => TYPE_STR
));
if ($Main->GPC['content_type']=='news' or $Main->GPC['content_type']=='articles') {
    $cats = $ContentClass->GetNewsCats($Main->GPC['content_type']);
}
$Main->input->clean_array_gpc('r', ['content_cat'=>TYPE_UINT]);

if ($Main->GPC['content_type'] == 'blog'){
    $Main->GPC['content_type'] = 'articles';
}

$filter_options=array();
$filter_options['order']='date';
$filter_options['content_type']=$Main->GPC['content_type'];
$filter_options['not_type']='pages';
$filter_options['show_order']=true;
$filter_options['skip_date']=true;
$variables=$filter_options;

$variables['content_type']=$Main->GPC['content_type'];
$variables['cats']=$cats;
$variables['news_cat']=$news_cat;
$variables['type']='numbers';



$Paging =new ClassPaging($Main,555,true);
$Paging->template = 'frontend/components/paging/paging.twig';
$Paging->template1 = 'frontend/components/paging/paging.twig';
$Paging->template2 = 'frontend/components/paging/paging.twig';
$Paging->template3 = 'frontend/components/paging/paging.twig';
$Paging->data=$ContentClass->GetContentList($filter_options,$Paging->per_page,$Paging->sql_start);
$Paging->total=$ContentClass->GetContentListTotal($filter_options);



$Main->template->SetPageAttributes(
    array(
        'title'=>$meta_title,
        'keywords'=>$keywords,
        'desc'=>$desc
    ),
    array(
        'breadcrumbs'=>$breadcrumbs,
        'title'=>$page_name,
        'total'=>$Paging->total,
        'background'=>BASE_URL.'/assets/images/static/blog_bg.jpg',

    )
);
$Paging->Display('content/news_list_table.html.twig',$variables);



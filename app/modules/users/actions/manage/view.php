<?php
$Main->user->PagePrivacy('admin');

$user=$Main->user->GetUserById($Main->GPC['user_id']);
if ($user==false) {
	$Main->error->ObjectNotFound();
}
$profile=$Main->user->GetUserProfile($Main->GPC['user_id']);



if ($Main->GPC['action']=='del_user_vendor_value' ) {
	$Main->input->clean_array_gpc('r', array(
		'value' => TYPE_UINT
	));
	$array['status']=false;
	$array['text']='Ошибка';

	unset($profile['profile_discounts'][$Main->GPC['value']]);

	$profile_data=array(
		'profile_discounts'=>serialize($profile['profile_discounts'])
	);

	$Main->user->UpdateProfileData($user['user_id'],$profile_data);


	$array['status']=true;
	$array['text']='Обновлено';
	$array['html']=$Main->template->Render('backend/components/filter_values_list/vendors.twig',
		array(
			'list'=>$profile['profile_discounts']
		));



	$Main->template->DisplayJson($array);
	exit;
}
if ($Main->GPC['action']=='save_user_vendor_value' ) {
	$Main->input->clean_array_gpc('r', array(
		'value' => TYPE_UINT,
		'discount'=>TYPE_INT
	));
	$array['status']=false;
	$array['text']='Ошибка';

	$profile['profile_discounts'][$Main->GPC['value']]['value']=$Main->GPC['discount'];

	$profile_data=array(
		'profile_discounts'=>serialize($profile['profile_discounts'])
	);

	$Main->user->UpdateProfileData($user['user_id'],$profile_data);


	$array['status']=true;
	$array['text']='Обновлено';
	$array['html']=$Main->template->Render('backend/components/filter_values_list/vendors.twig',
		array(
			'list'=>$profile['profile_discounts']
		));



	$Main->template->DisplayJson($array);
	exit;
}
if ($Main->GPC['action']=='add_user_vendor_value' ) {
	$Main->input->clean_array_gpc('r', array(
		'value' => TYPE_UINT,
		'title'=>TYPE_STR,
		'discount'=>TYPE_INT
	));
	$array['status']=false;
	$array['text']='Ошибка';

	$profile['profile_discounts'][$Main->GPC['value']]=array(
		'v_name'=>$Main->GPC['title'],
		'v_id'=>$Main->GPC['value'],
		'value'=>$Main->GPC['discount']
	);


	$profile_data=array(
		'profile_discounts'=>serialize($profile['profile_discounts'])
	);


	$Main->user->UpdateProfileData($user['user_id'],$profile_data);


	$array['status']=true;
	$array['text']='Обновлено';
	$array['html']=$Main->template->Render('backend/components/filter_values_list/vendors.twig',
		array(
			'list'=>$profile['profile_discounts']
		));



	$Main->template->DisplayJson($array);
	exit;
}


if ($Main->GPC['action']=='update_info') {
	$error='';
	$Main->input->clean_array_gpc('r', array(
		'profile_discount'=>TYPE_UINT,
		'profile_credit'=>TYPE_UINT,
		'profile_timeout'=>TYPE_UINT,
		'profile_price_id'=>TYPE_UINT
	));


	$profile_data=array(
		'profile_discount'=>$Main->GPC['profile_discount'],
		'profile_credit'=>$Main->GPC['profile_credit'],
		'profile_timeout'=>$Main->GPC['profile_timeout'],
		'profile_price_id'=>$Main->GPC['profile_price_id']
	);

	$Main->user->UpdateProfileData($Main->GPC['user_id'],$profile_data);

	$array['text']='Профиль обновлен';
	$array['status']=true;

	$Main->template->DisplayJson($array);
}


$ShopClass->vendors->CreateModel();
$ShopClass->vendors->model->columns_where->getStatus()->setValue(true);
$ShopClass->vendors->model->setSelectField($ShopClass->vendors->model->getTableName().'.*');
$ShopClass->vendors->model->setOrderByWithColumn($ShopClass->vendors->model->columns_where->getTitle()->getName());
$brands=$ShopClass->vendors->GetList();

$Main->template->Display(array(
	'user'=>$user,
	'profile'=>$profile,
	'brands'=>$brands
));

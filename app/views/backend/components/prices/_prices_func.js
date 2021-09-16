function setPriceValue(obj) {
    var data={};
    data['action']='set_price_value';
    data['start']=obj.attr('data-start');
    data['end']=obj.attr('data-end');
    data['class']=obj.attr('data-class');
    data['value']=obj.val();

    SendAjaxRequest(
        {
            'data':data
        }
    );
}

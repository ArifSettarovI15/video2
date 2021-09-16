$( document ).on( 'change', '.b-options__checkbox', function() {
  var data={};
  data['action']='set_checkbox';
  data['value']=$(this).prop('checked');
  data['option_id']=$(this).val();

  var options={};
  SendAjaxRequest(
    {
      'data': data,
      'options':options
    }
  );
});

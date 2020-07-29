{* template block that contains the custom fields for wordmail merge - Civi Version 4.7 and higher *}
<table id="is_mailmerge_field">
  <tbody>
    <tr>
      <td class="lablel-left">{$form.is_mail_merge.label}</td>
      <td>{$form.is_mail_merge.html}</td>  
    </tr>
  </tbody>
</table>
{* reposition the above block after #someOtherBlock *}
{literal}
<script type="text/javascript">
  // reposition wordmailmerge field
  cj('#is_mailmerge_field').insertAfter(cj('#file_id').parent().parent());

  // show or hide wordmailmerge field on change of template file type
  var selector = cj("input[id$='_file_type']").attr('type') == 'radio' ? "input[id$='_file_type']:checked" : "input[id$='_file_type']";
  showHideWordmailmerge(cj(selector).val());
  cj("input[id$='_file_type']").on('click', function(){
    showHideWordmailmerge(this.value);
  });
  function showHideWordmailmerge(type) {
    var show = (type == 1) ? false : true;
    cj("#is_mailmerge_field").toggle(!show);
  }
</script>
{/literal}

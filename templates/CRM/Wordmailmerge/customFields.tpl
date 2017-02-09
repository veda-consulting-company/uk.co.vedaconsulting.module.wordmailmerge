{* template block that contains the custom fields for wordmail merge - Civi Version 4.7 and higher *}
  <table id="is_wordmailmerge_field">
    <tbody>
      <tr>
        <td class="label-left">{$form.is_wordmailmerge.label}</td>
        <td>{$form.is_wordmailmerge.html}</td>
      </tr>
    </tbody>
  </table>
{* reposition the above block after #someOtherBlock *}
{literal}
<script type="text/javascript">
  // reposition wordmailmerge field
  cj('#is_wordmailmerge_field').insertAfter(cj('#file_id').parent().parent());

  // show or hide wordmailmerge field on change of template file type
  var selector = cj("input[id$='_file_type']").attr('type') == 'radio' ? "input[id$='_file_type']:checked" : "input[id$='_file_type']";
  showHideWordmailmerge(cj(selector).val());
  cj("input[id$='_file_type']").on('click', function(){
    showHideWordmailmerge(this.value);
  });
  function showHideWordmailmerge(type) {
    var show = (type == 1) ? false : true;
    cj("#is_wordmailmerge_field").toggle(!show);
  }
</script>
{/literal}

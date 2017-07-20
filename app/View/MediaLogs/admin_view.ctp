<?php 
// File: app/View/Media/view.ctp


$page_options = array(
	$this->Html->link(__('Show Just Differences'), '#', array('id' => 'show_diff')),
	$this->Html->link(__('Show All'), '#', array('id' => 'show_all')),
);

$details_left = array();
//$details_left[] = array('name' => __('Media ID'), 'value' => $this->Html->link($media_log['Media']['id'], array('controller' => 'media', 'action' => 'view', $media_log['Media']['id'], 'admin' => false)) );
$class = ($media_log['MediaLog']['serial_number'] == $media['Media']['serial_number']?'same':'diff'); 
$details_left[] = array('class' => $class, 'name' => __('Serial Number'), 'value' => $media_log['MediaLog']['serial_number']);
$class = ($media_log['SanitizeStatus']['order'] == $media['SanitizeStatus']['order']?'same':'diff'); 
$details_left[] = array('class' => $class, 'name' => __('%s Status', __('Sanitize')), 'value' => $media_log['SanitizeStatus']['name']);


$class = ($media_log['MediaLog']['asset_tag'] == $media['Media']['asset_tag']?'same':'diff'); 
$details_left[] = array('class' => $class, 'name' => __('Associated Asset Tag'), 'value' => $media_log['MediaLog']['asset_tag']);
$class = ($media_log['MediaLog']['example_ticket'] == $media['Media']['example_ticket']?'same':'diff'); 
$details_left[] = array('class' => $class, 'name' => __('Example Ticket'), 'value' => $media_log['MediaLog']['example_ticket']);
$class = ($media_log['MediaLog']['tickets'] == $media['Media']['tickets']?'same':'diff'); 
$details_left[] = array('class' => $class, 'name' => __('Related Tickets'), 'value' => $media_log['MediaLog']['tickets']);
$class = ($media_log['MediaLog']['filename'] == $media['Media']['filename']?'same':'diff'); 
$details_left[] = array('class' => $class, 'name' => __('Related Attachment'), 'value' => $this->Html->link($media_log['MediaLog']['filename'], array('controller' => 'media', 'action' => 'download', $media_log['Media']['id'], 0, $media_log['MediaLog']['filename'], 'admin' => false)) );
$class = ($media_log['MediaLog']['form'] == $media['Media']['form']?'same':'diff'); 
$details_left[] = array('class' => $class, 'name' => __('Form'), 'value' => $media_log['MediaLog']['form']);
$class = ($media_log['MediaLog']['size'] == $media['Media']['size']?'same':'diff'); 
$details_left[] = array('class' => $class, 'name' => __('Size'), 'value' => $media_log['MediaLog']['size']);
$class = ($media_log['MediaLog']['manufacturer'] == $media['Media']['manufacturer']?'same':'diff'); 
$details_left[] = array('class' => $class, 'name' => __('Manufacturer'), 'value' => $media_log['MediaLog']['manufacturer']);
$class = ($media_log['MediaLog']['media_type_id'] == $media['Media']['media_type_id']?'same':'diff'); 
$details_left[] = array('class' => $class, 'name' => __('Media Type'), 'value' => $media_log['MediaType']['name']);

$details_right = array();

$tmp = array('User' => $media_log['MediaLogAddedUser']);
$MediaLogAddedUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  

$MediaLogModifiedUser = '';
if($media_log['MediaLogModifiedUser']['id'])
{
	$tmp = array('User' => $media_log['MediaLogModifiedUser']);
	$MediaLogModifiedUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
}
$MediaLogOpenedUser = '';
if($media_log['MediaLogOpenedUser']['id'])
{
	$tmp = array('User' => $media_log['MediaLogOpenedUser']);
	$MediaLogOpenedUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
}
$MediaLogSanitizedUser = '';
if($media_log['MediaLogSanitizedUser']['id'])
{
	$tmp = array('User' => $media_log['MediaLogSanitizedUser']);
	$MediaLogSanitizedUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
}
$MediaLogSignedUser = '';
if($media_log['MediaLogSignedUser']['id'])
{
	$tmp = array('User' => $media_log['MediaLogSignedUser']);
	$MediaLogSignedUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
}
$MediaLogTestedUser = '';
if($media_log['MediaLogTestedUser']['id'])
{
	$tmp = array('User' => $media_log['MediaLogTestedUser']);
	$MediaLogTestedUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
}
$MediaLogRepurposedUser = '';
if($media_log['MediaLogRepurposedUser']['id'])
{
	$tmp = array('User' => $media_log['MediaLogRepurposedUser']);
	$MediaLogRepurposedUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
}
$class = ($media_log['MediaLog']['obtained'] == $media['Media']['obtained']?'same':'diff'); 
$details_right[] = array('class' => $class, 'name' => __('Obtained'), 'value' => $this->Wrap->niceTime($media_log['MediaLog']['obtained']));
$class = ($media_log['MediaLog']['created'] == $media['Media']['created']?'same':'diff'); 
$details_right[] = array('class' => $class, 'name' => __('Created'), 'value' => $this->Wrap->niceTime($media_log['MediaLog']['created']));
$class = ($media_log['MediaLog']['added_user_id'] == $media['Media']['added_user_id']?'same':'diff'); 
$details_right[] = array('class' => $class, 'name' => __('Created By'), 'value' => $MediaLogAddedUser);
$class = ($media_log['MediaLog']['modified'] == $media['Media']['modified']?'same':'diff'); 
$details_right[] = array('class' => $class, 'name' => __('Last Updated'), 'value' => $this->Wrap->niceTime($media_log['MediaLog']['modified']));
$class = ($media_log['MediaLog']['modified_user_id'] == $media['Media']['modified_user_id']?'same':'diff'); 
$details_right[] = array('class' => $class, 'name' => __('Last Updated By'), 'value' => $MediaLogModifiedUser);
$class = ($media_log['MediaLog']['opened_date'] == $media['Media']['opened_date']?'same':'diff'); 
$details_right[] = array('class' => $class, 'name' => __('Opened'), 'value' => $this->Wrap->niceTime($media_log['MediaLog']['opened_date']));
$class = ($media_log['MediaLog']['opened_user_id'] == $media['Media']['opened_user_id']?'same':'diff'); 
$details_right[] = array('class' => $class, 'name' => __('Last Opened By'), 'value' => $MediaLogOpenedUser);
$class = ($media_log['MediaLog']['sanitized_date'] == $media['Media']['sanitized_date']?'same':'diff'); 
$details_right[] = array('class' => $class, 'name' => __('Last Sanitized'), 'value' => $this->Wrap->niceTime($media_log['MediaLog']['sanitized_date']));

$class = ($media_log['MediaLog']['sanitized_user_id'] == $media['Media']['sanitized_user_id']?'same':'diff'); 
$details_right[] = array('class' => $class, 'name' => __('Last Sanitized By'), 'value' => $MediaLogSanitizedUser);

$class = ($media_log['MediaLog']['signed_date'] == $media['Media']['signed_date']?'same':'diff'); 
$details_right[] = array('class' => $class, 'name' => __('Last Signed'), 'value' => $this->Wrap->niceTime($media_log['MediaLog']['signed_date']));

$class = ($media_log['MediaLog']['signed_user_id'] == $media['Media']['signed_user_id']?'same':'diff'); 
$details_right[] = array('class' => $class, 'name' => __('Last Signed By'), 'value' => $MediaLogSignedUser);

$class = ($media_log['MediaLog']['tested_date'] == $media['Media']['tested_date']?'same':'diff'); 
$details_right[] = array('class' => $class, 'name' => __('Last Tested'), 'value' => $this->Wrap->niceTime($media_log['MediaLog']['tested_date']));

$class = ($media_log['MediaLog']['tested_user_id'] == $media['Media']['tested_user_id']?'same':'diff'); 
$details_right[] = array('class' => $class, 'name' => __('Last Tested By'), 'value' => $MediaLogTestedUser);

$class = ($media_log['MediaLog']['test_status_id'] == $media['Media']['test_status_id']?'same':'diff'); 
$details_right[] = array('class' => $class, 'name' => __('Last Test Results'), 'value' => $media_log['TestStatus']['name']);

$class = ($media_log['MediaLog']['test_notes'] == $media['Media']['test_notes']?'same':'diff'); 
$details_right[] = array('class' => $class, 'name' => __('Last Test Notes'), 'value' => $media_log['MediaLog']['test_notes']);

$class = ($media_log['MediaLog']['repurposed_date'] == $media['Media']['repurposed_date']?'same':'diff'); 
$details_right[] = array('class' => $class, 'name' => __('Last Repurposed'), 'value' => $this->Wrap->niceTime($media_log['MediaLog']['repurposed_date']));

$class = ($media_log['MediaLog']['repurposed_user_id'] == $media['Media']['repurposed_user_id']?'same':'diff'); 
$details_right[] = array('class' => $class, 'name' => __('Last Repurposed By'), 'value' => $MediaLogRepurposedUser);

$class = ($media_log['MediaLog']['repurpose_status_id'] == $media['Media']['repurpose_status_id']?'same':'diff'); 
$details_right[] = array('class' => $class, 'name' => __('Last Repurposed To'), 'value' => $media_log['RepurposeStatus']['name']);

$class = ($media_log['MediaLog']['tags'] == $media['Media']['tags']?'same':'diff'); 
$details_right[] = array('class' => $class, 'name' => __('Tags'), 'value' => $media_log['MediaLog']['tags']);

$class = ($media_log['MediaLog']['details'] == $media['Media']['details']?'same':'diff'); 
$details_right[] = array('class' => $class, 'name' => __('Notes'), 'value' => $this->Wrap->descView($media_log['MediaLog']['details']));

$stats = array();
		
$tabs = array();
echo $this->element('Utilities.page_compare', array(
	'page_title' => __('%s Details', __('Media Log')),
	'page_subtitle' => __('Selecting the defferences button will show you the OLD values from BEFORE the record was changed.'),
	'page_options' => $page_options,
	'details_left_title' => ' ',
	'details_left' => $details_left,
	'details_right_title' => ' ',
	'details_right' => $details_right,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));
?>

<script type="text/javascript">

var submitResults = false;

$(document).ready(function()
{
	$('#show_all').hide();
	
	$('#show_diff').bind("click", function (event) {
		$('.same').hide();
		$('#show_diff').hide();
		$('#show_all').show();
		return false;
	});
	
	$('#show_all').bind("click", function (event) {
		$('.same').show();
		$('#show_diff').show();
		$('#show_all').hide();
		return false;
	});
});//ready 

</script>

<script type="text/javascript">
$(function() {
	$('.back').click(function() {
		var url = '<?php echo fuel_url('bill_details');?>';
		window.location.replace(url);
	});
});
</script>

<style type="text/css">
.back:hover {
	text-decoration: underline;
	cursor:pointer;
}
</style>
<link rel="stylesheet" type="text/css" href="<?=$this->asset->css_path('print-style', 'search')?>">
<div id="main_top_panel">
	<h2 style="float: left;" class="ico ico_azure_storage"><?=lang('search_result')?></h2>
	<h2 style="float: right;color: black;">
		<span class="back" color="color:black !important;">&lt;&lt; Back to bill details</span>
	</h2>
</div>
<div id="innerpanel"> 
	<div style="float:right" class="search-actions"> 
		<button type="button"> 
		<a style="color:black;"  href="mailto:someone@example.com?Subject=Hello%20again" target="_top">Send Mail</a></button>
		<input style="color:black;" type="button" value="Print" onClick=" window.print(); return false">
	</div>
	<div class="noScreen"><center><b>INTERNATIONAL STEEL PROCESSORS<br>NO.43,KANNIAMMANPET VILLAGE, SURVEY NO145/6C ANDRAKUPPAM POST, KADAPAKKAM, CHENNAI-600 103.
		<br>Email: ispchennai@gmail.com</b></center>
		<br><br>
	</div>
<script type="text/javascript">
savelistform({
	//debug: true,
	addurl:"{{ request.getURI() }}",
	listurl:"{{ url('category/index') }}",
	name : '{{ jumpButton }}'
});
function advanced(){
	$('.advanced').toggle();
}
</script>
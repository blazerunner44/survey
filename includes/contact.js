// JavaScript Document
$(document).ready(function() {
	$('form').hide();
    $('#button').click(function() {
		$('#contact_pre').slideToggle('slow');
		$('form').slideToggle('slow').delay(600);
	});
	$('form').submit(function() {
		$('form').slideToggle('slow');
	});
	$('#submit').click(function() {
		$('#submit').toggle('slow');
		$('#load').toggle(show);
	});
});
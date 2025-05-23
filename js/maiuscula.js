jQuery(document).ready(function($) {
 // Chamada da funcao upperText(); ao carregar a pagina
 upperText();
 // Funcao que faz o texto ficar em uppercase
 function upperText() {
// Para tratar o colar
 $("input").bind('paste', function(e) {
 var el = $(this);
 setTimeout(function() {
 var text = $(el).val();
 el.val(text.toUpperCase());
 }, 100);
 });
 
// Para tratar quando é digitado
 $("input").keypress(function() {
 var el = $(this);
 setTimeout(function() {
 var text = $(el).val();
 el.val(text.toUpperCase());
 }, 100);
 });
 }
 });
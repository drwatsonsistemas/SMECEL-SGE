$(document).ready(function(){
  $('.date').mask('00/00/0000');
  $('.certidao').mask('000000 00 00 0000 0 00000 000 0000000 00');
  $('.sus').mask('000 0000 0000 0000');
  $('.pis').mask('000.00000.00-00');
  $('.time').mask('00:00:00');
  $('.hora').mask('00:00');
  $('.date_time').mask('00/00/0000 00:00:00');
  $('.cep').mask('00000-000');
  $('.inep').mask('00000000');
  $('.ano').mask('0000');
  $('.phone').mask('0000-0000');
  $('.phone_with_ddd').mask('(00) 0000-0000');
  $('.phone_us').mask('(000) 000-0000');
  $('.mixed').mask('AAA 000-S0S');
  $('.cpf').mask('000.000.000-00', {reverse: true});
  $('.cnpj').mask('00.000.000/0000-00');
  $('.money').mask('000.000.000.000.000,00', {reverse: true});
  $('.money2').mask("#.##0,00", {reverse: true});
  $('.ip_address').mask('099.099.099.099');
  $('.percent').mask('##0,00%', {reverse: true});
  $('.clear-if-not-match').mask("00/00/0000", {clearIfNotMatch: true});
  $('.placeholder').mask("00/00/0000", {placeholder: "__/__/____"});
  $('.placeholdercpf').mask("000.000.000-00", {placeholder: "___.___.___-__"});

$('.celular').focusout(function(){
    var phone, element;
    element = $(this);
    element.unmask();
    phone = element.val().replace(/\D/g, '');
    if(phone.length > 10) {
        element.mask("(99) 99999-9999");
    } else {
        element.mask("(99) 9999-99999");
    }
}).trigger('focusout');

});
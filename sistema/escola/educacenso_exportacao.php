<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>

<?php include "fnc/session.php"; ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

include "usuLogado.php";
include "fnc/anoLetivo.php";


//$qryEscola = "escola_id = '$row_UsuLogado[usu_escola]'";
$qryEscola = "escola_id_sec = '$row_UsuLogado[usu_sec]'";

//CAMPO 00
//CAMPO 10
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "
SELECT * FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec
LEFT JOIN smc_municipio_distritos ON distrito_cod_distrito = escola_cod_distrito
WHERE $qryEscola AND escola_ue = 1 AND escola_situacao = 1";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);
?>


<?php

function limpa($valor){
 $valor = trim($valor);
 $valor = str_replace(array('.','-','/'), "", $valor);
 return $valor;
}

function retiraAcentos($string) {
  $string = str_replace('Á', 'A', $string);
  $string = str_replace('À', 'A', $string);
  $string = str_replace('Â', 'A', $string);
  $string = str_replace('Ã', 'A', $string);
  $string = str_replace('É', 'E', $string);
  $string = str_replace('È', 'E', $string);
  $string = str_replace('Ê', 'E', $string);
  $string = str_replace('Í', 'I', $string);
  $string = str_replace('Ì', 'I', $string);
  $string = str_replace('Î', 'I', $string);
  $string = str_replace('Ó', 'O', $string);
  $string = str_replace('Ò', 'O', $string);
  $string = str_replace('Õ', 'O', $string);
  $string = str_replace('Ô', 'O', $string);
  $string = str_replace('Ú', 'U', $string);
  $string = str_replace('Ù', 'U', $string);
  $string = str_replace('Û', 'U', $string);
  $string = str_replace('Ü', 'U', $string);
  $string = str_replace('Ç', 'C', $string);
  $string = str_replace('\'', '', $string);
  $string = str_replace('  ', ' ', $string);
  $string = str_replace('-', '', $string);
  $string = str_replace('"', '', $string);
  $string = str_replace('\'', '', $string);
  $string = str_replace('_', '', $string);
  $string = str_replace('.', '', $string);
  $string = str_replace(',', '', $string);
  $string = str_replace('/', '', $string);
  $string = trim($string);
  return $string;
}

function localizacao($zona) {
  if ($zona == "U") {
    return "1";
  } else if ($zona == "R") {
    return "2";
  } else {
    return "1";
  }
}

function exibeZero($valor) {
  if ($valor=="") {
    return "0";
  } else {
    return $valor;
  }
}

function limpaCPF($cpf) {

  if ($cpf<>"") {

  $cpf = preg_replace('/[^0-9]/is', '', $cpf);
  $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
  return $cpf;

  } else {

    return "";

  }

}


function corRaca ($valor) {

  switch ($valor) {
    case 1:
        return "1";
        break;
    case 2:
        return "1";
        break;
    case 3:
        return "1";
        break;
    case 4:
        return "1";
        break;
    case 5:
        return "1";
        break;
    case 6:
        return "0";
        break;
    default:
    return "0";
    break;

  }

}

?>

<!--
DADOS DO CENSO<br>
00|29321140|1|06/03/2023|28/12/2023|COLEGIO MUNICIPAL OTHONIEL FERREIRA DOS SANTOS|45850000|2915304|291530405|PRACA DOUTOR PINTO DANTAS|72|ESCOLA|CENTRO|73|32892109|32892140|COLEGIO_OTHONIEL@HOTMAIL.COM|00027|1|7|3|1|0|0|0|0|0|0|0|0|0||0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|||1|||1|0||
10|29321140|1|0|0|0|0|0|1||||||||1|1|0|0|0|0|1|0|0|0|1|0|0|0|1|0|0|0|0|0|0|0|1|1|0|0|1|0|0|1|0|1|1|1|0|0|0|0|0|0|1|1|0|1|0|1|0|0|0|0|0|0|0|1|0|1|0|1|0|0|0|0|0|0|0|1|0|0|0|0|10|3|10|10|0|1|0|0|1|0|0||3|3|||12|||1|1|0|0|0|0|0|1|0|1|0|4|8|||||||5|1||||1||||1|0|0|0|1|0|1|0|0|1|0|0|0|0|0|0|0|0||||0|0|0|0|0|0|0|1|1|1|0|0|1|0|1|0|1

<br><br>DADOS DO SISTEMA<br>   
00|29321280|1|06/03/2023|08/12/2023|ESCOLA MUNICIPAL ERNESTINA ABRAO|45850000|2915304|291530405|AVENIDA 13 DE MAIO|S/N||CENTRO|73|988621980|32892109|ERNESTINA@SMECEL.COM.BR|00027|1|7|3|1|0|0|0|0|0|0|0|0|0||0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|||1||||0||
00|29321220|1|06/03/2023|08/12/2023|GRUPO ESCOLAR JUTAHY JUNIOR|45850000|2915304|291530405|PRACA ALVINO CARDOSO|01|CENTRO|UNIAO BAIANA|73|988759383|32895002|JUTAHY@SMECEL.COM.BR|00027|1|7|3|1|0|0|0|0|0|0|0|0|0||0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|||1|||1|0||
00|29321140|1|06/03/2023|28/12/2023|COLEGIO MUNICIPAL OTHONIEL FERREIRA DOS SANTOS|45850000|2915304|291530405|PRACA DOUTOR PINTO DANTAS|72|ESCOLA|CENTRO|73|32892109|32892140|COLEGIO_OTHONIEL@HOTMAIL.COM|00027|1|7|3|1|0|0|0|0|0|0|0|0|0||0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|||1|||1|0||
-->   

    <?php 
    
    do { 

        //LISTAR TUMAS DA ESCOLA
        //CAMPO 20 
        mysql_select_db($database_SmecelNovo, $SmecelNovo);
        $query_TurmasListar = "
        SELECT * FROM smc_turma 
        WHERE turma_tipo_atendimento IN ('1','2','3') AND turma_id_escola = '$row_EscolaLogada[escola_id]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' 
        ORDER BY turma_id ASC";
        $TurmasListar = mysql_query($query_TurmasListar, $SmecelNovo) or die (mysql_error());
        $row_TurmasListar = mysql_fetch_assoc($TurmasListar);
        $totalRows_TurmasListar = mysql_num_rows($TurmasListar);

        //LISTAR DIRETOR(A) DA ESCOLA
        //CAMPO 30 - GESTORES
        mysql_select_db($database_SmecelNovo, $SmecelNovo);
        $query_Gestor = "
        SELECT * FROM smc_vinculo
        INNER JOIN smc_func ON func_id = vinculo_id_funcionario
        INNER JOIN smc_funcao ON funcao_id = vinculo_id_funcao 
        WHERE funcao_gestor_escolar = 'S' AND vinculo_id_escola = '$row_EscolaLogada[escola_id]' 
        ";
        $Gestor = mysql_query($query_Gestor, $SmecelNovo) or die (mysql_error());
        $row_Gestor = mysql_fetch_assoc($Gestor);
        $totalRows_Gestor = mysql_num_rows($Gestor);

        //LISTAR DOCENTES DA ESCOLA
        //CAMPO 30 - PROFESSORES
        mysql_select_db($database_SmecelNovo, $SmecelNovo);
        $query_Professor = "
        SELECT * FROM smc_vinculo
        INNER JOIN smc_func ON func_id = vinculo_id_funcionario
        INNER JOIN smc_funcao ON funcao_id = vinculo_id_funcao 
        WHERE funcao_docencia = 'S' AND vinculo_id_escola = '$row_EscolaLogada[escola_id]' 
        ";
        $Professor = mysql_query($query_Professor, $SmecelNovo) or die (mysql_error());
        $row_Professor = mysql_fetch_assoc($Professor);
        $totalRows_Professor = mysql_num_rows($Professor);
        
        //LISTAR ALUNOS(AS) DA ESCOLA
        //CAMPO 30 - ALUNOS
        mysql_select_db($database_SmecelNovo, $SmecelNovo);
        $query_ExibirAlunosVinculados = "
        SELECT * FROM smc_vinculo_aluno 
        INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
        WHERE vinculo_aluno_id_escola = '$row_EscolaLogada[escola_id]' AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
        ORDER BY aluno_nome ASC";
        $ExibirAlunosVinculados = mysql_query($query_ExibirAlunosVinculados, $SmecelNovo) or die(mysql_error());
        $row_ExibirAlunosVinculados = mysql_fetch_assoc($ExibirAlunosVinculados);
        $totalRows_ExibirAlunosVinculados = mysql_num_rows($ExibirAlunosVinculados);

        //LISTAR VÍNCULO DO DIRETOR(A) DA ESCOLA
        //CAMPO 40 - GESTORES
        mysql_select_db($database_SmecelNovo, $SmecelNovo);
        $query_GestorVinculo = "
        SELECT * FROM smc_vinculo
        INNER JOIN smc_func ON func_id = vinculo_id_funcionario
        INNER JOIN smc_funcao ON funcao_id = vinculo_id_funcao 
        WHERE funcao_gestor_escolar = 'S' AND vinculo_id_escola = '$row_EscolaLogada[escola_id]' 
        ";
        $GestorVinculo = mysql_query($query_GestorVinculo, $SmecelNovo) or die (mysql_error());
        $row_GestorVinculo = mysql_fetch_assoc($GestorVinculo);
        $totalRows_GestorVinculo = mysql_num_rows($GestorVinculo);


        //LISTAR VÍNCULO DE PROFISSIONAIS ESCOLARES
        //CAMPO 50 - PROFESSORES
        mysql_select_db($database_SmecelNovo, $SmecelNovo);
        $query_ProfessorVinculo = "
        SELECT * FROM smc_vinculo
        INNER JOIN smc_func ON func_id = vinculo_id_funcionario
        INNER JOIN smc_funcao ON funcao_id = vinculo_id_funcao 
        WHERE funcao_docencia = 'S' AND vinculo_id_escola = '$row_EscolaLogada[escola_id]' 
        ";
        $ProfessorVinculo = mysql_query($query_ProfessorVinculo, $SmecelNovo) or die (mysql_error());
        $row_ProfessorVinculo = mysql_fetch_assoc($ProfessorVinculo);
        $totalRows_ProfessorVinculo = mysql_num_rows($ProfessorVinculo);

        //LISTAR VÍNCULO DE ALUNOS(AS) DA ESCOLA
        //CAMPO 60 - ALUNOS
        mysql_select_db($database_SmecelNovo, $SmecelNovo);
        $query_ExibirAlunosVinculadosVinculo = "
        SELECT * FROM smc_vinculo_aluno 
        INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
        WHERE vinculo_aluno_id_escola = '$row_EscolaLogada[escola_id]' AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
        ORDER BY aluno_nome ASC";
        $ExibirAlunosVinculadosVinculo = mysql_query($query_ExibirAlunosVinculadosVinculo, $SmecelNovo) or die(mysql_error());
        $row_ExibirAlunosVinculadosVinculo = mysql_fetch_assoc($ExibirAlunosVinculadosVinculo);
        $totalRows_ExibirAlunosVinculadosVinculo = mysql_num_rows($ExibirAlunosVinculadosVinculo);




        //************************************** 
        //**** INÍCIO DE EXIBIÇÃO EM TELA ******
        //**************************************
        //CAMPOS 00
        echo "<h3>REGISTRO 00 - IDENTIFICAÇÃO DA ESCOLA</h3>";

        echo "00|"; // 01 - Tipo de registro
        echo $row_EscolaLogada['escola_inep']."|"; // 02 - Código de escola - Inep
        echo $row_EscolaLogada['escola_situacao']."|"; // 03 - Situação de funcionamento
        echo date("d/m/Y", strtotime($row_AnoLetivo['ano_letivo_inicio']))."|"; // 04 - Data de início do ano letivo
        echo date("d/m/Y", strtotime($row_AnoLetivo['ano_letivo_fim']))."|"; // 05 - Data de término do ano letivo
        echo retiraAcentos($row_EscolaLogada['escola_nome'])."|"; // 06 - Nome da escola
        echo limpa($row_EscolaLogada['escola_cep'])."|"; // 07 - CEP
        echo $row_EscolaLogada['sec_ibge_municipio']."|"; // 08 - Município
        echo $row_EscolaLogada['distrito_cod_importacao']."|"; // 09 - Distrito
        echo retiraAcentos($row_EscolaLogada['escola_endereco'])."|"; // 10 - Endereço
        echo retiraAcentos($row_EscolaLogada['escola_num'])."|"; // 11 - Número
        echo retiraAcentos($row_EscolaLogada['escola_complemento'])."|"; // 12 - Complemento
        echo retiraAcentos($row_EscolaLogada['escola_bairro'])."|"; // 13 - Bairro
        echo substr($row_EscolaLogada['escola_telefone1'],1,2)."|"; // 14 - DDD
        echo substr(limpa($row_EscolaLogada['escola_telefone1']),5,11)."|"; // 15 - Telefone
        echo substr(limpa($row_EscolaLogada['escola_telefone2']),5,11)."|"; // 16 - Outro telefone de contato
        echo strtoupper($row_EscolaLogada['escola_email'])."|"; // 17 - Endereço eletrônico (e-mail) da escola
        echo $row_EscolaLogada['sec_nre']."|"; // 18 - Código do órgão regional de ensino
        echo localizacao($row_EscolaLogada['escola_localizacao'])."|"; // 19 - Localização/Zona da escola
        echo $row_EscolaLogada['escola_localizacao_diferenciada']."|"; // 20 - Localização diferenciada da escola
        echo "3|"; // 21 - Dependência administrativa
        echo "1|"; // 22 - Secretaria de Educação/Ministério da Educação
        echo "0|"; // 23 - Secretaria de Segurança Pública/Forças Armadas/Militar
        echo "0|"; // 24 - Secretaria da Saúde/Ministério da Saúde
        echo "0|"; // 25 - Outro órgão da administração pública
        echo "0|"; // 26 - Empresa, grupos empresariais do setor privado ou pessoa física
        echo "0|"; // 27 - Sindicatos de trabalhadores ou patronais, associações, cooperativas
        echo "0|"; // 28 - Organização não governamental (ONG) - nacional ou internacional
        echo "0|"; // 29 - Instituição sem fins lucrativos
        echo "0|"; // 30 - Sistema S (Sesi, Senai, Sesc, outros)
        echo "0|"; // 31 - Organização da Sociedade Civil de Interesse Público (Oscip)
        echo "|"; // 32 - Categoria da escola privada
        echo "0|"; // 33 - Secretaria estadual
        echo "0|"; // 34 - Secretaria Municipal
        echo "0|"; // 35 - Não possui parceria ou convênio
        echo "0|"; // 36 - Termo de colaboração (Lei nº 13.019/2014)
        echo "0|"; // 37 - Termo de fomento (Lei nº 13.019/2014)
        echo "0|"; // 38 - Acordo de cooperação (Lei nº 13.019/2014)
        echo "0|"; // 39 - Contrato de prestação de serviço
        echo "0|"; // 40 - Termo de cooperação técnica e financeira
        echo "0|"; // 41 - Contrato de consórcio público/Convênio de cooperação
        echo "0|"; // 42 - Termo de colaboração (Lei nº 13.019/2014)
        echo "0|"; // 43 - Termo de fomento (Lei nº 13.019/2014)
        echo "0|"; // 44 - Acordo de cooperação (Lei nº 13.019/2014)
        echo "0|"; // 45 - Contrato de prestação de serviço
        echo "0|"; // 46 - Termo de cooperação técnica e financeira
        echo "0|"; // 47 - Contrato de consórcio público/Convênio de cooperação
        echo "|"; // 48 - CNPJ da mantenedora principal da escola privada
        echo "|"; // 49 - Número do CNPJ da escola privada
        echo "1|"; // 50 - Regulamentação/autorização no conselho ou órgão municipal, estadual ou federal de educação
        echo "|"; // 51 - Federal
        echo "|"; // 52 - Estadual
        echo "1|"; // 53 - Municipal
        echo "0|"; // 54 - Unidade vinculada à escola de educação básica ou unidade ofertante de educação superior
        echo "|"; // 55 - Código da Escola Sede
        echo ""; // 56 - Código da IES
        echo "<br>";


        
        //CAMPO 10
        /*
        echo "<h3>REGISTRO 10 - CARACTERIZAÇÃO E INFRAESTRUTURA DA ESCOLA</h3>";

        echo "10|";  //001
        echo $row_EscolaLogada['escola_inep']."|";
        echo exibeZero($row_EscolaLogada['escola_info_predio_escolar_3']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_salas_em_outra_escola_4']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_galpao_rancho_paiol_barracao_5']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_unidade_de_atendimento_socioeducativa_6']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_unidade_prisional_7']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_outros_8_8']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_forma_de_ocupacao_do_predio_9']).'|';
        echo $row_EscolaLogada['escola_info_predio_escolar_compartilhado_com_outra_escola_10'].'|'; //10
        echo $row_EscolaLogada['escola_info_codigo_da_escola_com_a_qual_compartilha_1_11'].'|';
        echo $row_EscolaLogada['escola_info_codigo_da_escola_com_a_qual_compartilha_2_12'].'|';
        echo $row_EscolaLogada['escola_info_codigo_da_escola_com_a_qual_compartilha_3_13'].'|';
        echo $row_EscolaLogada['escola_info_codigo_da_escola_com_a_qual_compartilha_4_14'].'|';
        echo $row_EscolaLogada['escola_info_codigo_da_escola_com_a_qual_compartilha_5_15'].'|';
        echo $row_EscolaLogada['escola_info_codigo_da_escola_com_a_qual_compartilha_6_16'].'|';
        echo exibeZero($row_EscolaLogada['escola_info_fornece_agua_potavel_para_o_consumo_humano_17']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_rede_publica_18']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_poco_artesiano_19']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_cacimba_cisterna_poco_20']).'|'; //20
        echo exibeZero($row_EscolaLogada['escola_info_fonte_rio_igarape_riacho_corrego_21']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_carropipa_22']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_nao_ha_abastecimento_de_agua_23']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_rede_publica_24']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_gerador_movido_a_combustivel_fossil_25']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_fontes_de_energia_renovaveis_ou_alternativas_ger_26']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_nao_ha_energia_eletrica_27']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_rede_publica_28']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_fossa_septica_29']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_fossa_rudimentarcomum_30']).'|'; //30
        echo exibeZero($row_EscolaLogada['escola_info_nao_ha_esgotamento_sanitario_31']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_servico_de_coleta_32']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_queima_33']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_enterra_34']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_leva_a_uma_destinacao_final_licenciada_pelo_pode_35']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_descarta_em_outra_area_36']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_separacao_do_lixoresiduos_37']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_reaproveitamentoreutilizacao_38']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_reciclagem_39']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_nao_faz_tratamento_40']).'|'; //40
        echo exibeZero($row_EscolaLogada['escola_info_almoxarifado_41']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_area_de_vegetacao_ou_gramado_42']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_auditorio_43']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_banheiro_44']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_banheiro_acessivel_adequado_ao_uso_de_pessoas_co_45']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_banheiro_adequado_a_educacao_infantil_46']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_banheiro_exclusivo_para_os_funcionarios_47']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_banheiro_ou_vestiario_com_chuveiro_48']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_biblioteca_49']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_cozinha_50']).'|'; //50
        echo exibeZero($row_EscolaLogada['escola_info_despensa_51']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_dormitorio_de_alunoa_52']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_dormitorio_de_professora_53']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_laboratorio_de_ciencias_54']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_laboratorio_de_informatica_55']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_laboratorio_especifico_para_a_educacao_profissi_56']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_parque_infantil_57']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_patio_coberto_58']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_patio_descoberto_59']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_piscina_60']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_quadra_de_esportes_coberta_61']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_quadra_de_esportes_descoberta_62']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_refeitorio_63']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_sala_de_repouso_para_alunoa_64']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_salaatelie_de_artes_65']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_sala_de_musicacoral_66']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_salaestudio_de_danca_67']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_sala_multiuso_musica_danca_e_artes_68']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_terreirao_area_para_pratica_desportiva_e_recreac_69']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_viveirocriacao_de_animais_70']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_sala_de_diretoria_71']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_sala_de_leitura_72']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_sala_de_professores_73']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_sala_de_recursos_multifuncionais_para_atendiment_74']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_sala_de_secretaria_75']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_salas_de_oficinas_da_educacao_profissional_76']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_estudio_de_gravacao_e_edicao_77']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_area_de_horta_plantio_eou_producao_agricola_78']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_nenhuma_das_dependencias_relacionadas_79']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_corrimao_e_guardacorpos_80']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_elevador_81']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_pisos_tateis_82']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_portas_com_vao_livre_de_no_minimo_80_cm_83']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_rampas_84']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_sinalizacaoalarme_luminoso_85']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_sinalizacao_sonora_86']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_sinalizacao_tatil_87']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_sinalizacao_visual_pisoparedes_88']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_nenhum_dos_recursos_de_acessibilidade_listados_89']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_numero_de_salas_de_aula_utilizadas_na_escola_den_90']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_numero_de_salas_de_aula_utilizadas_na_escola_for_91']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_numero_de_salas_de_aula_climatizadas_ar_condicio_92']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_numero_de_salas_de_aula_com_acessibilidade_para__93']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_antena_parabolica_94']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_computadores_95']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_copiadora_96']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_impressora_97']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_impressora_multifuncional_98']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_scanner_99']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_nenhum_dos_equipamentos_listados_100']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_aparelho_de_dvdbluray_101']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_aparelho_de_som_102']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_aparelho_de_televisao_103']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_lousa_digital_104']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_projetor_multimidia_data_show_105']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_computadores_de_mesa_desktop_106']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_computadores_portateis_107']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_tablets_108']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_para_uso_administrativo_109']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_para_uso_no_processo_de_ensino_e_aprendizagem_110']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_para_uso_dos_alunoas_111']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_para_uso_da_comunidade_112']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_nao_possui_acesso_a_internet_113']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_computadores_de_mesa_portateis_e_tablets_da_esco_114']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_dispositivos_pessoais_computadores_portateis_cel_115']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_internet_banda_larga_116']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_a_cabo_117']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_wireless_118']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_nao_ha_rede_local_interligando_computadores_119']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_agronomosas_horticultoresas_tecnicos_ou_monitore_120']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_auxiliares_de_secretaria_ou_auxiliares_administr_121']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_auxiliar_de_servicos_gerais_porteiroa_zeladora_f_122']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_bibliotecarioa_auxiliar_de_biblioteca_ou_monitor_123']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_bombeiroa_brigadista_profissionais_de_assistenci_124']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_coordenadora_de_turnodisciplinar_125']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_fonoaudiologoa_126']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_nutricionista_127']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_psicologoa_escolar_128']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_profissionais_de_preparacao_e_seguranca_alimenta_129']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_profissionais_de_apoio_e_supervisao_pedagogica_p_130']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_secretarioa_escolar_131']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_seguranca_guarda_ou_seguranca_patrimonial_132']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_tecnicosas_monitoresas_supervisoresas_ou_auxilia_133']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_vicediretora_ou_diretora_adjuntoa_profissionais__134']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_orientadora_comunitarioa_ou_assistente_social_135']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_tradutor_e_interprete_de_libras_para_atendimento_136']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_revisor_de_texto_braille_assistente_vidente_assi_137']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_nao_ha_funcionarios_para_as_funcoes_listadas_138']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_alimentacao_escolar_para_os_alunoas_139']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_acervo_multimidia_140']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_brinquedos_para_educacao_infantil_141']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_conjunto_de_materiais_cientificos_142']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_equipamento_para_amplificacao_e_difusao_de_somau_143']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_equipamentos_e_instrumentos_para_atividades_em_a_144']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_instrumentos_musicais_para_conjunto_bandafanfarr_145']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_jogos_educativos_146']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_materiais_para_atividades_culturais_e_artisticas_147']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_materiais_para_educacao_profissional_148']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_materiais_para_pratica_desportiva_e_recreacao_149']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_materiais_pedagogicos_para_a_educacao_bilingue_d_150']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_materiais_pedagogicos_para_a_educacao_escolar_in_151']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_materiais_pedagogicos_para_a_educacao_das_relaco_152']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_materiais_pedagogicos_para_a_educacao_do_campo_153']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_materiais_pedagogicos_para_a_educacao_escolar_qu_154']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_materiais_pedagogicos_para_a_educacao_especial_155']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_nenhum_dos_instrumentos_listados_156']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_escola_indigena_157']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_lingua_indigena_158']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_lingua_portuguesa_159']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_codigo_da_lingua_indigena_1_160']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_codigo_da_lingua_indigena_2_161']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_codigo_da_lingua_indigena_3_162']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_a_escola_faz_exame_de_selecao_para_ingresso_de_s_163']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_autodeclarado_preto_pardo_ou_indigena_ppi_164']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_condicao_de_renda_165']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_oriundo_de_escola_publica_166']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_pessoa_com_deficiencia_pcd_167']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_outros_grupos_que_nao_os_listados_168']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_sem_reservas_de_vagas_para_sistema_de_cotas_ampl_169']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_a_escola_possui_site_ou_blog_ou_pagina_em_redes__170']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_a_escola_compartilha_espacos_para_atividades_de__171']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_a_escola_usa_espacos_e_equipamentos_do_entorno_e_172']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_associacao_de_pais_173']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_associacao_de_pais_e_mestres_174']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_conselho_escolar_175']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_gremio_estudantil_176']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_outros_177_177']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_nao_ha_orgaos_colegiados_em_funcionamento_178']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_o_projeto_politico_pedagogico_ou_a_proposta_peda_179']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_a_escola_desenvolve_acoes_na_area_de_educacao_am_180']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_como_conteudo_dos_componentescampos_de_experienc_181']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_como_um_componente_curricular_especial_especific_182']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_como_um_eixo_estruturante_do_curriculo_183']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_em_eventos_184']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_em_projetos_transversais_ou_interdisciplinares_185']).'|';
        echo exibeZero($row_EscolaLogada['escola_info_nenhuma_das_opcoes_listadas_186']);
        echo "<br>";

        */

        //CAMPO 10
        echo "<h3>REGISTRO 10 - CARACTERIZAÇÃO E INFRAESTRUTURA DA ESCOLA</h3>";

        echo "10|";  // Campo 1 - Tipo de registro
        echo $row_EscolaLogada['escola_inep']."|"; // Campo 2 - Código da escola (Inep)
        echo exibeZero($row_EscolaLogada['escola_info_predio_escolar_3']).'|'; // Campo 3 - Prédio escolar
        echo exibeZero($row_EscolaLogada['escola_info_salas_em_outra_escola_4']).'|'; // Campo 4 - Salas em outra escola
        echo exibeZero($row_EscolaLogada['escola_info_galpao_rancho_paiol_barracao_5']).'|'; // Campo 5 - Galpão, rancho, paiol, barracão
        echo exibeZero($row_EscolaLogada['escola_info_unidade_de_atendimento_socioeducativa_6']).'|'; // Campo 6 - Unidade de atendimento socioeducativa
        echo exibeZero($row_EscolaLogada['escola_info_unidade_prisional_7']).'|'; // Campo 7 - Unidade prisional
        echo exibeZero($row_EscolaLogada['escola_info_outros_8_8']).'|'; // Campo 8 - Outros tipos de ocupação
        echo exibeZero($row_EscolaLogada['escola_info_forma_de_ocupacao_do_predio_9']).'|'; // Campo 9 - Forma de ocupação do prédio
        echo $row_EscolaLogada['escola_info_predio_escolar_compartilhado_com_outra_escola_10'].'|'; // Campo 10 - Prédio escolar compartilhado com outra escola
        echo $row_EscolaLogada['escola_info_codigo_da_escola_com_a_qual_compartilha_1_11'].'|'; // Campo 11 - Código da escola com a qual compartilha (1)
        echo $row_EscolaLogada['escola_info_codigo_da_escola_com_a_qual_compartilha_2_12'].'|'; // Campo 12 - Código da escola com a qual compartilha (2)
        echo $row_EscolaLogada['escola_info_codigo_da_escola_com_a_qual_compartilha_3_13'].'|'; // Campo 13 - Código da escola com a qual compartilha (3)
        echo $row_EscolaLogada['escola_info_codigo_da_escola_com_a_qual_compartilha_4_14'].'|'; // Campo 14 - Código da escola com a qual compartilha (4)
        echo $row_EscolaLogada['escola_info_codigo_da_escola_com_a_qual_compartilha_5_15'].'|'; // Campo 15 - Código da escola com a qual compartilha (5)
        echo $row_EscolaLogada['escola_info_codigo_da_escola_com_a_qual_compartilha_6_16'].'|'; // Campo 16 - Código da escola com a qual compartilha (6)
        echo exibeZero($row_EscolaLogada['escola_info_fornece_agua_potavel_para_o_consumo_humano_17']).'|'; // Campo 17 - Fornece água potável para o consumo humano
        echo exibeZero($row_EscolaLogada['escola_info_rede_publica_18']).'|'; // Campo 18 - Rede pública de água
        echo exibeZero($row_EscolaLogada['escola_info_poco_artesiano_19']).'|'; // Campo 19 - Poço artesiano
        echo exibeZero($row_EscolaLogada['escola_info_cacimba_cisterna_poco_20']).'|'; // Campo 20 - Cacimba/cisterna/poço
        echo exibeZero($row_EscolaLogada['escola_info_fonte_rio_igarape_riacho_corrego_21']).'|'; // Campo 21 - Fonte, rio, igarapé, riacho, córrego
        echo exibeZero($row_EscolaLogada['escola_info_carropipa_22']).'|'; // Campo 22 - Carro-pipa
        echo exibeZero($row_EscolaLogada['escola_info_nao_ha_abastecimento_de_agua_23']).'|'; // Campo 23 - Não há abastecimento de água
        echo exibeZero($row_EscolaLogada['escola_info_rede_publica_24']).'|'; // Campo 24 - Rede pública de energia
        echo exibeZero($row_EscolaLogada['escola_info_gerador_movido_a_combustivel_fossil_25']).'|'; // Campo 25 - Gerador movido a combustível fóssil
        echo exibeZero($row_EscolaLogada['escola_info_fontes_de_energia_renovaveis_ou_alternativas_ger_26']).'|'; // Campo 26 - Fontes de energia renováveis ou alternativas
        echo exibeZero($row_EscolaLogada['escola_info_nao_ha_energia_eletrica_27']).'|'; // Campo 27 - Não há energia elétrica
        echo exibeZero($row_EscolaLogada['escola_info_rede_publica_28']).'|'; // Campo 28 - Rede pública de esgoto
        echo exibeZero($row_EscolaLogada['escola_info_fossa_septica_29']).'|'; // Campo 29 - Fossa séptica
        echo exibeZero($row_EscolaLogada['escola_info_fossa_rudimentarcomum_30']).'|'; // Campo 30 - Fossa rudimentar/comum
        echo exibeZero($row_EscolaLogada['escola_info_nao_ha_esgotamento_sanitario_31']).'|'; // Campo 31 - Não há esgotamento sanitário
        echo exibeZero($row_EscolaLogada['escola_info_servico_de_coleta_32']).'|'; // Campo 32 - Serviço de coleta de lixo
        echo exibeZero($row_EscolaLogada['escola_info_queima_33']).'|'; // Campo 33 - Queima do lixo
        echo exibeZero($row_EscolaLogada['escola_info_enterra_34']).'|'; // Campo 34 - Enterra o lixo
        echo exibeZero($row_EscolaLogada['escola_info_leva_a_uma_destinacao_final_licenciada_pelo_pode_35']).'|'; // Campo 35 - Leva a uma destinação final licenciada pelo poder público
        echo exibeZero($row_EscolaLogada['escola_info_descarta_em_outra_area_36']).'|'; // Campo 36 - Descarta em outra área
        echo exibeZero($row_EscolaLogada['escola_info_separacao_do_lixoresiduos_37']).'|'; // Campo 37 - Separação do lixo/resíduos
        echo exibeZero($row_EscolaLogada['escola_info_reaproveitamentoreutilizacao_38']).'|'; // Campo 38 - Reaproveitamento/reutilização de resíduos
        echo exibeZero($row_EscolaLogada['escola_info_reciclagem_39']).'|'; // Campo 39 - Reciclagem
        echo exibeZero($row_EscolaLogada['escola_info_nao_faz_tratamento_40']).'|'; // Campo 40 - Não faz tratamento do lixo
        echo exibeZero($row_EscolaLogada['escola_info_almoxarifado_41']).'|'; // Campo 41 - Almoxarifado
        echo exibeZero($row_EscolaLogada['escola_info_area_de_vegetacao_ou_gramado_42']).'|'; // Campo 42 - Área de vegetação ou gramado
        echo exibeZero($row_EscolaLogada['escola_info_auditorio_43']).'|'; // Campo 43 - Auditório
        echo exibeZero($row_EscolaLogada['escola_info_banheiro_44']).'|'; // Campo 44 - Banheiro
        echo exibeZero($row_EscolaLogada['escola_info_banheiro_acessivel_adequado_ao_uso_de_pessoas_co_45']).'|'; // Campo 45 - Banheiro acessível adequado ao uso de pessoas com deficiência
        echo exibeZero($row_EscolaLogada['escola_info_banheiro_adequado_a_educacao_infantil_46']).'|'; // Campo 46 - Banheiro adequado à educação infantil
        echo exibeZero($row_EscolaLogada['escola_info_banheiro_exclusivo_para_os_funcionarios_47']).'|'; // Campo 47 - Banheiro exclusivo para os funcionários
        echo exibeZero($row_EscolaLogada['escola_info_banheiro_ou_vestiario_com_chuveiro_48']).'|'; // Campo 48 - Banheiro ou vestiário com chuveiro
        echo exibeZero($row_EscolaLogada['escola_info_biblioteca_49']).'|'; // Campo 49 - Biblioteca
        echo exibeZero($row_EscolaLogada['escola_info_cozinha_50']).'|'; // Campo 50 - Cozinha
        echo exibeZero($row_EscolaLogada['escola_info_despensa_51']).'|'; // Campo 51 - Despensa
        echo exibeZero($row_EscolaLogada['escola_info_dormitorio_de_alunoa_52']).'|'; // Campo 52 - Dormitório de aluno(a)
        echo exibeZero($row_EscolaLogada['escola_info_dormitorio_de_professora_53']).'|'; // Campo 53 - Dormitório de professor(a)
        echo exibeZero($row_EscolaLogada['escola_info_laboratorio_de_ciencias_54']).'|'; // Campo 54 - Laboratório de ciências
        echo exibeZero($row_EscolaLogada['escola_info_laboratorio_de_informatica_55']).'|'; // Campo 55 - Laboratório de informática
        echo exibeZero($row_EscolaLogada['escola_info_laboratorio_especifico_para_a_educacao_profissi_56']).'|'; // Campo 56 - Laboratório específico para a educação profissional
        echo exibeZero($row_EscolaLogada['escola_info_parque_infantil_57']).'|'; // Campo 57 - Parque infantil
        echo exibeZero($row_EscolaLogada['escola_info_patio_coberto_58']).'|'; // Campo 58 - Pátio coberto
        echo exibeZero($row_EscolaLogada['escola_info_patio_descoberto_59']).'|'; // Campo 59 - Pátio descoberto
        echo exibeZero($row_EscolaLogada['escola_info_piscina_60']).'|'; // Campo 60 - Piscina
        echo exibeZero($row_EscolaLogada['escola_info_quadra_de_esportes_coberta_61']).'|'; // Campo 61 - Quadra de esportes coberta
        echo exibeZero($row_EscolaLogada['escola_info_quadra_de_esportes_descoberta_62']).'|'; // Campo 62 - Quadra de esportes descoberta
        echo exibeZero($row_EscolaLogada['escola_info_refeitorio_63']).'|'; // Campo 63 - Refeitório
        echo exibeZero($row_EscolaLogada['escola_info_sala_de_repouso_para_alunoa_64']).'|'; // Campo 64 - Sala de repouso para aluno(a)
        echo exibeZero($row_EscolaLogada['escola_info_salaatelie_de_artes_65']).'|'; // Campo 65 - Sala/Ateliê de artes
        echo exibeZero($row_EscolaLogada['escola_info_sala_de_musicacoral_66']).'|'; // Campo 66 - Sala de música/coral
        echo exibeZero($row_EscolaLogada['escola_info_salaestudio_de_danca_67']).'|'; // Campo 67 - Sala/Estúdio de dança
        echo exibeZero($row_EscolaLogada['escola_info_sala_multiuso_musica_danca_e_artes_68']).'|'; // Campo 68 - Sala multiuso (música, dança e artes)
        echo exibeZero($row_EscolaLogada['escola_info_terreirao_area_para_pratica_desportiva_e_recreac_69']).'|'; // Campo 69 - Terreirão (área para prática desportiva e recreação)
        echo exibeZero($row_EscolaLogada['escola_info_viveirocriacao_de_animais_70']).'|'; // Campo 70 - Viveiro/Criação de animais
        echo exibeZero($row_EscolaLogada['escola_info_sala_de_diretoria_71']).'|'; // Campo 71 - Sala de diretoria
        echo exibeZero($row_EscolaLogada['escola_info_sala_de_leitura_72']).'|'; // Campo 72 - Sala de leitura
        echo exibeZero($row_EscolaLogada['escola_info_sala_de_professores_73']).'|'; // Campo 73 - Sala de professores
        echo exibeZero($row_EscolaLogada['escola_info_sala_de_recursos_multifuncionais_para_atendiment_74']).'|'; // Campo 74 - Sala de recursos multifuncionais para atendimento educacional especializado
        echo exibeZero($row_EscolaLogada['escola_info_sala_de_secretaria_75']).'|'; // Campo 75 - Sala de secretaria
        echo exibeZero($row_EscolaLogada['escola_info_salas_de_oficinas_da_educacao_profissional_76']).'|'; // Campo 76 - Salas de oficinas da educação profissional
        echo exibeZero($row_EscolaLogada['escola_info_estudio_de_gravacao_e_edicao_77']).'|'; // Campo 77 - Estúdio de gravação e edição
        echo exibeZero($row_EscolaLogada['escola_info_area_de_horta_plantio_eou_producao_agricola_78']).'|'; // Campo 78 - Área de horta, plantio e/ou produção agrícola
        echo exibeZero($row_EscolaLogada['escola_info_nenhuma_das_dependencias_relacionadas_79']).'|'; // Campo 79 - Nenhuma das dependências relacionadas
        echo exibeZero($row_EscolaLogada['escola_info_corrimao_e_guardacorpos_80']).'|'; // Campo 80 - Corrimão e guarda-corpos
        echo exibeZero($row_EscolaLogada['escola_info_elevador_81']).'|'; // Campo 81 - Elevador
        echo exibeZero($row_EscolaLogada['escola_info_pisos_tateis_82']).'|'; // Campo 82 - Pisos táteis
        echo exibeZero($row_EscolaLogada['escola_info_portas_com_vao_livre_de_no_minimo_80_cm_83']).'|'; // Campo 83 - Portas com vão livre de no mínimo 80 cm
        echo exibeZero($row_EscolaLogada['escola_info_rampas_84']).'|'; // Campo 84 - Rampas
        echo exibeZero($row_EscolaLogada['escola_info_sinalizacaoalarme_luminoso_85']).'|'; // Campo 85 - Sinalização/alarme luminoso
        echo exibeZero($row_EscolaLogada['escola_info_sinalizacao_sonora_86']).'|'; // Campo 86 - Sinalização sonora
        echo exibeZero($row_EscolaLogada['escola_info_sinalizacao_tatil_87']).'|'; // Campo 87 - Sinalização tátil
        echo exibeZero($row_EscolaLogada['escola_info_sinalizacao_visual_pisoparedes_88']).'|'; // Campo 88 - Sinalização visual (piso/parede)
        echo exibeZero($row_EscolaLogada['escola_info_nenhum_dos_recursos_de_acessibilidade_listados_89']).'|'; // Campo 89 - Nenhum dos recursos de acessibilidade listados
        echo exibeZero($row_EscolaLogada['escola_info_numero_de_salas_de_aula_utilizadas_na_escola_den_90']).'|'; // Campo 90 - Número de salas de aula utilizadas na escola (denominador)
        echo exibeZero($row_EscolaLogada['escola_info_numero_de_salas_de_aula_utilizadas_na_escola_for_91']).'|'; // Campo 91 - Número de salas de aula utilizadas na escola (formação)
        echo exibeZero($row_EscolaLogada['escola_info_numero_de_salas_de_aula_climatizadas_ar_condicio_92']).'|'; // Campo 92 - Número de salas de aula climatizadas (ar-condicionado)
        echo exibeZero($row_EscolaLogada['escola_info_numero_de_salas_de_aula_com_acessibilidade_para__93']).'|'; // Campo 93 - Número de salas de aula com acessibilidade para pessoas com deficiência
        echo exibeZero($row_EscolaLogada['escola_info_antena_parabolica_94']).'|'; // Campo 94 - Antena parabólica
        echo exibeZero($row_EscolaLogada['escola_info_computadores_95']).'|'; // Campo 95 - Computadores
        echo exibeZero($row_EscolaLogada['escola_info_copiadora_96']).'|'; // Campo 96 - Copiadora
        echo exibeZero($row_EscolaLogada['escola_info_impressora_97']).'|'; // Campo 97 - Impressora
        echo exibeZero($row_EscolaLogada['escola_info_impressora_multifuncional_98']).'|'; // Campo 98 - Impressora multifuncional
        echo exibeZero($row_EscolaLogada['escola_info_scanner_99']).'|'; // Campo 99 - Scanner
        echo exibeZero($row_EscolaLogada['escola_info_nenhum_dos_equipamentos_listados_100']).'|'; // Campo 100 - Nenhum dos equipamentos listados
        echo exibeZero($row_EscolaLogada['escola_info_aparelho_de_dvdbluray_101']).'|'; // Campo 101 - Aparelho de DVD/Blu-ray
        echo exibeZero($row_EscolaLogada['escola_info_aparelho_de_som_102']).'|'; // Campo 102 - Aparelho de som
        echo exibeZero($row_EscolaLogada['escola_info_aparelho_de_televisao_103']).'|'; // Campo 103 - Aparelho de televisão
        echo exibeZero($row_EscolaLogada['escola_info_lousa_digital_104']).'|'; // Campo 104 - Lousa digital
        echo exibeZero($row_EscolaLogada['escola_info_projetor_multimidia_data_show_105']).'|'; // Campo 105 - Projetor multimídia/data show
        echo exibeZero($row_EscolaLogada['escola_info_computadores_de_mesa_desktop_106']).'|'; // Campo 106 - Computadores de mesa (desktop)
        echo exibeZero($row_EscolaLogada['escola_info_computadores_portateis_107']).'|'; // Campo 107 - Computadores portáteis
        echo exibeZero($row_EscolaLogada['escola_info_tablets_108']).'|'; // Campo 108 - Tablets
        echo exibeZero($row_EscolaLogada['escola_info_para_uso_administrativo_109']).'|'; // Campo 109 - Para uso administrativo
        echo exibeZero($row_EscolaLogada['escola_info_para_uso_no_processo_de_ensino_e_aprendizagem_110']).'|'; // Campo 110 - Para uso no processo de ensino e aprendizagem
        echo exibeZero($row_EscolaLogada['escola_info_para_uso_dos_alunoas_111']).'|'; // Campo 111 - Para uso dos aluno(as)
        echo exibeZero($row_EscolaLogada['escola_info_para_uso_da_comunidade_112']).'|'; // Campo 112 - Para uso da comunidade
        echo exibeZero($row_EscolaLogada['escola_info_nao_possui_acesso_a_internet_113']).'|'; // Campo 113 - Não possui acesso à internet
        echo exibeZero($row_EscolaLogada['escola_info_computadores_de_mesa_portateis_e_tablets_da_esco_114']).'|'; // Campo 114 - Computadores de mesa, portáteis e tablets da escola
        echo exibeZero($row_EscolaLogada['escola_info_dispositivos_pessoais_computadores_portateis_cel_115']).'|'; // Campo 115 - Dispositivos pessoais (computadores portáteis, celulares)
        echo exibeZero($row_EscolaLogada['escola_info_internet_banda_larga_116']).'|'; // Campo 116 - Internet banda larga
        echo exibeZero($row_EscolaLogada['escola_info_a_cabo_117']).'|'; // Campo 117 - A cabo
        echo exibeZero($row_EscolaLogada['escola_info_wireless_118']).'|'; // Campo 118 - Wireless
        echo exibeZero($row_EscolaLogada['escola_info_nao_ha_rede_local_interligando_computadores_119']).'|'; // Campo 119 - Não há rede local interligando computadores
        echo exibeZero($row_EscolaLogada['escola_info_agronomosas_horticultoresas_tecnicos_ou_monitore_120']).'|'; // Campo 120 - Agrônomos(as), horticultores(as), técnicos ou monitores
        echo exibeZero($row_EscolaLogada['escola_info_auxiliares_de_secretaria_ou_auxiliares_administr_121']).'|'; // Campo 121 - Auxiliares de secretaria ou auxiliares administrativos
        echo exibeZero($row_EscolaLogada['escola_info_auxiliar_de_servicos_gerais_porteiroa_zeladora_f_122']).'|'; // Campo 122 - Auxiliar de serviços gerais, porteiro(a), zelador(a), faxineiro(a)
        echo exibeZero($row_EscolaLogada['escola_info_bibliotecarioa_auxiliar_de_biblioteca_ou_monitor_123']).'|'; // Campo 123 - Bibliotecário(a), auxiliar de biblioteca ou monitor
        echo exibeZero($row_EscolaLogada['escola_info_bombeiroa_brigadista_profissionais_de_assistenci_124']).'|'; // Campo 124 - Bombeiro(a), brigadista, profissionais de assistência
        echo exibeZero($row_EscolaLogada['escola_info_coordenadora_de_turnodisciplinar_125']).'|'; // Campo 125 - Coordenador(a) de turno/disciplina
        echo exibeZero($row_EscolaLogada['escola_info_fonoaudiologoa_126']).'|'; // Campo 126 - Fonoaudiólogo(a)
        echo exibeZero($row_EscolaLogada['escola_info_nutricionista_127']).'|'; // Campo 127 - Nutricionista
        echo exibeZero($row_EscolaLogada['escola_info_psicologoa_escolar_128']).'|'; // Campo 128 - Psicólogo(a) escolar
        echo exibeZero($row_EscolaLogada['escola_info_profissionais_de_preparacao_e_seguranca_alimenta_129']).'|'; // Campo 129 - Profissionais de preparação e segurança alimentar
        echo exibeZero($row_EscolaLogada['escola_info_profissionais_de_apoio_e_supervisao_pedagogica_p_130']).'|'; // Campo 130 - Profissionais de apoio e supervisão pedagógica
        echo exibeZero($row_EscolaLogada['escola_info_secretarioa_escolar_131']).'|'; // Campo 131 - Secretário(a) escolar
        echo exibeZero($row_EscolaLogada['escola_info_seguranca_guarda_ou_seguranca_patrimonial_132']).'|'; // Campo 132 - Segurança, guarda ou segurança patrimonial
        echo exibeZero($row_EscolaLogada['escola_info_tecnicosas_monitoresas_supervisoresas_ou_auxilia_133']).'|'; // Campo 133 - Técnicos(as), monitores(as), supervisores(as) ou auxiliares
        echo exibeZero($row_EscolaLogada['escola_info_vicediretora_ou_diretora_adjuntoa_profissionais__134']).'|'; // Campo 134 - Vice-diretor(a) ou diretor(a) adjunto(a), profissionais de direção
        echo exibeZero($row_EscolaLogada['escola_info_orientadora_comunitarioa_ou_assistente_social_135']).'|'; // Campo 135 - Orientador(a) comunitário(a) ou assistente social
        echo exibeZero($row_EscolaLogada['escola_info_tradutor_e_interprete_de_libras_para_atendimento_136']).'|'; // Campo 136 - Tradutor e intérprete de libras para atendimento
        echo exibeZero($row_EscolaLogada['escola_info_revisor_de_texto_braille_assistente_vidente_assi_137']).'|'; // Campo 137 - Revisor de texto braille, assistente vidente, assistente para deficientes visuais
        echo exibeZero($row_EscolaLogada['escola_info_nao_ha_funcionarios_para_as_funcoes_listadas_138']).'|'; // Campo 138 - Não há funcionários para as funções listadas
        echo exibeZero($row_EscolaLogada['escola_info_alimentacao_escolar_para_os_alunoas_139']).'|'; // Campo 139 - Alimentação escolar para os alunos
        echo exibeZero($row_EscolaLogada['escola_info_acervo_multimidia_140']).'|'; // Campo 140 - Acervo multimídia
        echo exibeZero($row_EscolaLogada['escola_info_brinquedos_para_educacao_infantil_141']).'|'; // Campo 141 - Brinquedos para educação infantil
        echo exibeZero($row_EscolaLogada['escola_info_conjunto_de_materiais_cientificos_142']).'|'; // Campo 142 - Conjunto de materiais científicos
        echo exibeZero($row_EscolaLogada['escola_info_equipamento_para_amplificacao_e_difusao_de_somau_143']).'|'; // Campo 143 - Equipamento para amplificação e difusão de som
        echo exibeZero($row_EscolaLogada['escola_info_equipamentos_e_instrumentos_para_atividades_em_a_144']).'|'; // Campo 144 - Equipamentos e instrumentos para atividades em artes
        echo exibeZero($row_EscolaLogada['escola_info_instrumentos_musicais_para_conjunto_bandafanfarr_145']).'|'; // Campo 145 - Instrumentos musicais para conjunto/banda/fanfarra
        echo exibeZero($row_EscolaLogada['escola_info_jogos_educativos_146']).'|'; // Campo 146 - Jogos educativos
        echo exibeZero($row_EscolaLogada['escola_info_materiais_para_atividades_culturais_e_artisticas_147']).'|'; // Campo 147 - Materiais para atividades culturais e artísticas
        echo exibeZero($row_EscolaLogada['escola_info_materiais_para_educacao_profissional_148']).'|'; // Campo 148 - Materiais para educação profissional
        echo exibeZero($row_EscolaLogada['escola_info_materiais_para_pratica_desportiva_e_recreacao_149']).'|'; // Campo 149 - Materiais para prática desportiva e recreação
        echo exibeZero($row_EscolaLogada['escola_info_materiais_pedagogicos_para_a_educacao_bilingue_d_150']).'|'; // Campo 150 - Materiais pedagógicos para a educação bilíngue
        echo exibeZero($row_EscolaLogada['escola_info_materiais_pedagogicos_para_a_educacao_escolar_in_151']).'|'; // Campo 151 - Materiais pedagógicos para a educação escolar indígena
        echo exibeZero($row_EscolaLogada['escola_info_materiais_pedagogicos_para_a_educacao_das_relaco_152']).'|'; // Campo 152 - Materiais pedagógicos para a educação das relações étnico-raciais
        echo exibeZero($row_EscolaLogada['escola_info_materiais_pedagogicos_para_a_educacao_do_campo_153']).'|'; // Campo 153 - Materiais pedagógicos para a educação do campo
        echo exibeZero($row_EscolaLogada['escola_info_materiais_pedagogicos_para_a_educacao_escolar_qu_154']).'|'; // Campo 154 - Materiais pedagógicos para a educação escolar quilombola
        echo exibeZero($row_EscolaLogada['escola_info_materiais_pedagogicos_para_a_educacao_especial_155']).'|'; // Campo 155 - Materiais pedagógicos para a educação especial
        echo exibeZero($row_EscolaLogada['escola_info_nenhum_dos_instrumentos_listados_156']).'|'; // Campo 156 - Nenhum dos instrumentos listados
        echo exibeZero($row_EscolaLogada['escola_info_escola_indigena_157']).'|'; // Campo 157 - Escola indígena
        echo exibeZero($row_EscolaLogada['escola_info_lingua_indigena_158']).'|'; // Campo 158 - Língua indígena
        echo exibeZero($row_EscolaLogada['escola_info_lingua_portuguesa_159']).'|'; // Campo 159 - Língua portuguesa
        echo exibeZero($row_EscolaLogada['escola_info_codigo_da_lingua_indigena_1_160']).'|'; // Campo 160 - Código da língua indígena (1)
        echo exibeZero($row_EscolaLogada['escola_info_codigo_da_lingua_indigena_2_161']).'|'; // Campo 161 - Código da língua indígena (2)
        echo exibeZero($row_EscolaLogada['escola_info_codigo_da_lingua_indigena_3_162']).'|'; // Campo 162 - Código da língua indígena (3)
        echo exibeZero($row_EscolaLogada['escola_info_a_escola_faz_exame_de_selecao_para_ingresso_de_s_163']).'|'; // Campo 163 - A escola faz exame de seleção para ingresso de alunos
        echo exibeZero($row_EscolaLogada['escola_info_autodeclarado_preto_pardo_ou_indigena_ppi_164']).'|'; // Campo 164 - Autodeclarado preto, pardo ou indígena (PPI)
        echo exibeZero($row_EscolaLogada['escola_info_condicao_de_renda_165']).'|'; // Campo 165 - Condição de renda
        echo exibeZero($row_EscolaLogada['escola_info_oriundo_de_escola_publica_166']).'|'; // Campo 166 - Oriundo de escola pública
        echo exibeZero($row_EscolaLogada['escola_info_pessoa_com_deficiencia_pcd_167']).'|'; // Campo 167 - Pessoa com deficiência (PCD)
        echo exibeZero($row_EscolaLogada['escola_info_outros_grupos_que_nao_os_listados_168']).'|'; // Campo 168 - Outros grupos que não os listados
        echo exibeZero($row_EscolaLogada['escola_info_sem_reservas_de_vagas_para_sistema_de_cotas_ampl_169']).'|'; // Campo 169 - Sem reservas de vagas para sistema de cotas (amplo)
        echo exibeZero($row_EscolaLogada['escola_info_a_escola_possui_site_ou_blog_ou_pagina_em_redes__170']).'|'; // Campo 170 - A escola possui site ou blog ou página em redes sociais
        echo exibeZero($row_EscolaLogada['escola_info_a_escola_compartilha_espacos_para_atividades_de__171']).'|'; // Campo 171 - A escola compartilha espaços para atividades de outras instituições
        echo exibeZero($row_EscolaLogada['escola_info_a_escola_usa_espacos_e_equipamentos_do_entorno_e_172']).'|'; // Campo 172 - A escola usa espaços e equipamentos do entorno
        echo exibeZero($row_EscolaLogada['escola_info_associacao_de_pais_173']).'|'; // Campo 173 - Associação de pais
        echo exibeZero($row_EscolaLogada['escola_info_associacao_de_pais_e_mestres_174']).'|'; // Campo 174 - Associação de pais e mestres
        echo exibeZero($row_EscolaLogada['escola_info_conselho_escolar_175']).'|'; // Campo 175 - Conselho escolar
        echo exibeZero($row_EscolaLogada['escola_info_gremio_estudantil_176']).'|'; // Campo 176 - Grêmio estudantil
        echo exibeZero($row_EscolaLogada['escola_info_outros_177_177']).'|'; // Campo 177 - Outros
        echo exibeZero($row_EscolaLogada['escola_info_nao_ha_orgaos_colegiados_em_funcionamento_178']).'|'; // Campo 178 - Não há órgãos colegiados em funcionamento
        echo exibeZero($row_EscolaLogada['escola_info_o_projeto_politico_pedagogico_ou_a_proposta_peda_179']).'|'; // Campo 179 - Projeto político-pedagógico ou proposta pedagógica
        echo exibeZero($row_EscolaLogada['escola_info_a_escola_desenvolve_acoes_na_area_de_educacao_am_180']).'|'; // Campo 180 - A escola desenvolve ações na área de educação ambiental
        echo exibeZero($row_EscolaLogada['escola_info_como_conteudo_dos_componentescampos_de_experienc_181']).'|'; // Campo 181 - Como conteúdo dos componentes/campos de experiência
        echo exibeZero($row_EscolaLogada['escola_info_como_um_componente_curricular_especial_especific_182']).'|'; // Campo 182 - Como um componente curricular especial/específico
        echo exibeZero($row_EscolaLogada['escola_info_como_um_eixo_estruturante_do_curriculo_183']).'|'; // Campo 183 - Como um eixo estruturante do currículo
        echo exibeZero($row_EscolaLogada['escola_info_em_eventos_184']).'|'; // Campo 184 - Em eventos
        echo exibeZero($row_EscolaLogada['escola_info_em_projetos_transversais_ou_interdisciplinares_185']).'|'; // Campo 185 - Em projetos transversais ou interdisciplinares
        echo exibeZero($row_EscolaLogada['escola_info_nenhuma_das_opcoes_listadas_186']); // Campo 186 - Nenhuma das opções listadas
        echo "<br>";


         //CAMPO 20
         echo "<h3>REGISTRO 20 - IDENTIFICAÇÃO DAS TURMAS</h3>";

        do {

          echo "20|";                                                                                     // Campo 1: Tipo de Registro (fixo)
          echo $row_EscolaLogada['escola_inep']."|";                                                      // Campo 2: Código de escola - Inep
          echo exibeZero($row_TurmasListar['turma_id']).'|';                                              // Campo 3: Código da Turma na Entidade/Escola
          echo $row_TurmasListar['turma_info_codigo_da_turma_inep_4'].'|';                                // Campo 4: Código da Turma - Inep
          echo retiraAcentos($row_TurmasListar['turma_nome']).'|';                                        // Campo 5: Nome da Turma
          echo exibeZero($row_TurmasListar['turma_info_tipo_de_mediacao_didaticopedagogica_6']).'|';      // Campo 6: Tipo de mediação didático-pedagógica
          echo exibeZero($row_TurmasListar['turma_info_hora_inicial_hora_7']).'|';                        // Campo 7: Hora Inicial - Hora
          echo exibeZero($row_TurmasListar['turma_info_hora_inicial_minuto_8']).'|';                      // Campo 8: Hora Inicial - Minuto
          echo exibeZero($row_TurmasListar['turma_info_hora_final_hora_9']).'|';                          // Campo 9: Hora Final - Hora
          echo exibeZero($row_TurmasListar['turma_info_hora_final_minuto_10']).'|';                       // Campo 10: Hora Final - Minuto
          echo exibeZero($row_TurmasListar['turma_info_domingo_11']).'|';                                 // Campo 11: Domingo
          echo exibeZero($row_TurmasListar['turma_info_segundafeira_12']).'|';                            // Campo 12: Segunda-feira
          echo exibeZero($row_TurmasListar['turma_info_tercafeira_13']).'|';                              // Campo 13: Terça-feira
          echo exibeZero($row_TurmasListar['turma_info_quartafeira_14']).'|';                             // Campo 14: Quarta-feira
          echo exibeZero($row_TurmasListar['turma_info_quintafeira_15']).'|';                             // Campo 15: Quinta-feira
          echo exibeZero($row_TurmasListar['turma_info_sextafeira_16']).'|';                              // Campo 16: Sexta-feira
          echo exibeZero($row_TurmasListar['turma_info_sabado_17']).'|';                                  // Campo 17: Sábado
          
          if ($row_TurmasListar['turma_tipo_atendimento'] == "1") { echo "1|"; } else { echo "0|"; };     // Campo 18: Escolarização
          if ($row_TurmasListar['turma_tipo_atendimento'] == "2") { echo "2|"; } else { echo "0|"; };     // Campo 19: Atividade complementar
          if ($row_TurmasListar['turma_tipo_atendimento'] == "3") { echo "3|"; } else { echo "0|"; };     // Campo 20: Atendimento educacional especializado - AEE
          
          echo exibeZero($row_TurmasListar['turma_info_formacao_geral_basica_21']).'|';                   // Campo 21: Formação geral básica
          echo exibeZero($row_TurmasListar['turma_info_itinerario_formativo_22']).'|';                    // Campo 22: Itinerário formativo
          echo exibeZero($row_TurmasListar['turma_info_nao_se_aplica_23']).'|';                           // Campo 23: Não se aplica
          echo $row_TurmasListar['turma_info_codigo_1_24'].'|';                                           // Campo 24: Código 1
          echo $row_TurmasListar['turma_info_codigo_2_25'].'|';                                           // Campo 25: Código 2
          echo $row_TurmasListar['turma_info_codigo_3_26'].'|';                                           // Campo 26: Código 3
          echo $row_TurmasListar['turma_info_codigo_4_27'].'|';                                           // Campo 27: Código 4
          echo $row_TurmasListar['turma_info_codigo_5_28'].'|';                                           // Campo 28: Código 5
          echo $row_TurmasListar['turma_info_codigo_6_29'].'|';                                           // Campo 29: Código 6
          echo exibeZero($row_TurmasListar['turma_info_local_de_funcionamento_diferenciado_da_t_30']).'|';// Campo 30: Local de funcionamento diferenciado da turma
          echo exibeZero($row_TurmasListar['turma_info_modalidade_31']).'|';                              // Campo 31: Modalidade
          echo exibeZero($row_TurmasListar['turma_etapa']).'|';                                           // Campo 32: Etapa
          echo "|";                                                                                      // Campo 33: Código Curso (não usado)
          
          if (($row_TurmasListar['turma_etapa']=="1") || ($row_TurmasListar['turma_etapa']=="2") || ($row_TurmasListar['turma_etapa']=="2")) { echo "0|"; } else { echo "1|"; }; // Campo 34 a 39: Formas de organização da turma
          echo exibeZero($row_TurmasListar['turma_info_periodos_semestrais_35']).'|';                     // Campo 35: Períodos semestrais
          echo exibeZero($row_TurmasListar['turma_info_ciclos_36']).'|';                                  // Campo 36: Ciclos
          echo exibeZero($row_TurmasListar['turma_info_grupos_nao_seriados_com_base_na_idade_ou_37']).'|';// Campo 37: Grupos não seriados com base na idade ou competência
          echo exibeZero($row_TurmasListar['turma_info_modulos_38']).'|';                                 // Campo 38: Módulos
          echo exibeZero($row_TurmasListar['turma_info_alternancia_regular_de_periodos_de_estud_39']).'|';// Campo 39: Alternância regular de períodos de estudos
          // Campos 40 a 76 seguem a mesma lógica, com comentários apropriados.
          
          echo exibeZero($row_TurmasListar['turma_info_eletivas_40']).'|';                                // Campo 40: Eletivas
          echo exibeZero($row_TurmasListar['turma_info_libras_41']).'|';                                  // Campo 41: Libras
          echo exibeZero($row_TurmasListar['turma_info_lingua_indigena_42']).'|';                         // Campo 42: Língua indígena
          echo exibeZero($row_TurmasListar['turma_info_lingua_literatura_estrangeira_espanhol_43']).'|';  // Campo 43: Língua/Literatura estrangeira - Espanhol
          echo exibeZero($row_TurmasListar['turma_info_lingua_literatura_estrangeira_frances_44']).'|';   // Campo 44: Língua/Literatura estrangeira - Francês
          echo exibeZero($row_TurmasListar['turma_info_lingua_literatura_estrangeira_outra_45']).'|';     // Campo 45: Língua/Literatura estrangeira - outra
          echo exibeZero($row_TurmasListar['turma_info_projeto_de_vida_46']).'|';                         // Campo 46: Projeto de vida
          echo exibeZero($row_TurmasListar['turma_info_trilhas_de_aprofundamento_aprendizagens_47']).'|'; // Campo 47: Trilhas de aprofundamento/aprendizagens
          echo exibeZero($row_TurmasListar['turma_info_outras_unidades_curriculares_obrigatoria_48']).'|';// Campo 48: Outra(s) unidade(s) curricular(es) obrigatória(s)
          echo exibeZero($row_TurmasListar['turma_info_1_quimica_49']).'|';                               // Campo 49: 1. Química
          echo exibeZero($row_TurmasListar['turma_info_2_fisica_50']).'|';                                // Campo 50: 2. Física
          echo exibeZero($row_TurmasListar['turma_info_3_matematica_51']).'|';                            // Campo 51: 3. Matemática
          echo exibeZero($row_TurmasListar['turma_info_4_biologia_52']).'|';                              // Campo 52: 4. Biologia
          echo exibeZero($row_TurmasListar['turma_info_5_ciencias_53']).'|';                              // Campo 53: 5. Ciências
          echo exibeZero($row_TurmasListar['turma_info_6_lingua_literatura_portuguesa_54']).'|';          // Campo 54: 6. Língua/Literatura Portuguesa
          echo exibeZero($row_TurmasListar['turma_info_7_lingua_literatura_estrangeira_ingles_55']).'|';  // Campo 55: 7. Língua/Literatura Estrangeira – Inglês
          echo exibeZero($row_TurmasListar['turma_info_8_lingua_literatura_estrangeira_espanhol_56']).'|';// Campo 56: 8. Língua/Literatura Estrangeira – Espanhol
          echo exibeZero($row_TurmasListar['turma_info_9_lingua_literatura_estrangeira_outra_57']).'|';   // Campo 57: 9. Língua/Literatura Estrangeira – outra
          echo exibeZero($row_TurmasListar['turma_info_10_arte_educacao_artistica_teatro_danca__58']).'|';// Campo 58: 10. Arte (Educação Artística, Teatro, Dança, Música, Artes Plásticas e outras)
          echo exibeZero($row_TurmasListar['turma_info_11_educacao_fisica_59']).'|';                      // Campo 59: 11. Educação Física
          echo exibeZero($row_TurmasListar['turma_info_12_historia_60']).'|';                             // Campo 60: 12. História
          echo exibeZero($row_TurmasListar['turma_info_13_geografia_61']).'|';                            // Campo 61: 13. Geografia
          echo exibeZero($row_TurmasListar['turma_info_14_filosofia_62']).'|';                            // Campo 62: 14. Filosofia
          echo exibeZero($row_TurmasListar['turma_info_16_informatica_computacao_63']).'|';               // Campo 63: 16. Informática/ Computação
          echo exibeZero($row_TurmasListar['turma_info_17_areas_do_conhecimento_profissionaliza_64']).'|';// Campo 64: 17. Áreas do conhecimento profissionalizantes
          echo exibeZero($row_TurmasListar['turma_info_23_libras_65']).'|';                               // Campo 65: 23. Libras
          echo exibeZero($row_TurmasListar['turma_info_25_areas_do_conhecimento_pedagogicas_66']).'|';    // Campo 66: 25. Áreas do conhecimento pedagógicas
          echo exibeZero($row_TurmasListar['turma_info_26_ensino_religioso_67']).'|';                     // Campo 67: 26. Ensino Religioso
          echo exibeZero($row_TurmasListar['turma_info_27_lingua_indigena_68']).'|';                      // Campo 68: 27. Língua Indígena
          echo exibeZero($row_TurmasListar['turma_info_28_estudos_sociais_69']).'|';                      // Campo 69: 28. Estudos Sociais
          echo exibeZero($row_TurmasListar['turma_info_29_sociologia_70']).'|';                           // Campo 70: 29. Sociologia
          echo exibeZero($row_TurmasListar['turma_info_30_lingua_literatura_estrangeira_frances_71']).'|';// Campo 71: 30. Língua/Literatura Estrangeira – Francês
          echo exibeZero($row_TurmasListar['turma_info_31_lingua_portuguesa_como_segunda_lingua_72']).'|';// Campo 72: 31. Língua Portuguesa como Segunda Língua
          echo exibeZero($row_TurmasListar['turma_info_32_estagio_curricular_supervisionado_73']).'|';    // Campo 73: 32. Estágio curricular supervisionado
          echo exibeZero($row_TurmasListar['turma_info_33_projeto_de_vida_74']).'|';                      // Campo 74: 33. Projeto de vida
          echo exibeZero($row_TurmasListar['turma_info_99_outras_areas_do_conhecimento_75']).'|';         // Campo 75: 99. Outras áreas do conhecimento
          echo exibeZero($row_TurmasListar['turma_info_classe_bilingue_de_surdos_tendo_a_libras_76']);    // Campo 76: Classe bilíngue de surdos
          echo "<br>";


        } while ($row_TurmasListar = mysql_fetch_assoc($TurmasListar));

        
        //CAMPO 30 - EXIBE GESTOR ESCOLAR
        echo "<h3>REGISTRO 30 - PESSOAS FÍSICAS DA ESCOLA (GESTORES)</h3>";

        do {

          /*

          echo "30|";                                                                                     
          echo $row_EscolaLogada['escola_inep']."|";                                                      
          echo str_pad($row_Gestor['vinculo_id'], 6, '0', STR_PAD_LEFT)."|";
          echo "|"; // 4 - CÓDIGO INEP
          echo limpaCPF($row_Gestor['func_cpf'])."|";
          echo retiraAcentos($row_Gestor['func_nome'])."|";
          echo date("d/m/Y", strtotime($row_Gestor['func_data_nascimento']))."|";
          if ($row_Gestor['func_mae']<>"" || $row_Gestor['func_pai']<>"") { echo "1|"; } else { echo "0|"; }
          echo retiraAcentos($row_Gestor['func_mae'])."|";
          echo retiraAcentos($row_Gestor['func_pai'])."|";
          echo $row_Gestor['func_sexo']."|";
          echo "0|"; //CAMPO 12 DECLARAÇÃO DE COR/RAÇA = ACRESCENTAR NA TABELA FUNCIONARIOS (smc_func)
          echo "1|"; //CAMPO 13 NACIONALIDADE = ACRESCENTAR NA TABELA
          echo "76|"; //CAMPO 14 PREENCHER COM O CÓDIGO DO PAIS BASEADO NA TABELA = ACRESCENTAR
          echo "8888888|"; //CAMPO 15 = CODIGO IBGE DO MUNICIPIO DE NASCIMENTO
          echo "0|"; //16 - ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //27 - ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //39 - ACRESCENTAR
          echo "0|"; //40 - CERTIDÃO / NÃO PRECISA PREENCHER SE NÃO FOR ALUNO
          echo "|"; //País de residência
          echo "|"; //CEP DA RESIDENCIA // VAMOS DEIXAR SEM PREENCHER
          echo "|"; //IBGE DA RESIDENCIA // VAMOS DEIXAR SEM PREENCHER
          echo "1|"; //44 - URBANA E RURAL // VAMOS DEIXAR COMO URBANA POR ENQUANTO
          echo "7|"; //45 - LOCALIZAÇÃO DIFERENCIADA // VAMOS DEIXAR COMO NÃO ESTÁ EM... POR ENQUANTO
          if ($row_Gestor['func_escolaridade']=="1") { echo "2|"; } else if ($row_Gestor['func_escolaridade']=="2") { echo "7|"; } else if ($row_Gestor['func_escolaridade']=="3" || $row_Gestor['func_escolaridade']=="4" || $row_Gestor['func_escolaridade']=="5") { echo "6|"; } else { echo "1|"; }
          echo "|"; //47 - MODALIDADE DO ENSINO MEDIO // VER DEPOIS
          echo "|"; //48 - Código do curso. Deve ser preenchido com base na tabela de cursos, se o campo 46 for igual a 6 
          echo "|"; //49 - Ano de conclusão do curso superior
          echo "|"; //50 - Tabela de IES
          echo "|"; //51 - Código do curso 2. Deve ser preenchido com base na tabela de cursos, se o campo 46 for igual a 6 
          echo "|"; //52 Ano de conclusão do curso superior 2
          echo "|"; //53 - Tabela de IES 2
          echo "|"; //54 - Código do curso 2. Deve ser preenchido com base na tabela de cursos, se o campo 46 for igual a 6 
          echo "|"; //55 Ano de conclusão do curso superior 2
          echo "|"; //56 - Tabela de IES 2
          echo "|"; //57 - tabela de Áreas de Conhecimento // não precisa
          echo "|"; //58 - tabela de Áreas de Conhecimento // não precisa
          echo "|"; //59 - tabela de Áreas de Conhecimento // não precisa
          echo "|"; //60 - Tipo de pós-graduação 1
          echo "|"; //61
          echo "|"; //62
          echo "|"; //63 - Tipo de pós-graduação 2
          echo "|"; //64
          echo "|"; //65
          echo "|"; //66 - Tipo de pós-graduação 3
          echo "|"; //67
          echo "|"; //68
          echo "|"; //69 - Tipo de pós-graduação 4
          echo "|"; //70
          echo "|"; //71
          echo "|"; //72 - Tipo de pós-graduação 5
          echo "|"; //73
          echo "|"; //74
          echo "|"; //75 - Tipo de pós-graduação 6
          echo "|"; //76
          echo "|"; //77
          echo "|"; //78 SEM POS
          echo "|"; //79 Cursos de formação continuada creche
          echo "|"; //80 pré-escola
          echo "|"; //81 Anos iniciais fundamental
          echo "|"; //82 Anos finais fundamental
          echo "|"; //83 Ensino Medio
          echo "|"; //84 EJA
          echo "|";
          echo "|";
          echo "|";
          echo "|";
          echo "|";
          echo "|"; //90
          echo "|";
          echo "|";
          echo "|";
          echo "|";
          echo "|";
          echo "|";
          echo "|";
          echo strtolower($row_Gestor['func_email']);
          echo "<br>";

          */

          echo "30|"; // Tipo de registro
echo $row_EscolaLogada['escola_inep'] . "|"; // Código da escola - Inep
echo str_pad($row_Gestor['func_id'], 6, '0', STR_PAD_LEFT) . "|"; // Código da pessoa física no sistema próprio
echo (isset($row_Gestor['func_cod_inep']) ? $row_Gestor['func_cod_inep'] : "") . "|"; // Identificação única (Inep)
echo limpaCPF($row_Gestor['func_cpf']) . "|"; // CPF
echo retiraAcentos($row_Gestor['func_nome']) . "|"; // Nome completo
echo date("d/m/Y", strtotime($row_Gestor['func_data_nascimento'])) . "|"; // Data de nascimento

// Filiação
if ($row_Gestor['func_mae'] != "" || $row_Gestor['func_pai'] != "") {
    echo "1|"; // Tem filiação informada
} else {
    echo "0|"; // Não declarada/Ignorada
}
echo retiraAcentos($row_Gestor['func_mae']) . "|"; // Filiação 1 (nome da mãe)
echo retiraAcentos($row_Gestor['func_pai']) . "|"; // Filiação 2 (nome do pai)
echo $row_Gestor['func_sexo'] . "|"; // Sexo
echo (isset($row_Gestor['func_raca']) ? $row_Gestor['func_raca'] : "0") . "|"; // Cor/Raça (0 - Não Declarada)

// Nacionalidade
echo (isset($row_Gestor['func_nacionalidade']) ? $row_Gestor['func_nacionalidade'] : "1") . "|"; // Nacionalidade (1 - Brasileira)
echo (isset($row_Gestor['func_pais']) ? $row_Gestor['func_pais'] : "76") . "|"; // País de nacionalidade (76 - Brasil)

// Município de nascimento
echo (isset($row_Gestor['func_municipio_nascimento_ibge']) ? $row_Gestor['func_municipio_nascimento_ibge'] : "8888888") . "|"; // Código IBGE do município

// Deficiência ou transtornos
echo (isset($row_Gestor['func_func_com_deficiencia']) ? $row_Gestor['func_func_com_deficiencia'] : "0") . "|"; // Pessoa física com deficiência
echo (isset($row_Gestor['func_def_cegueira']) ? $row_Gestor['func_def_cegueira'] : "0") . "|"; // Cegueira
echo (isset($row_Gestor['func_def_bvisao']) ? $row_Gestor['func_def_bvisao'] : "0") . "|"; // Baixa visão
echo (isset($row_Gestor['func_def_auditiva']) ? $row_Gestor['func_def_auditiva'] : "0") . "|"; // Deficiência auditiva
echo (isset($row_Gestor['func_def_surdez']) ? $row_Gestor['func_def_surdez'] : "0") . "|"; // Surdez
echo (isset($row_Gestor['func_def_fisica']) ? $row_Gestor['func_def_fisica'] : "0") . "|"; // Deficiência física
echo (isset($row_Gestor['func_def_intelectual']) ? $row_Gestor['func_def_intelectual'] : "0") . "|"; // Deficiência intelectual
echo (isset($row_Gestor['func_def_surdocegueira']) ? $row_Gestor['func_def_surdocegueira'] : "0") . "|"; // Surdocegueira
echo (isset($row_Gestor['func_def_autista']) ? $row_Gestor['func_def_autista'] : "0") . "|"; // Transtorno do espectro autista
echo (isset($row_Gestor['func_def_superdotacao']) ? $row_Gestor['func_def_superdotacao'] : "0") . "|"; // Altas habilidades/superdotação

// Recursos não preenchidos (apenas placeholders por enquanto)
for ($i = 0; $i < 12; $i++) {
    echo "0|";
}

// Certidão de nascimento (não aplicável para gestores, apenas para alunos)
echo "0|"; // Número da matrícula da certidão de nascimento

// Residência
echo "|"; // País de residência (não preenchido)
echo "|"; // CEP da residência (não preenchido)
echo "|"; // IBGE do município da residência (não preenchido)
echo "1|"; // Localização/Zona de residência (1 - Urbana)
echo "7|"; // Localização diferenciada de residência (7 - Não está em localização diferenciada)

// Escolaridade
if ($row_Gestor['func_escolaridade'] == "1") {
    echo "2|"; // Não concluiu ensino fundamental
} elseif ($row_Gestor['func_escolaridade'] == "2") {
    echo "7|"; // Ensino médio
} elseif (in_array($row_Gestor['func_escolaridade'], ["3", "4", "5", "6"])) {
    echo "6|"; // Educação superior
} else {
    echo "1|"; // Outro (não especificado)
}

// Formação e pós-graduações (não preenchidas, placeholders)
for ($i = 47; $i <= 97; $i++) {
    echo "|";
}

// E-mail
echo strtolower($row_Gestor['func_email']) . "|"; // Endereço eletrônico (e-mail)
echo "<br>";



        } while ($row_Gestor = mysql_fetch_assoc($Gestor));


        //CAMPO 30 - EXIBE PROFESSOR ESCOLAR
        echo "<h3>REGISTRO 30 - PESSOAS FÍSICAS DA ESCOLA (PROFISSIONAIS ESCOLARES)</h3>";

        
        do {
          echo "30|";                                                                                     
          echo $row_EscolaLogada['escola_inep']."|";                                                      
          echo str_pad($row_Professor['vinculo_id'], 6, '0', STR_PAD_LEFT)."|";
          echo "|"; // 4 - CÓDIGO INEP
          echo limpaCPF($row_Professor['func_cpf'])."|";
          echo retiraAcentos($row_Professor['func_nome'])."|";
          echo date("d/m/Y", strtotime($row_Professor['func_data_nascimento']))."|";
          if ($row_Professor['func_mae']<>"" || $row_Professor['func_pai']<>"") { echo "1|"; } else { echo "0|"; }
          echo retiraAcentos($row_Professor['func_mae'])."|";
          echo retiraAcentos($row_Professor['func_pai'])."|";
          echo $row_Professor['func_sexo']."|";
          echo "0|"; //CAMPO 12 DECLARAÇÃO DE COR/RAÇA = ACRESCENTAR NA TABELA FUNCIONARIOS (smc_func)
          echo "1|"; //CAMPO 13 NACIONALIDADE = ACRESCENTAR NA TABELA
          echo "76|"; //CAMPO 14 PREENCHER COM O CÓDIGO DO PAIS BASEADO NA TABELA = ACRESCENTAR
          echo "8888888|"; //CAMPO 15 = CODIGO IBGE DO MUNICIPIO DE NASCIMENTO
          echo "0|"; //16 - ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //27 - ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //ACRESCENTAR
          echo "0|"; //39 - ACRESCENTAR
          echo "0|"; //40 - CERTIDÃO / NÃO PRECISA PREENCHER SE NÃO FOR ALUNO
          echo "|"; //País de residência
          echo "|"; //CEP DA RESIDENCIA // VAMOS DEIXAR SEM PREENCHER
          echo "|"; //IBGE DA RESIDENCIA // VAMOS DEIXAR SEM PREENCHER
          echo "1|"; //44 - URBANA E RURAL // VAMOS DEIXAR COMO URBANA POR ENQUANTO
          echo "7|"; //45 - LOCALIZAÇÃO DIFERENCIADA // VAMOS DEIXAR COMO NÃO ESTÁ EM... POR ENQUANTO
          if ($row_Professor['func_escolaridade']=="1") { echo "2|"; } else if ($row_Professor['func_escolaridade']=="2") { echo "7|"; } else if ($row_Professor['func_escolaridade']=="3" || $row_Professor['func_escolaridade']=="4" || $row_Professor['func_escolaridade']=="5") { echo "6|"; } else { echo "1|"; }
          echo "|"; //47 - MODALIDADE DO ENSINO MEDIO // VER DEPOIS
          echo "|"; //48 - Código do curso. Deve ser preenchido com base na tabela de cursos, se o campo 46 for igual a 6 
          echo "|"; //49 - Ano de conclusão do curso superior
          echo "|"; //50 - Tabela de IES
          echo "|"; //51 - Código do curso 2. Deve ser preenchido com base na tabela de cursos, se o campo 46 for igual a 6 
          echo "|"; //52 Ano de conclusão do curso superior 2
          echo "|"; //53 - Tabela de IES 2
          echo "|"; //54 - Código do curso 2. Deve ser preenchido com base na tabela de cursos, se o campo 46 for igual a 6 
          echo "|"; //55 Ano de conclusão do curso superior 2
          echo "|"; //56 - Tabela de IES 2
          echo "|"; //57 - tabela de Áreas de Conhecimento // não precisa
          echo "|"; //58 - tabela de Áreas de Conhecimento // não precisa
          echo "|"; //59 - tabela de Áreas de Conhecimento // não precisa
          echo "|"; //60 - Tipo de pós-graduação 1
          echo "|"; //61
          echo "|"; //62
          echo "|"; //63 - Tipo de pós-graduação 2
          echo "|"; //64
          echo "|"; //65
          echo "|"; //66 - Tipo de pós-graduação 3
          echo "|"; //67
          echo "|"; //68
          echo "|"; //69 - Tipo de pós-graduação 4
          echo "|"; //70
          echo "|"; //71
          echo "|"; //72 - Tipo de pós-graduação 5
          echo "|"; //73
          echo "|"; //74
          echo "|"; //75 - Tipo de pós-graduação 6
          echo "|"; //76
          echo "|"; //77
          echo "|"; //78 SEM POS
          echo "|"; //79 Cursos de formação continuada creche
          echo "|"; //80 pré-escola
          echo "|"; //81 Anos iniciais fundamental
          echo "|"; //82 Anos finais fundamental
          echo "|"; //83 Ensino Medio
          echo "|"; //84 EJA
          echo "|";
          echo "|";
          echo "|";
          echo "|";
          echo "|";
          echo "|"; //90
          echo "|";
          echo "|";
          echo "|";
          echo "|";
          echo "|";
          echo "|";
          echo "|";
          echo "|"; // Email só para o gestor escolar
          echo "<br>";
        } while ($row_Professor = mysql_fetch_assoc($Professor));

        
        //CAMPO 30 - EXIBE ALUNOS
        echo "<h3>REGISTRO 30 - PESSOAS FÍSICAS DA ESCOLA (ALUNOS)</h3>";

        do {
          echo "30|";                                                                                     //ok
          echo $row_EscolaLogada['escola_inep']."|";                                                      //ok
          echo str_pad($row_ExibirAlunosVinculados['vinculo_aluno_id'], 6, '0', STR_PAD_LEFT)."|";
          echo "|"; //4 - Identificação única (Inep) / 
          echo limpaCPF($row_ExibirAlunosVinculados['aluno_cpf'])."|"; //5 - Número do CPF / 
          echo retiraAcentos($row_ExibirAlunosVinculados['aluno_nome'])."|"; //6 - Nome completo / Obrigatorio
          echo date("d/m/Y", strtotime($row_ExibirAlunosVinculados['aluno_nascimento']))."|"; //7 - Data de nascimento / Obrigatorio
          if ($row_ExibirAlunosVinculados['aluno_filiacao1']<>"" || $row_ExibirAlunosVinculados['aluno_filiacao2']<>"") { echo "1|"; } else { echo "0|"; }
          echo $row_ExibirAlunosVinculados['aluno_filiacao1']."|"; //9 - Filiação 1 (preferencialmente o nome da mãe) / 
          echo $row_ExibirAlunosVinculados['aluno_filiacao2']."|"; //10 - Filiação 2 (preferencialmente o nome do pai) / 
          echo $row_ExibirAlunosVinculados['aluno_sexo']."|"; //11 - Sexo / Obrigatorio
          echo corRaca($row_ExibirAlunosVinculados['aluno_raca'])."|"; //12 - Cor/Raça / Obrigatorio
          echo $row_ExibirAlunosVinculados['aluno_nacionalidade']."|"; //13 - Nacionalidade / Obrigatorio
          echo $row_ExibirAlunosVinculados['aluno_pais']."|"; //14 - País de nacionalidade / Obrigatorio
          echo $row_ExibirAlunosVinculados['aluno_municipio_nascimento_ibge']."|"; //15 - Município de nascimento / 
          echo "|"; //16 - Pessoa física com deficiência, transtorno do espectro autista ou altas habilidades/ superdotação / Obrigatorio
          echo "|"; //17 - Cegueira / 
          echo "|"; //18 - Baixa visão / 
          echo "|"; //19 - Visão monocular / 
          echo "|"; //20 - Surdez / 
          echo "|"; //21 - Deficiência auditiva / 
          echo "|"; //22 - Surdocegueira / 
          echo "|"; //23 - Deficiência física / 
          echo "|"; //24 - Deficiência intelectual / 
          echo "|"; //25 - Deficiência múltipla / 
          echo "|"; //26 - Transtorno do espectro autista / 
          echo "|"; //27 - Altas habilidades/ superdotação / 
          echo "|"; //28 - Auxílio ledor / 
          echo "|"; //29 - Auxílio transcrição / 
          echo "|"; //30 - Guia-Intérprete / 
          echo "|"; //31 - Tradutor-Intérprete de Libras / 
          echo "|"; //32 - Leitura Labial / 
          echo "|"; //33 - Prova Ampliada (Fonte 18) / 
          echo "|"; //34 - Prova superampliada (Fonte 24) / 
          echo "|"; //35 - CD com áudio para deficiente visual / 
          echo "|"; //36 - Prova de Língua Portuguesa como Segunda Língua para surdos e deficientes auditivos / 
          echo "|"; //37 - Prova em Vídeo em Libras / 
          echo "|"; //38 - Material didático e prova em Braille / 
          echo "|"; //39 - Nenhum / 
          echo "|"; //40 - Número da matrícula da certidão de nascimento (certidão nova) / 
          echo "|"; //41 - País de residência / 
          echo "|"; //42 - CEP / 
          echo "|"; //43 - Município de residência / 
          echo "|"; //44 - Localização/ Zona de residência / 
          echo "|"; //45 - Localização diferenciada de residência / 
          echo "|"; //46 - Maior nível de escolaridade concluído / 
          echo "|"; //47 - Tipo de ensino médio cursado / 
          echo "|"; //48 - Código do Curso 1 / 
          echo "|"; //49 - Ano de Conclusão 1 / 
          echo "|"; //50 - Instituição de educação superior 1 / 
          echo "|"; //51 - Código do Curso 2 / 
          echo "|"; //52 - Ano de Conclusão 2 / 
          echo "|"; //53 - Instituição de educação superior 2 / 
          echo "|"; //54 - Código do Curso 3 / 
          echo "|"; //55 - Ano de Conclusão 3 / 
          echo "|"; //56 - Instituição de educação superior 3 / 
          echo "|"; //57 - Área do conhecimento/ componentes curriculares 1 / 
          echo "|"; //58 - Área do conhecimento/ componentes curriculares 2 / 
          echo "|"; //59 - Área do conhecimento/ componentes curriculares 3 / 
          echo "|"; //60 - Tipo de pós-graduação 1 / 
          echo "|"; //61 - Área da pós-graduação 1 / 
          echo "|"; //62 - Ano de conclusão da pós-graduação 1 / 
          echo "|"; //63 - Tipo de pós-graduação 2 / 
          echo "|"; //64 - Área da pós-graduação 2 / 
          echo "|"; //65 - Ano de conclusão da pós-graduação 2 / 
          echo "|"; //66 - Tipo de pós-graduação 3 / 
          echo "|"; //67 - Área da pós-graduação 3 / 
          echo "|"; //68 - Ano de conclusão da pós-graduação 3 / 
          echo "|"; //69 - Tipo de pós-graduação 4 / 
          echo "|"; //70 - Área da pós-graduação 4 / 
          echo "|"; //71 - Ano de conclusão da pós-graduação 4 / 
          echo "|"; //72 - Tipo de pós-graduação 5 / 
          echo "|"; //73 - Área da pós-graduação 5 / 
          echo "|"; //74 - Ano de conclusão da pós-graduação 5 / 
          echo "|"; //75 - Tipo de pós-graduação 6 / 
          echo "|"; //76 - Área da pós-graduação 6 / 
          echo "|"; //77 - Ano de conclusão da pós-graduação 6 / 
          echo "|"; //78 - Não tem pós-graduação concluída / 
          echo "|"; //79 - Creche (0 a 3 anos) / 
          echo "|"; //80 - Pré-escola (4 e 5 anos) / 
          echo "|"; //81 - Anos iniciais do ensino fundamental / 
          echo "|"; //82 - Anos finais do ensino fundamental / 
          echo "|"; //83 - Ensino médio / 
          echo "|"; //84 - Educação de jovens e adultos / 
          echo "|"; //85 - Educação especial / 
          echo "|"; //86 - Educação Indígena / 
          echo "|"; //87 - Educação do campo / 
          echo "|"; //88 - Educação ambiental / 
          echo "|"; //89 - Educação em direitos humanos / 
          echo "|"; //90 - Educação bilíngue de surdos / 
          echo "|"; //91 - Educação e Tecnologia de Informação e Comunicação (TIC) / 
          echo "|"; //92 - Gênero e diversidade sexual / 
          echo "|"; //93 - Direitos de criança e adolescente / 
          echo "|"; //94 - Educação para as relações étnico-raciais e História e cultura afro-brasileira e africana / 
          echo "|"; //95 - Gestão Escolar / 
          echo "|"; //96 - Outros / 
          echo "|"; //97 - Nenhum / 
          echo "|"; //98 - Endereço Eletrônico (e-mail) / 

          echo "<br>";

        } while ($row_ExibirAlunosVinculados = mysql_fetch_assoc($ExibirAlunosVinculados));

        echo "<h3>REGISTRO 40 - VÍNCULO DE GESTORES ESCOLARES</h3>";

        echo "40|"; 
        echo $row_EscolaLogada['escola_inep']."|"; //2	Código de escola - Inep
        echo str_pad($row_GestorVinculo['vinculo_id'], 6, '0', STR_PAD_LEFT)."|"; //3 Código da pessoa física no sistema próprio
        echo "|"; //4 Identificação única (Inep)
        echo "1|"; //5 Cargo
        echo "2|"; //6	Critério de acesso ao cargo/função 
	      echo "1|"; //7	Situação Funcional/ Regime de contratação/Tipo de vínculo 
        echo "<br>";

        
//CAMPO 50 - EXIBE VÍNCULO DO PROFESSOR ESCOLAR
echo "<h3>REGISTRO 50 - EXIBE VÍNCULOS DOS PROFISSIONAIS ESCOLARES</h3>";

        
do {
echo "50|"; // Tipo de registro                                                                                     
echo $row_EscolaLogada['escola_inep']."|"; // Código de escola - Inep                                                     
echo str_pad($row_ProfessorVinculo['vinculo_id'], 6, '0', STR_PAD_LEFT)."|"; // Código da pessoa física no sistema próprio
echo "4|"; //4 - Identificação única (Inep)
echo "5|"; //5 - Código da Turma na Entidade/Escola
echo "6|"; //6 - Código da turma no INEP
echo "7|"; //7 - Função que exerce na escola/Turma
echo "8|"; //8 - Situação funcional/regime de contratação/tipo de vínculo
echo "9|"; //9 - Código 1
echo "10|"; //10 - Código 2
echo "11|"; //11 - Código 3
echo "12|"; //12 - Código 4
echo "13|"; //13 - Código 5
echo "14|"; //14 - Código 6
echo "15|"; //15 - Código 7
echo "16|"; //16 - Código 8
echo "17|"; //17 - Código 9
echo "18|"; //18 - Código 10
echo "19|"; //19 - Código 11
echo "20|"; //20 - Código 12
echo "21|"; //21 - Código 13
echo "22|"; //22 - Código 14
echo "23|"; //23 - Código 15
echo "24|"; //24 - Código 16
echo "25|"; //25 - Código 17
echo "26|"; //26 - Código 18
echo "27|"; //27 - Código 19
echo "28|"; //28 - Código 20
echo "29|"; //29 - Código 21
echo "30|"; //30 - Código 22
echo "31|"; //31 - Código 23
echo "32|"; //32 - Código 24
echo "33|"; //33 - Código 25
echo "34|"; //34 - Eletivas
echo "35|"; //35 - Libras
echo "36|"; //36 - Língua indígena
echo "37|"; //37 - Língua/Literatura estrangeira - Espanhol
echo "38|"; //38 - Língua/Literatura estrangeira - Francês
echo "39|"; //39 - Língua/Literatura estrangeira - outra
echo "40|"; //40 - Projeto de vida
echo "41|"; //41 - Trilhas de aprofundamento/aprendizagens
echo "42|"; //42 - Outra(s) unidade(s) curricular(es) obrigatória(s)
  
  
  echo "<br>";
} while ($row_ProfessorVinculo = mysql_fetch_assoc($ProfessorVinculo));




//CAMPO 30 - EXIBE ALUNOS
echo "<h3>REGISTRO 60 - EXIBE VÍNCULO DE ALUNOS</h3>";

do {
echo "60|"; //1 - Tipo de registro                                                                            //ok
echo $row_EscolaLogada['escola_inep']."|"; //2 - Código de escola - Inep                                                     //ok
echo str_pad($row_ExibirAlunosVinculadosVinculo['vinculo_aluno_id'], 6, '0', STR_PAD_LEFT)."|";//3 - Código da pessoa física no sistema próprio
echo "4|"; //4 - Identificação única (Inep)
echo "5|"; //5 - Código da Turma na Entidade/Escola
echo "6|"; //6 - Código da turma no INEP
echo "7|"; //7 - Código da Matrícula do(a) aluno(a)
echo "8|"; //8 - Turma multi
echo "9|"; //9 - Linguagens e suas tecnologias
echo "10|"; //10 - Matemática e suas tecnologias
echo "11|"; //11 - Ciências da natureza e suas tecnologias
echo "12|"; //12 - Ciências humanas e sociais aplicadas
echo "13|"; //13 - Formação técnica e profissional
echo "14|"; //14 - Itinerário formativo integrado (entre as áreas de conhecimento ou entre as áreas de conhecimento e a formação técnica profissional)
echo "15|"; //15 - Linguagens e suas tecnologias
echo "16|"; //16 - Matemática e suas tecnologias
echo "17|"; //17 - Ciências da natureza e suas tecnologias
echo "18|"; //18 - Ciências humanas e sociais aplicadas
echo "19|"; //19 - Formação técnica e profissional
echo "20|"; //20 - Tipo do curso do itinerário de formação técnica e profissional
echo "21|"; //21 - Código do curso técnico
echo "22|"; //22 - Itinerário concomitante intercomplementar à matrícula de formação geral básica
echo "23|"; //23 - Desenvolvimento de funções cognitivas
echo "24|"; //24 - Desenvolvimento de vida autônoma
echo "25|"; //25 - Enriquecimento curricular
echo "26|"; //26 - Ensino da informática acessível
echo "27|"; //27 - Ensino da Língua Brasileira de Sinais (Libras)
echo "28|"; //28 - Ensino da Língua Portuguesa como Segunda Língua
echo "29|"; //29 - Ensino das técnicas do cálculo no Soroban
echo "30|"; //30 - Ensino de Sistema Braille
echo "31|"; //31 - Ensino de técnicas para orientação e mobilidade
echo "32|"; //32 - Ensino de uso da Comunicação Alternativa e Aumentativa (CAA)
echo "33|"; //33 - Ensino de uso de recursos ópticos e não ópticos
echo "34|"; //34 - Recebe escolarização em outro espaço (diferente da escola)
echo "35|"; //35 - Transporte escolar público
echo "36|"; //36 - Poder Público responsável pelo transporte escolar
echo "37|"; //37 - Rodoviário - Bicicleta
echo "38|"; //38 - Rodoviário - Microônibus
echo "39|"; //39 - Rodoviário - Ônibus
echo "40|"; //40 - Rodoviário – Tração Animal
echo "41|"; //41 - Rodoviário - Vans/Kombis
echo "42|"; //42 - Rodoviário - Outro
echo "43|"; //43 - Aquaviário - Capacidade de até 5 aluno(a)s
echo "44|"; //44 - Aquaviário - Capacidade entre 5 a 15 aluno(a)s
echo "45|"; //45 - Aquaviário - Capacidade entre 15 a 35 aluno(a)s
echo "46|"; //46 - Aquaviário - Capacidade acima de 35 aluno(a)s
  


  echo "<br>";

} while ($row_ExibirAlunosVinculadosVinculo = mysql_fetch_assoc($ExibirAlunosVinculadosVinculo));




 } while ($row_EscolaLogada = mysql_fetch_assoc($EscolaLogada));
 
 echo "99|"; //99 - FIM DA EXPORTAÇÃO

 
 
 ?>


<?php
mysql_free_result($UsuLogado);
mysql_free_result($EscolaLogada);
?>

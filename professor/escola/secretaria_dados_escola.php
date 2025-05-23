<?php require_once('../../Connections/SmecelNovo.php'); ?>
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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "
SELECT *,
sec_id, sec_cidade, sec_uf, sec_ibge_municipio 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
		
	if ($row_UsuLogado['usu_update']=="N") {
		header(sprintf("Location: secretaria.php?permissao"));
		break;
	}
	
    $updateSQL = sprintf("UPDATE smc_escola SET 

    escola_info_predio_escolar_3=%s,
    escola_info_salas_em_outra_escola_4=%s,
    escola_info_galpao_rancho_paiol_barracao_5=%s,
    escola_info_unidade_de_atendimento_socioeducativa_6=%s,
    escola_info_unidade_prisional_7=%s,
    escola_info_outros_8_8=%s,
    escola_info_forma_de_ocupacao_do_predio_9=%s,
    escola_info_predio_escolar_compartilhado_com_outra_escola_10=%s,
    escola_info_codigo_da_escola_com_a_qual_compartilha_1_11=%s,
    escola_info_codigo_da_escola_com_a_qual_compartilha_2_12=%s,
    escola_info_codigo_da_escola_com_a_qual_compartilha_3_13=%s,
    escola_info_codigo_da_escola_com_a_qual_compartilha_4_14=%s,
    escola_info_codigo_da_escola_com_a_qual_compartilha_5_15=%s,
    escola_info_codigo_da_escola_com_a_qual_compartilha_6_16=%s,
    escola_info_fornece_agua_potavel_para_o_consumo_humano_17=%s,
    escola_info_rede_publica_18=%s,
    escola_info_poco_artesiano_19=%s,
    escola_info_cacimba_cisterna_poco_20=%s,
    escola_info_fonte_rio_igarape_riacho_corrego_21=%s,
    escola_info_carropipa_22=%s,
    escola_info_nao_ha_abastecimento_de_agua_23=%s,
    escola_info_rede_publica_24=%s,
    escola_info_gerador_movido_a_combustivel_fossil_25=%s,
    escola_info_fontes_de_energia_renovaveis_ou_alternativas_ger_26=%s,
    escola_info_nao_ha_energia_eletrica_27=%s,
    escola_info_rede_publica_28=%s,
    escola_info_fossa_septica_29=%s,
    escola_info_fossa_rudimentarcomum_30=%s,
    escola_info_nao_ha_esgotamento_sanitario_31=%s,
    escola_info_servico_de_coleta_32=%s,
    escola_info_queima_33=%s,
    escola_info_enterra_34=%s,
    escola_info_leva_a_uma_destinacao_final_licenciada_pelo_pode_35=%s,
    escola_info_descarta_em_outra_area_36=%s,
    escola_info_separacao_do_lixoresiduos_37=%s,
    escola_info_reaproveitamentoreutilizacao_38=%s,
    escola_info_reciclagem_39=%s,
    escola_info_nao_faz_tratamento_40=%s,
    escola_info_almoxarifado_41=%s,
    escola_info_area_de_vegetacao_ou_gramado_42=%s,
    escola_info_auditorio_43=%s,
    escola_info_banheiro_44=%s,
    escola_info_banheiro_acessivel_adequado_ao_uso_de_pessoas_co_45=%s,
    escola_info_banheiro_adequado_a_educacao_infantil_46=%s,
    escola_info_banheiro_exclusivo_para_os_funcionarios_47=%s,
    escola_info_banheiro_ou_vestiario_com_chuveiro_48=%s,
    escola_info_biblioteca_49=%s,
    escola_info_cozinha_50=%s,
    escola_info_despensa_51=%s,
    escola_info_dormitorio_de_alunoa_52=%s,
    escola_info_dormitorio_de_professora_53=%s,
    escola_info_laboratorio_de_ciencias_54=%s,
    escola_info_laboratorio_de_informatica_55=%s,
    escola_info_laboratorio_especifico_para_a_educacao_profissi_56=%s,
    escola_info_parque_infantil_57=%s,
    escola_info_patio_coberto_58=%s,
    escola_info_patio_descoberto_59=%s,
    escola_info_piscina_60=%s,
    escola_info_quadra_de_esportes_coberta_61=%s,
    escola_info_quadra_de_esportes_descoberta_62=%s,
    escola_info_refeitorio_63=%s,
    escola_info_sala_de_repouso_para_alunoa_64=%s,
    escola_info_salaatelie_de_artes_65=%s,
    escola_info_sala_de_musicacoral_66=%s,
    escola_info_salaestudio_de_danca_67=%s,
    escola_info_sala_multiuso_musica_danca_e_artes_68=%s,
    escola_info_terreirao_area_para_pratica_desportiva_e_recreac_69=%s,
    escola_info_viveirocriacao_de_animais_70=%s,
    escola_info_sala_de_diretoria_71=%s,
    escola_info_sala_de_leitura_72=%s,
    escola_info_sala_de_professores_73=%s,
    escola_info_sala_de_recursos_multifuncionais_para_atendiment_74=%s,
    escola_info_sala_de_secretaria_75=%s,
    escola_info_salas_de_oficinas_da_educacao_profissional_76=%s,
    escola_info_estudio_de_gravacao_e_edicao_77=%s,
    escola_info_area_de_horta_plantio_eou_producao_agricola_78=%s,
    escola_info_nenhuma_das_dependencias_relacionadas_79=%s,
    escola_info_corrimao_e_guardacorpos_80=%s,
    escola_info_elevador_81=%s,
    escola_info_pisos_tateis_82=%s,
    escola_info_portas_com_vao_livre_de_no_minimo_80_cm_83=%s,
    escola_info_rampas_84=%s,
    escola_info_sinalizacaoalarme_luminoso_85=%s,
    escola_info_sinalizacao_sonora_86=%s,
    escola_info_sinalizacao_tatil_87=%s,
    escola_info_sinalizacao_visual_pisoparedes_88=%s,
    escola_info_nenhum_dos_recursos_de_acessibilidade_listados_89=%s,
    escola_info_numero_de_salas_de_aula_utilizadas_na_escola_den_90=%s,
    escola_info_numero_de_salas_de_aula_utilizadas_na_escola_for_91=%s,
    escola_info_numero_de_salas_de_aula_climatizadas_ar_condicio_92=%s,
    escola_info_numero_de_salas_de_aula_com_acessibilidade_para__93=%s,
    escola_info_antena_parabolica_94=%s,
    escola_info_computadores_95=%s,
    escola_info_copiadora_96=%s,
    escola_info_impressora_97=%s,
    escola_info_impressora_multifuncional_98=%s,
    escola_info_scanner_99=%s,
    escola_info_nenhum_dos_equipamentos_listados_100=%s,
    escola_info_aparelho_de_dvdbluray_101=%s,
    escola_info_aparelho_de_som_102=%s,
    escola_info_aparelho_de_televisao_103=%s,
    escola_info_lousa_digital_104=%s,
    escola_info_projetor_multimidia_data_show_105=%s,
    escola_info_computadores_de_mesa_desktop_106=%s,
    escola_info_computadores_portateis_107=%s,
    escola_info_tablets_108=%s,
    escola_info_para_uso_administrativo_109=%s,
    escola_info_para_uso_no_processo_de_ensino_e_aprendizagem_110=%s,
    escola_info_para_uso_dos_alunoas_111=%s,
    escola_info_para_uso_da_comunidade_112=%s,
    escola_info_nao_possui_acesso_a_internet_113=%s,
    escola_info_computadores_de_mesa_portateis_e_tablets_da_esco_114=%s,
    escola_info_dispositivos_pessoais_computadores_portateis_cel_115=%s,
    escola_info_internet_banda_larga_116=%s,
    escola_info_a_cabo_117=%s,
    escola_info_wireless_118=%s,
    escola_info_nao_ha_rede_local_interligando_computadores_119=%s,
    escola_info_agronomosas_horticultoresas_tecnicos_ou_monitore_120=%s,
    escola_info_auxiliares_de_secretaria_ou_auxiliares_administr_121=%s,
    escola_info_auxiliar_de_servicos_gerais_porteiroa_zeladora_f_122=%s,
    escola_info_bibliotecarioa_auxiliar_de_biblioteca_ou_monitor_123=%s,
    escola_info_bombeiroa_brigadista_profissionais_de_assistenci_124=%s,
    escola_info_coordenadora_de_turnodisciplinar_125=%s,
    escola_info_fonoaudiologoa_126=%s,
    escola_info_nutricionista_127=%s,
    escola_info_psicologoa_escolar_128=%s,
    escola_info_profissionais_de_preparacao_e_seguranca_alimenta_129=%s,
    escola_info_profissionais_de_apoio_e_supervisao_pedagogica_p_130=%s,
    escola_info_secretarioa_escolar_131=%s,
    escola_info_seguranca_guarda_ou_seguranca_patrimonial_132=%s,
    escola_info_tecnicosas_monitoresas_supervisoresas_ou_auxilia_133=%s,
    escola_info_vicediretora_ou_diretora_adjuntoa_profissionais__134=%s,
    escola_info_orientadora_comunitarioa_ou_assistente_social_135=%s,
    escola_info_tradutor_e_interprete_de_libras_para_atendimento_136=%s,
    escola_info_revisor_de_texto_braille_assistente_vidente_assi_137=%s,
    escola_info_nao_ha_funcionarios_para_as_funcoes_listadas_138=%s,
    escola_info_alimentacao_escolar_para_os_alunoas_139=%s,
    escola_info_acervo_multimidia_140=%s,
    escola_info_brinquedos_para_educacao_infantil_141=%s,
    escola_info_conjunto_de_materiais_cientificos_142=%s,
    escola_info_equipamento_para_amplificacao_e_difusao_de_somau_143=%s,
    escola_info_equipamentos_e_instrumentos_para_atividades_em_a_144=%s,
    escola_info_instrumentos_musicais_para_conjunto_bandafanfarr_145=%s,
    escola_info_jogos_educativos_146=%s,
    escola_info_materiais_para_atividades_culturais_e_artisticas_147=%s,
    escola_info_materiais_para_educacao_profissional_148=%s,
    escola_info_materiais_para_pratica_desportiva_e_recreacao_149=%s,
    escola_info_materiais_pedagogicos_para_a_educacao_bilingue_d_150=%s,
    escola_info_materiais_pedagogicos_para_a_educacao_escolar_in_151=%s,
    escola_info_materiais_pedagogicos_para_a_educacao_das_relaco_152=%s,
    escola_info_materiais_pedagogicos_para_a_educacao_do_campo_153=%s,
    escola_info_materiais_pedagogicos_para_a_educacao_escolar_qu_154=%s,
    escola_info_materiais_pedagogicos_para_a_educacao_especial_155=%s,
    escola_info_nenhum_dos_instrumentos_listados_156=%s,
    escola_info_escola_indigena_157=%s,
    escola_info_lingua_indigena_158=%s,
    escola_info_lingua_portuguesa_159=%s,
    escola_info_codigo_da_lingua_indigena_1_160=%s,
    escola_info_codigo_da_lingua_indigena_2_161=%s,
    escola_info_codigo_da_lingua_indigena_3_162=%s,
    escola_info_a_escola_faz_exame_de_selecao_para_ingresso_de_s_163=%s,
    escola_info_autodeclarado_preto_pardo_ou_indigena_ppi_164=%s,
    escola_info_condicao_de_renda_165=%s,
    escola_info_oriundo_de_escola_publica_166=%s,
    escola_info_pessoa_com_deficiencia_pcd_167=%s,
    escola_info_outros_grupos_que_nao_os_listados_168=%s,
    escola_info_sem_reservas_de_vagas_para_sistema_de_cotas_ampl_169=%s,
    escola_info_a_escola_possui_site_ou_blog_ou_pagina_em_redes__170=%s,
    escola_info_a_escola_compartilha_espacos_para_atividades_de__171=%s,
    escola_info_a_escola_usa_espacos_e_equipamentos_do_entorno_e_172=%s,
    escola_info_associacao_de_pais_173=%s,
    escola_info_associacao_de_pais_e_mestres_174=%s,
    escola_info_conselho_escolar_175=%s,
    escola_info_gremio_estudantil_176=%s,
    escola_info_outros_177_177=%s,
    escola_info_nao_ha_orgaos_colegiados_em_funcionamento_178=%s,
    escola_info_o_projeto_politico_pedagogico_ou_a_proposta_peda_179=%s,
    escola_info_a_escola_desenvolve_acoes_na_area_de_educacao_am_180=%s,
    escola_info_como_conteudo_dos_componentescampos_de_experienc_181=%s,
    escola_info_como_um_componente_curricular_especial_especific_182=%s,
    escola_info_como_um_eixo_estruturante_do_curriculo_183=%s,
    escola_info_em_eventos_184=%s,
    escola_info_em_projetos_transversais_ou_interdisciplinares_185=%s,
    escola_info_nenhuma_das_opcoes_listadas_186=%s
    WHERE escola_id=%s",

    GetSQLValueString($_POST['escola_info_predio_escolar_3'], "text"),
    GetSQLValueString($_POST['escola_info_salas_em_outra_escola_4'], "text"),
    GetSQLValueString($_POST['escola_info_galpao_rancho_paiol_barracao_5'], "text"),
    GetSQLValueString($_POST['escola_info_unidade_de_atendimento_socioeducativa_6'], "text"),
    GetSQLValueString($_POST['escola_info_unidade_prisional_7'], "text"),
    GetSQLValueString($_POST['escola_info_outros_8_8'], "text"),
    GetSQLValueString($_POST['escola_info_forma_de_ocupacao_do_predio_9'], "text"),
    GetSQLValueString($_POST['escola_info_predio_escolar_compartilhado_com_outra_escola_10'], "text"),
    GetSQLValueString($_POST['escola_info_codigo_da_escola_com_a_qual_compartilha_1_11'], "text"),
    GetSQLValueString($_POST['escola_info_codigo_da_escola_com_a_qual_compartilha_2_12'], "text"),
    GetSQLValueString($_POST['escola_info_codigo_da_escola_com_a_qual_compartilha_3_13'], "text"),
    GetSQLValueString($_POST['escola_info_codigo_da_escola_com_a_qual_compartilha_4_14'], "text"),
    GetSQLValueString($_POST['escola_info_codigo_da_escola_com_a_qual_compartilha_5_15'], "text"),
    GetSQLValueString($_POST['escola_info_codigo_da_escola_com_a_qual_compartilha_6_16'], "text"),
    GetSQLValueString($_POST['escola_info_fornece_agua_potavel_para_o_consumo_humano_17'], "text"),
    GetSQLValueString($_POST['escola_info_rede_publica_18'], "text"),
    GetSQLValueString($_POST['escola_info_poco_artesiano_19'], "text"),
    GetSQLValueString($_POST['escola_info_cacimba_cisterna_poco_20'], "text"),
    GetSQLValueString($_POST['escola_info_fonte_rio_igarape_riacho_corrego_21'], "text"),
    GetSQLValueString($_POST['escola_info_carropipa_22'], "text"),
    GetSQLValueString($_POST['escola_info_nao_ha_abastecimento_de_agua_23'], "text"),
    GetSQLValueString($_POST['escola_info_rede_publica_24'], "text"),
    GetSQLValueString($_POST['escola_info_gerador_movido_a_combustivel_fossil_25'], "text"),
    GetSQLValueString($_POST['escola_info_fontes_de_energia_renovaveis_ou_alternativas_ger_26'], "text"),
    GetSQLValueString($_POST['escola_info_nao_ha_energia_eletrica_27'], "text"),
    GetSQLValueString($_POST['escola_info_rede_publica_28'], "text"),
    GetSQLValueString($_POST['escola_info_fossa_septica_29'], "text"),
    GetSQLValueString($_POST['escola_info_fossa_rudimentarcomum_30'], "text"),
    GetSQLValueString($_POST['escola_info_nao_ha_esgotamento_sanitario_31'], "text"),
    GetSQLValueString($_POST['escola_info_servico_de_coleta_32'], "text"),
    GetSQLValueString($_POST['escola_info_queima_33'], "text"),
    GetSQLValueString($_POST['escola_info_enterra_34'], "text"),
    GetSQLValueString($_POST['escola_info_leva_a_uma_destinacao_final_licenciada_pelo_pode_35'], "text"),
    GetSQLValueString($_POST['escola_info_descarta_em_outra_area_36'], "text"),
    GetSQLValueString($_POST['escola_info_separacao_do_lixoresiduos_37'], "text"),
    GetSQLValueString($_POST['escola_info_reaproveitamentoreutilizacao_38'], "text"),
    GetSQLValueString($_POST['escola_info_reciclagem_39'], "text"),
    GetSQLValueString($_POST['escola_info_nao_faz_tratamento_40'], "text"),
    GetSQLValueString($_POST['escola_info_almoxarifado_41'], "text"),
    GetSQLValueString($_POST['escola_info_area_de_vegetacao_ou_gramado_42'], "text"),
    GetSQLValueString($_POST['escola_info_auditorio_43'], "text"),
    GetSQLValueString($_POST['escola_info_banheiro_44'], "text"),
    GetSQLValueString($_POST['escola_info_banheiro_acessivel_adequado_ao_uso_de_pessoas_co_45'], "text"),
    GetSQLValueString($_POST['escola_info_banheiro_adequado_a_educacao_infantil_46'], "text"),
    GetSQLValueString($_POST['escola_info_banheiro_exclusivo_para_os_funcionarios_47'], "text"),
    GetSQLValueString($_POST['escola_info_banheiro_ou_vestiario_com_chuveiro_48'], "text"),
    GetSQLValueString($_POST['escola_info_biblioteca_49'], "text"),
    GetSQLValueString($_POST['escola_info_cozinha_50'], "text"),
    GetSQLValueString($_POST['escola_info_despensa_51'], "text"),
    GetSQLValueString($_POST['escola_info_dormitorio_de_alunoa_52'], "text"),
    GetSQLValueString($_POST['escola_info_dormitorio_de_professora_53'], "text"),
    GetSQLValueString($_POST['escola_info_laboratorio_de_ciencias_54'], "text"),
    GetSQLValueString($_POST['escola_info_laboratorio_de_informatica_55'], "text"),
    GetSQLValueString($_POST['escola_info_laboratorio_especifico_para_a_educacao_profissi_56'], "text"),
    GetSQLValueString($_POST['escola_info_parque_infantil_57'], "text"),
    GetSQLValueString($_POST['escola_info_patio_coberto_58'], "text"),
    GetSQLValueString($_POST['escola_info_patio_descoberto_59'], "text"),
    GetSQLValueString($_POST['escola_info_piscina_60'], "text"),
    GetSQLValueString($_POST['escola_info_quadra_de_esportes_coberta_61'], "text"),
    GetSQLValueString($_POST['escola_info_quadra_de_esportes_descoberta_62'], "text"),
    GetSQLValueString($_POST['escola_info_refeitorio_63'], "text"),
    GetSQLValueString($_POST['escola_info_sala_de_repouso_para_alunoa_64'], "text"),
    GetSQLValueString($_POST['escola_info_salaatelie_de_artes_65'], "text"),
    GetSQLValueString($_POST['escola_info_sala_de_musicacoral_66'], "text"),
    GetSQLValueString($_POST['escola_info_salaestudio_de_danca_67'], "text"),
    GetSQLValueString($_POST['escola_info_sala_multiuso_musica_danca_e_artes_68'], "text"),
    GetSQLValueString($_POST['escola_info_terreirao_area_para_pratica_desportiva_e_recreac_69'], "text"),
    GetSQLValueString($_POST['escola_info_viveirocriacao_de_animais_70'], "text"),
    GetSQLValueString($_POST['escola_info_sala_de_diretoria_71'], "text"),
    GetSQLValueString($_POST['escola_info_sala_de_leitura_72'], "text"),
    GetSQLValueString($_POST['escola_info_sala_de_professores_73'], "text"),
    GetSQLValueString($_POST['escola_info_sala_de_recursos_multifuncionais_para_atendiment_74'], "text"),
    GetSQLValueString($_POST['escola_info_sala_de_secretaria_75'], "text"),
    GetSQLValueString($_POST['escola_info_salas_de_oficinas_da_educacao_profissional_76'], "text"),
    GetSQLValueString($_POST['escola_info_estudio_de_gravacao_e_edicao_77'], "text"),
    GetSQLValueString($_POST['escola_info_area_de_horta_plantio_eou_producao_agricola_78'], "text"),
    GetSQLValueString($_POST['escola_info_nenhuma_das_dependencias_relacionadas_79'], "text"),
    GetSQLValueString($_POST['escola_info_corrimao_e_guardacorpos_80'], "text"),
    GetSQLValueString($_POST['escola_info_elevador_81'], "text"),
    GetSQLValueString($_POST['escola_info_pisos_tateis_82'], "text"),
    GetSQLValueString($_POST['escola_info_portas_com_vao_livre_de_no_minimo_80_cm_83'], "text"),
    GetSQLValueString($_POST['escola_info_rampas_84'], "text"),
    GetSQLValueString($_POST['escola_info_sinalizacaoalarme_luminoso_85'], "text"),
    GetSQLValueString($_POST['escola_info_sinalizacao_sonora_86'], "text"),
    GetSQLValueString($_POST['escola_info_sinalizacao_tatil_87'], "text"),
    GetSQLValueString($_POST['escola_info_sinalizacao_visual_pisoparedes_88'], "text"),
    GetSQLValueString($_POST['escola_info_nenhum_dos_recursos_de_acessibilidade_listados_89'], "text"),
    GetSQLValueString($_POST['escola_info_numero_de_salas_de_aula_utilizadas_na_escola_den_90'], "text"),
    GetSQLValueString($_POST['escola_info_numero_de_salas_de_aula_utilizadas_na_escola_for_91'], "text"),
    GetSQLValueString($_POST['escola_info_numero_de_salas_de_aula_climatizadas_ar_condicio_92'], "text"),
    GetSQLValueString($_POST['escola_info_numero_de_salas_de_aula_com_acessibilidade_para__93'], "text"),
    GetSQLValueString($_POST['escola_info_antena_parabolica_94'], "text"),
    GetSQLValueString($_POST['escola_info_computadores_95'], "text"),
    GetSQLValueString($_POST['escola_info_copiadora_96'], "text"),
    GetSQLValueString($_POST['escola_info_impressora_97'], "text"),
    GetSQLValueString($_POST['escola_info_impressora_multifuncional_98'], "text"),
    GetSQLValueString($_POST['escola_info_scanner_99'], "text"),
    GetSQLValueString($_POST['escola_info_nenhum_dos_equipamentos_listados_100'], "text"),
    GetSQLValueString($_POST['escola_info_aparelho_de_dvdbluray_101'], "text"),
    GetSQLValueString($_POST['escola_info_aparelho_de_som_102'], "text"),
    GetSQLValueString($_POST['escola_info_aparelho_de_televisao_103'], "text"),
    GetSQLValueString($_POST['escola_info_lousa_digital_104'], "text"),
    GetSQLValueString($_POST['escola_info_projetor_multimidia_data_show_105'], "text"),
    GetSQLValueString($_POST['escola_info_computadores_de_mesa_desktop_106'], "text"),
    GetSQLValueString($_POST['escola_info_computadores_portateis_107'], "text"),
    GetSQLValueString($_POST['escola_info_tablets_108'], "text"),
    GetSQLValueString($_POST['escola_info_para_uso_administrativo_109'], "text"),
    GetSQLValueString($_POST['escola_info_para_uso_no_processo_de_ensino_e_aprendizagem_110'], "text"),
    GetSQLValueString($_POST['escola_info_para_uso_dos_alunoas_111'], "text"),
    GetSQLValueString($_POST['escola_info_para_uso_da_comunidade_112'], "text"),
    GetSQLValueString($_POST['escola_info_nao_possui_acesso_a_internet_113'], "text"),
    GetSQLValueString($_POST['escola_info_computadores_de_mesa_portateis_e_tablets_da_esco_114'], "text"),
    GetSQLValueString($_POST['escola_info_dispositivos_pessoais_computadores_portateis_cel_115'], "text"),
    GetSQLValueString($_POST['escola_info_internet_banda_larga_116'], "text"),
    GetSQLValueString($_POST['escola_info_a_cabo_117'], "text"),
    GetSQLValueString($_POST['escola_info_wireless_118'], "text"),
    GetSQLValueString($_POST['escola_info_nao_ha_rede_local_interligando_computadores_119'], "text"),
    GetSQLValueString($_POST['escola_info_agronomosas_horticultoresas_tecnicos_ou_monitore_120'], "text"),
    GetSQLValueString($_POST['escola_info_auxiliares_de_secretaria_ou_auxiliares_administr_121'], "text"),
    GetSQLValueString($_POST['escola_info_auxiliar_de_servicos_gerais_porteiroa_zeladora_f_122'], "text"),
    GetSQLValueString($_POST['escola_info_bibliotecarioa_auxiliar_de_biblioteca_ou_monitor_123'], "text"),
    GetSQLValueString($_POST['escola_info_bombeiroa_brigadista_profissionais_de_assistenci_124'], "text"),
    GetSQLValueString($_POST['escola_info_coordenadora_de_turnodisciplinar_125'], "text"),
    GetSQLValueString($_POST['escola_info_fonoaudiologoa_126'], "text"),
    GetSQLValueString($_POST['escola_info_nutricionista_127'], "text"),
    GetSQLValueString($_POST['escola_info_psicologoa_escolar_128'], "text"),
    GetSQLValueString($_POST['escola_info_profissionais_de_preparacao_e_seguranca_alimenta_129'], "text"),
    GetSQLValueString($_POST['escola_info_profissionais_de_apoio_e_supervisao_pedagogica_p_130'], "text"),
    GetSQLValueString($_POST['escola_info_secretarioa_escolar_131'], "text"),
    GetSQLValueString($_POST['escola_info_seguranca_guarda_ou_seguranca_patrimonial_132'], "text"),
    GetSQLValueString($_POST['escola_info_tecnicosas_monitoresas_supervisoresas_ou_auxilia_133'], "text"),
    GetSQLValueString($_POST['escola_info_vicediretora_ou_diretora_adjuntoa_profissionais__134'], "text"),
    GetSQLValueString($_POST['escola_info_orientadora_comunitarioa_ou_assistente_social_135'], "text"),
    GetSQLValueString($_POST['escola_info_tradutor_e_interprete_de_libras_para_atendimento_136'], "text"),
    GetSQLValueString($_POST['escola_info_revisor_de_texto_braille_assistente_vidente_assi_137'], "text"),
    GetSQLValueString($_POST['escola_info_nao_ha_funcionarios_para_as_funcoes_listadas_138'], "text"),
    GetSQLValueString($_POST['escola_info_alimentacao_escolar_para_os_alunoas_139'], "text"),
    GetSQLValueString($_POST['escola_info_acervo_multimidia_140'], "text"),
    GetSQLValueString($_POST['escola_info_brinquedos_para_educacao_infantil_141'], "text"),
    GetSQLValueString($_POST['escola_info_conjunto_de_materiais_cientificos_142'], "text"),
    GetSQLValueString($_POST['escola_info_equipamento_para_amplificacao_e_difusao_de_somau_143'], "text"),
    GetSQLValueString($_POST['escola_info_equipamentos_e_instrumentos_para_atividades_em_a_144'], "text"),
    GetSQLValueString($_POST['escola_info_instrumentos_musicais_para_conjunto_bandafanfarr_145'], "text"),
    GetSQLValueString($_POST['escola_info_jogos_educativos_146'], "text"),
    GetSQLValueString($_POST['escola_info_materiais_para_atividades_culturais_e_artisticas_147'], "text"),
    GetSQLValueString($_POST['escola_info_materiais_para_educacao_profissional_148'], "text"),
    GetSQLValueString($_POST['escola_info_materiais_para_pratica_desportiva_e_recreacao_149'], "text"),
    GetSQLValueString($_POST['escola_info_materiais_pedagogicos_para_a_educacao_bilingue_d_150'], "text"),
    GetSQLValueString($_POST['escola_info_materiais_pedagogicos_para_a_educacao_escolar_in_151'], "text"),
    GetSQLValueString($_POST['escola_info_materiais_pedagogicos_para_a_educacao_das_relaco_152'], "text"),
    GetSQLValueString($_POST['escola_info_materiais_pedagogicos_para_a_educacao_do_campo_153'], "text"),
    GetSQLValueString($_POST['escola_info_materiais_pedagogicos_para_a_educacao_escolar_qu_154'], "text"),
    GetSQLValueString($_POST['escola_info_materiais_pedagogicos_para_a_educacao_especial_155'], "text"),
    GetSQLValueString($_POST['escola_info_nenhum_dos_instrumentos_listados_156'], "text"),
    GetSQLValueString($_POST['escola_info_escola_indigena_157'], "text"),
    GetSQLValueString($_POST['escola_info_lingua_indigena_158'], "text"),
    GetSQLValueString($_POST['escola_info_lingua_portuguesa_159'], "text"),
    GetSQLValueString($_POST['escola_info_codigo_da_lingua_indigena_1_160'], "text"),
    GetSQLValueString($_POST['escola_info_codigo_da_lingua_indigena_2_161'], "text"),
    GetSQLValueString($_POST['escola_info_codigo_da_lingua_indigena_3_162'], "text"),
    GetSQLValueString($_POST['escola_info_a_escola_faz_exame_de_selecao_para_ingresso_de_s_163'], "text"),
    GetSQLValueString($_POST['escola_info_autodeclarado_preto_pardo_ou_indigena_ppi_164'], "text"),
    GetSQLValueString($_POST['escola_info_condicao_de_renda_165'], "text"),
    GetSQLValueString($_POST['escola_info_oriundo_de_escola_publica_166'], "text"),
    GetSQLValueString($_POST['escola_info_pessoa_com_deficiencia_pcd_167'], "text"),
    GetSQLValueString($_POST['escola_info_outros_grupos_que_nao_os_listados_168'], "text"),
    GetSQLValueString($_POST['escola_info_sem_reservas_de_vagas_para_sistema_de_cotas_ampl_169'], "text"),
    GetSQLValueString($_POST['escola_info_a_escola_possui_site_ou_blog_ou_pagina_em_redes__170'], "text"),
    GetSQLValueString($_POST['escola_info_a_escola_compartilha_espacos_para_atividades_de__171'], "text"),
    GetSQLValueString($_POST['escola_info_a_escola_usa_espacos_e_equipamentos_do_entorno_e_172'], "text"),
    GetSQLValueString($_POST['escola_info_associacao_de_pais_173'], "text"),
    GetSQLValueString($_POST['escola_info_associacao_de_pais_e_mestres_174'], "text"),
    GetSQLValueString($_POST['escola_info_conselho_escolar_175'], "text"),
    GetSQLValueString($_POST['escola_info_gremio_estudantil_176'], "text"),
    GetSQLValueString($_POST['escola_info_outros_177_177'], "text"),
    GetSQLValueString($_POST['escola_info_nao_ha_orgaos_colegiados_em_funcionamento_178'], "text"),
    GetSQLValueString($_POST['escola_info_o_projeto_politico_pedagogico_ou_a_proposta_peda_179'], "text"),
    GetSQLValueString($_POST['escola_info_a_escola_desenvolve_acoes_na_area_de_educacao_am_180'], "text"),
    GetSQLValueString($_POST['escola_info_como_conteudo_dos_componentescampos_de_experienc_181'], "text"),
    GetSQLValueString($_POST['escola_info_como_um_componente_curricular_especial_especific_182'], "text"),
    GetSQLValueString($_POST['escola_info_como_um_eixo_estruturante_do_curriculo_183'], "text"),
    GetSQLValueString($_POST['escola_info_em_eventos_184'], "text"),
    GetSQLValueString($_POST['escola_info_em_projetos_transversais_ou_interdisciplinares_185'], "text"),
    GetSQLValueString($_POST['escola_info_nenhuma_das_opcoes_listadas_186'], "text"),
    GetSQLValueString($_POST['escola_id'], "int")
  );

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());
  
    
$usu = $_POST['usu_id'];
$esc = $_POST['escola_id'];
date_default_timezone_set('America/Bahia');
$dat = date('Y-m-d H:i:s');

$sql = "
INSERT INTO smc_registros (
registros_id_escola, 
registros_id_usuario, 
registros_tipo, 
registros_complemento, 
registros_data_hora
) VALUES (
'$esc', 
'$usu', 
'1', 
'', 
'$dat')
";
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$Result1 = mysql_query($sql, $SmecelNovo) or die(mysql_error());
  
  

  $updateGoTo = "secretaria_dados_escola.php?atualizado";
  if (isset($_SERVER['QUERY_STRING'])) {
    //$updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    //$updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}


	
//CADASTRO DA LOGO

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_distritos = "SELECT * FROM smc_municipio_distritos WHERE distrito_cod_ibge_mun = '$row_EscolaLogada[sec_ibge_municipio]'";
$distritos = mysql_query($query_distritos, $SmecelNovo) or die(mysql_error());
$row_distritos = mysql_fetch_assoc($distritos);
$totalRows_distritos = mysql_num_rows($distritos);

?>
<!DOCTYPE html>
<html class="<?php echo $row_EscolaLogada['escola_tema']; ?>" lang="pt-br">
<head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-117872281-1');
</script>
<title>SMECEL - Sistema de Gestão Escolar</title>
<meta charset="utf-8">
<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<meta name="description" content="">
<meta name="keywords" content="">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<link rel="stylesheet" type="text/css" href="css/locastyle.css"><link rel="stylesheet" type="text/css" href="css/preloader.css">
<script src="js/locastyle.js"></script>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body>
<?php include_once ("menu-top.php"); ?>
<?php include_once ("menu-esc.php"); ?>
<main class="ls-main ">
  <div class="container-fluid">
     
    <h1 class="ls-title-intro ls-ico-home">Dados da Escola/Setor</h1>

    <?php if (isset($_GET["contato"])) { ?>
      <div class="ls-alert-danger ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
      
                  PREENCHA OS DADOS DE CONTATO DA SUA ESCOLA. <br><br>Telefone e E-mail<br><br>Obs.: Se a escola não possuir dados de contato, informe os dados de contato do(a) diretor(a) ou responsável. 
    </div>

    <?php } ?>  

    <?php if (isset($_GET["atualizado"])) { ?>
      <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> Dados atualizados com sucesso. </div>
      <?php } ?>
	                <?php if (isset($_GET["permissao"])) { ?>
                <div class="ls-alert-danger ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  VOCÊ NÃO TEM PERMISSÃO PARA REALIZAR ESTA AÇÃO.
                </div>
              <?php } ?>
			  


			  
    <div class="row">
      <div class="col-sm-12">
        <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal row">
        
      
<label class="ls-label col-md-12">
<h4 class="ls-title-4 ls-color-theme ls-box ls-box-gray">3 a 8 - Local de funcionamento da escola</h4></label>


<label class="ls-label col-md-4">
        <b class="ls-label-text">3 - Prédio escolar</b>
        <div class="ls-custom-select">
            <select name="escola_info_predio_escolar_3" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_predio_escolar_3'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_predio_escolar_3'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">4 - Sala(s) em outra escola</b>
        <div class="ls-custom-select">
            <select name="escola_info_salas_em_outra_escola_4" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_salas_em_outra_escola_4'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_salas_em_outra_escola_4'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">5 - Galpão/ rancho/ paiol/ barracão</b>
        <div class="ls-custom-select">
            <select name="escola_info_galpao_rancho_paiol_barracao_5" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_galpao_rancho_paiol_barracao_5'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_galpao_rancho_paiol_barracao_5'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">6 - Unidade de atendimento Socioeducativa</b>
        <div class="ls-custom-select">
            <select name="escola_info_unidade_de_atendimento_socioeducativa_6" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_unidade_de_atendimento_socioeducativa_6'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_unidade_de_atendimento_socioeducativa_6'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">7 - Unidade Prisional</b>
        <div class="ls-custom-select">
            <select name="escola_info_unidade_prisional_7" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_unidade_prisional_7'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_unidade_prisional_7'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">8 - Outros</b>
        <div class="ls-custom-select">
            <select name="escola_info_outros_8_8" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_outros_8_8'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_outros_8_8'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">9 - Forma de ocupação do prédio</b>
        <div class="ls-custom-select">
            <select name="escola_info_forma_de_ocupacao_do_predio_9" class="ls-select">
            <option value=""></option>
            <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_forma_de_ocupacao_do_predio_9'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>PRÓPRIO</option>
            <option value="2" <?php if (!(strcmp("2", htmlentities($row_EscolaLogada['escola_info_forma_de_ocupacao_do_predio_9'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>ALUGADO</option>
            <option value="3" <?php if (!(strcmp("3", htmlentities($row_EscolaLogada['escola_info_forma_de_ocupacao_do_predio_9'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>CEDIDO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">10 - Prédio escolar compartilhado com outra escola</b>
        <div class="ls-custom-select">
            <select name="escola_info_predio_escolar_compartilhado_com_outra_escola_10" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_predio_escolar_compartilhado_com_outra_escola_10'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_predio_escolar_compartilhado_com_outra_escola_10'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>
<label class="ls-label col-md-12">
<h4 class="ls-title-4 ls-color-theme ls-box ls-box-gray">11 a 16 - Código da escola com a qual compartilha</h4></label>

<label class="ls-label col-md-2">
        <b class="ls-label-text">11 - Cód da escola (1)</b>

        <input type="text" name="escola_info_codigo_da_escola_com_a_qual_compartilha_1_11" value="<?php echo $row_EscolaLogada['escola_info_codigo_da_escola_com_a_qual_compartilha_1_11']; ?>" maxLength="8">

    </label>

<label class="ls-label col-md-2">
        <b class="ls-label-text">12 - Cód da escola (2)</b>
        <input type="text" name="escola_info_codigo_da_escola_com_a_qual_compartilha_2_12" value="<?php echo $row_EscolaLogada['escola_info_codigo_da_escola_com_a_qual_compartilha_2_12']; ?>" maxLength="8">

    </label>

<label class="ls-label col-md-2">
        <b class="ls-label-text">13 - Cód da escola (3)</b>
        <input type="text" name="escola_info_codigo_da_escola_com_a_qual_compartilha_3_13" value="<?php echo $row_EscolaLogada['escola_info_codigo_da_escola_com_a_qual_compartilha_3_13']; ?>" maxLength="8">

    </label>

<label class="ls-label col-md-2">
        <b class="ls-label-text">14 - Cód da escola (4)</b>
        
            <input type="text" name="escola_info_codigo_da_escola_com_a_qual_compartilha_4_14" value="<?php echo $row_EscolaLogada['escola_info_codigo_da_escola_com_a_qual_compartilha_4_14']; ?>" maxLength="8">

    </label>

<label class="ls-label col-md-2">
        <b class="ls-label-text">15 - Cód da escola (5)</b>
        <input type="text" name="escola_info_codigo_da_escola_com_a_qual_compartilha_5_15" value="<?php echo $row_EscolaLogada['escola_info_codigo_da_escola_com_a_qual_compartilha_5_15']; ?>" maxLength="8">

    </label>

<label class="ls-label col-md-2">
        <b class="ls-label-text">16 - Cód da escola (6)</b>
        <input type="text" name="escola_info_codigo_da_escola_com_a_qual_compartilha_6_16" value="<?php echo $row_EscolaLogada['escola_info_codigo_da_escola_com_a_qual_compartilha_6_16']; ?>" maxLength="8">

    </label>

<label class="ls-label col-md-12">
<h4 class="ls-title-4 ls-color-theme ls-box ls-box-gray">18 a 23 - Abastecimento de água</h4></label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">17 - Fornece água potável para o consumo humano</b>
        <div class="ls-custom-select">
            <select name="escola_info_fornece_agua_potavel_para_o_consumo_humano_17" class="ls-select">
				<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_fornece_agua_potavel_para_o_consumo_humano_17'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_fornece_agua_potavel_para_o_consumo_humano_17'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">18 - Rede pública</b>
        <div class="ls-custom-select">
            <select name="escola_info_rede_publica_18" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_rede_publica_18'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_rede_publica_18'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">19 - Poço artesiano</b>
        <div class="ls-custom-select">
            <select name="escola_info_poco_artesiano_19" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_poco_artesiano_19'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_poco_artesiano_19'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">20 - Cacimba/ cisterna / poço</b>
        <div class="ls-custom-select">
            <select name="escola_info_cacimba_cisterna_poco_20" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_cacimba_cisterna_poco_20'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_cacimba_cisterna_poco_20'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">21 - Fonte/ rio / igarapé/ riacho/ córrego.</b>
        <div class="ls-custom-select">
            <select name="escola_info_fonte_rio_igarape_riacho_corrego_21" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_fonte_rio_igarape_riacho_corrego_21'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_fonte_rio_igarape_riacho_corrego_21'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">22 - Carro-pipa</b>
        <div class="ls-custom-select">
            <select name="escola_info_carropipa_22" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_carropipa_22'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_carropipa_22'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">23 - Não há abastecimento de água</b>
        <div class="ls-custom-select">
            <select name="escola_info_nao_ha_abastecimento_de_agua_23" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_nao_ha_abastecimento_de_agua_23'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_nao_ha_abastecimento_de_agua_23'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>
<label class="ls-label col-md-12">
<h4 class="ls-title-4 ls-color-theme ls-box ls-box-gray">24 a 27 - Fonte de energia elétrica</h4></label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">24 - Rede pública</b>
        <div class="ls-custom-select">
            <select name="escola_info_rede_publica_24" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_rede_publica_24'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_rede_publica_24'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">25 - Gerador movido a combustível fóssil</b>
        <div class="ls-custom-select">
            <select name="escola_info_gerador_movido_a_combustivel_fossil_25" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_gerador_movido_a_combustivel_fossil_25'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_gerador_movido_a_combustivel_fossil_25'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-7">
        <b class="ls-label-text">26 - Fontes de energia renováveis ou alternativas (gerador a biocombustível e/ou biodigestores, eólica, solar, outras)</b>
        <div class="ls-custom-select">
            <select name="escola_info_fontes_de_energia_renovaveis_ou_alternativas_ger_26" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_fontes_de_energia_renovaveis_ou_alternativas_ger_26'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_fontes_de_energia_renovaveis_ou_alternativas_ger_26'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">27 - Não há energia elétrica</b>
        <div class="ls-custom-select">
            <select name="escola_info_nao_ha_energia_eletrica_27" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_nao_ha_energia_eletrica_27'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_nao_ha_energia_eletrica_27'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-12">
<h4 class="ls-title-4 ls-color-theme ls-box ls-box-gray">28 a 31 - Esgotamento sanitário</h4></label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">28 - Rede pública</b>
        <div class="ls-custom-select">
            <select name="escola_info_rede_publica_28" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_rede_publica_28'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_rede_publica_28'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">29 - Fossa séptica</b>
        <div class="ls-custom-select">
            <select name="escola_info_fossa_septica_29" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_fossa_septica_29'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_fossa_septica_29'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">30 - Fossa rudimentar/comum</b>
        <div class="ls-custom-select">
            <select name="escola_info_fossa_rudimentarcomum_30" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_fossa_rudimentarcomum_30'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_fossa_rudimentarcomum_30'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">31 - Não há esgotamento sanitário</b>
        <div class="ls-custom-select">
            <select name="escola_info_nao_ha_esgotamento_sanitario_31" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_nao_ha_esgotamento_sanitario_31'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_nao_ha_esgotamento_sanitario_31'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-12">
<h4 class="ls-title-4 ls-color-theme ls-box ls-box-gray">32 a 36 - Destinação do lixo</h4></label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">32 - Serviço de coleta</b>
        <div class="ls-custom-select">
            <select name="escola_info_servico_de_coleta_32" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_servico_de_coleta_32'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_servico_de_coleta_32'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">33 - Queima</b>
        <div class="ls-custom-select">
            <select name="escola_info_queima_33" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_queima_33'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_queima_33'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">34 - Enterra</b>
        <div class="ls-custom-select">
            <select name="escola_info_enterra_34" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_enterra_34'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_enterra_34'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">35 - Leva a uma destinação final licenciada pelo poder público</b>
        <div class="ls-custom-select">
            <select name="escola_info_leva_a_uma_destinacao_final_licenciada_pelo_pode_35" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_leva_a_uma_destinacao_final_licenciada_pelo_pode_35'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_leva_a_uma_destinacao_final_licenciada_pelo_pode_35'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">36 - Descarta em outra área</b>
        <div class="ls-custom-select">
            <select name="escola_info_descarta_em_outra_area_36" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_descarta_em_outra_area_36'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_descarta_em_outra_area_36'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-12">
<h4 class="ls-title-4 ls-color-theme ls-box ls-box-gray">37 a 40 - Tratamento do lixo/resíduos que a escola realiza</h4></label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">37 - Separação do lixo/resíduos</b>
        <div class="ls-custom-select">
            <select name="escola_info_separacao_do_lixoresiduos_37" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_separacao_do_lixoresiduos_37'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_separacao_do_lixoresiduos_37'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">38 - Reaproveitamento/reutilização</b>
        <div class="ls-custom-select">
            <select name="escola_info_reaproveitamentoreutilizacao_38" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_reaproveitamentoreutilizacao_38'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_reaproveitamentoreutilizacao_38'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">39 - Reciclagem</b>
        <div class="ls-custom-select">
            <select name="escola_info_reciclagem_39" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_reciclagem_39'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_reciclagem_39'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">40 - Não faz tratamento</b>
        <div class="ls-custom-select">
            <select name="escola_info_nao_faz_tratamento_40" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_nao_faz_tratamento_40'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_nao_faz_tratamento_40'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-12">
<h4 class="ls-title-4 ls-color-theme ls-box ls-box-gray">41 a 79 - Dependências físicas existentes e utilizadas na escola</h4></label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">41 - Almoxarifado</b>
        <div class="ls-custom-select">
            <select name="escola_info_almoxarifado_41" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_almoxarifado_41'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_almoxarifado_41'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">42 - Área de vegetação ou gramado</b>
        <div class="ls-custom-select">
            <select name="escola_info_area_de_vegetacao_ou_gramado_42" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_area_de_vegetacao_ou_gramado_42'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_area_de_vegetacao_ou_gramado_42'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">43 - Auditório</b>
        <div class="ls-custom-select">
            <select name="escola_info_auditorio_43" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_auditorio_43'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_auditorio_43'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">44 - Banheiro</b>
        <div class="ls-custom-select">
            <select name="escola_info_banheiro_44" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_banheiro_44'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_banheiro_44'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-6">
        <b class="ls-label-text">45 - Banheiro acessível adequado ao uso de pessoas com deficiência ou mobilidade reduzida</b>
        <div class="ls-custom-select">
            <select name="escola_info_banheiro_acessivel_adequado_ao_uso_de_pessoas_co_45" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_banheiro_acessivel_adequado_ao_uso_de_pessoas_co_45'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_banheiro_acessivel_adequado_ao_uso_de_pessoas_co_45'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">46 - Banheiro adequado à educação infantil</b>
        <div class="ls-custom-select">
            <select name="escola_info_banheiro_adequado_a_educacao_infantil_46" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_banheiro_adequado_a_educacao_infantil_46'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_banheiro_adequado_a_educacao_infantil_46'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">47 - Banheiro exclusivo para os funcionários</b>
        <div class="ls-custom-select">
            <select name="escola_info_banheiro_exclusivo_para_os_funcionarios_47" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_banheiro_exclusivo_para_os_funcionarios_47'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_banheiro_exclusivo_para_os_funcionarios_47'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">48 - Banheiro ou vestiário com chuveiro</b>
        <div class="ls-custom-select">
            <select name="escola_info_banheiro_ou_vestiario_com_chuveiro_48" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_banheiro_ou_vestiario_com_chuveiro_48'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_banheiro_ou_vestiario_com_chuveiro_48'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">49 - Biblioteca</b>
        <div class="ls-custom-select">
            <select name="escola_info_biblioteca_49" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_biblioteca_49'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_biblioteca_49'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">50 - Cozinha</b>
        <div class="ls-custom-select">
            <select name="escola_info_cozinha_50" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_cozinha_50'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_cozinha_50'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">51 - Despensa</b>
        <div class="ls-custom-select">
            <select name="escola_info_despensa_51" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_despensa_51'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_despensa_51'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">52 - Dormitório de aluno(a)</b>
        <div class="ls-custom-select">
            <select name="escola_info_dormitorio_de_alunoa_52" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_dormitorio_de_alunoa_52'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_dormitorio_de_alunoa_52'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">53 - Dormitório de professor(a)</b>
        <div class="ls-custom-select">
            <select name="escola_info_dormitorio_de_professora_53" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_dormitorio_de_professora_53'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_dormitorio_de_professora_53'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">54 - Laboratório de ciências</b>
        <div class="ls-custom-select">
            <select name="escola_info_laboratorio_de_ciencias_54" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_laboratorio_de_ciencias_54'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_laboratorio_de_ciencias_54'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">55 - Laboratório de informática</b>
        <div class="ls-custom-select">
            <select name="escola_info_laboratorio_de_informatica_55" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_laboratorio_de_informatica_55'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_laboratorio_de_informatica_55'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">56 - Laboratório específico para a educação profissional</b>
        <div class="ls-custom-select">
            <select name="escola_info_laboratorio_especifico_para_a_educacao_profissi_56" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_laboratorio_especifico_para_a_educacao_profissi_56'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_laboratorio_especifico_para_a_educacao_profissi_56'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">57 - Parque infantil</b>
        <div class="ls-custom-select">
            <select name="escola_info_parque_infantil_57" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_parque_infantil_57'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_parque_infantil_57'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">58 - Pátio coberto</b>
        <div class="ls-custom-select">
            <select name="escola_info_patio_coberto_58" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_patio_coberto_58'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_patio_coberto_58'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">59 - Pátio descoberto</b>
        <div class="ls-custom-select">
            <select name="escola_info_patio_descoberto_59" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_patio_descoberto_59'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_patio_descoberto_59'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">60 - Piscina</b>
        <div class="ls-custom-select">
            <select name="escola_info_piscina_60" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_piscina_60'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_piscina_60'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">61 - Quadra de esportes coberta</b>
        <div class="ls-custom-select">
            <select name="escola_info_quadra_de_esportes_coberta_61" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_quadra_de_esportes_coberta_61'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_quadra_de_esportes_coberta_61'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">62 - Quadra de esportes descoberta</b>
        <div class="ls-custom-select">
            <select name="escola_info_quadra_de_esportes_descoberta_62" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_quadra_de_esportes_descoberta_62'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_quadra_de_esportes_descoberta_62'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">63 - Refeitório</b>
        <div class="ls-custom-select">
            <select name="escola_info_refeitorio_63" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_refeitorio_63'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_refeitorio_63'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">64 - Sala de repouso para aluno(a)</b>
        <div class="ls-custom-select">
            <select name="escola_info_sala_de_repouso_para_alunoa_64" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_sala_de_repouso_para_alunoa_64'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_sala_de_repouso_para_alunoa_64'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">65 - Sala/ateliê de artes</b>
        <div class="ls-custom-select">
            <select name="escola_info_salaatelie_de_artes_65" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_salaatelie_de_artes_65'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_salaatelie_de_artes_65'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">66 - Sala de música/coral</b>
        <div class="ls-custom-select">
            <select name="escola_info_sala_de_musicacoral_66" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_sala_de_musicacoral_66'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_sala_de_musicacoral_66'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">67 - Sala/estúdio de dança</b>
        <div class="ls-custom-select">
            <select name="escola_info_salaestudio_de_danca_67" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_salaestudio_de_danca_67'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_salaestudio_de_danca_67'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">68 - Sala multiuso (música, dança e artes)</b>
        <div class="ls-custom-select">
            <select name="escola_info_sala_multiuso_musica_danca_e_artes_68" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_sala_multiuso_musica_danca_e_artes_68'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_sala_multiuso_musica_danca_e_artes_68'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-7">
        <b class="ls-label-text">69 - Terreirão (área para prática desportiva e recreação sem cobertura, sem piso e sem edificações)</b>
        <div class="ls-custom-select">
            <select name="escola_info_terreirao_area_para_pratica_desportiva_e_recreac_69" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_terreirao_area_para_pratica_desportiva_e_recreac_69'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_terreirao_area_para_pratica_desportiva_e_recreac_69'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">70 - Viveiro/criação de animais</b>
        <div class="ls-custom-select">
            <select name="escola_info_viveirocriacao_de_animais_70" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_viveirocriacao_de_animais_70'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_viveirocriacao_de_animais_70'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">71 - Sala de diretoria</b>
        <div class="ls-custom-select">
            <select name="escola_info_sala_de_diretoria_71" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_sala_de_diretoria_71'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_sala_de_diretoria_71'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">72 - Sala de Leitura</b>
        <div class="ls-custom-select">
            <select name="escola_info_sala_de_leitura_72" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_sala_de_leitura_72'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_sala_de_leitura_72'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">73 - Sala de professores</b>
        <div class="ls-custom-select">
            <select name="escola_info_sala_de_professores_73" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_sala_de_professores_73'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_sala_de_professores_73'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-6">
        <b class="ls-label-text">74 - Sala de recursos multifuncionais para atendimento educacional especializado (AEE)</b>
        <div class="ls-custom-select">
            <select name="escola_info_sala_de_recursos_multifuncionais_para_atendiment_74" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_sala_de_recursos_multifuncionais_para_atendiment_74'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_sala_de_recursos_multifuncionais_para_atendiment_74'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">75 - Sala de Secretaria</b>
        <div class="ls-custom-select">
            <select name="escola_info_sala_de_secretaria_75" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_sala_de_secretaria_75'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_sala_de_secretaria_75'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">76 - Salas de oficinas da educação profissional</b>
        <div class="ls-custom-select">
            <select name="escola_info_salas_de_oficinas_da_educacao_profissional_76" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_salas_de_oficinas_da_educacao_profissional_76'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_salas_de_oficinas_da_educacao_profissional_76'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">77 - Estúdio de gravação e edição</b>
        <div class="ls-custom-select">
            <select name="escola_info_estudio_de_gravacao_e_edicao_77" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_estudio_de_gravacao_e_edicao_77'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_estudio_de_gravacao_e_edicao_77'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">78 - Área de horta, plantio e/ou produção agrícola</b>
        <div class="ls-custom-select">
            <select name="escola_info_area_de_horta_plantio_eou_producao_agricola_78" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_area_de_horta_plantio_eou_producao_agricola_78'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_area_de_horta_plantio_eou_producao_agricola_78'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">79 - Nenhuma das dependências relacionadas</b>
        <div class="ls-custom-select">
            <select name="escola_info_nenhuma_das_dependencias_relacionadas_79" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_nenhuma_das_dependencias_relacionadas_79'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_nenhuma_das_dependencias_relacionadas_79'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-12">
<h4 class="ls-title-4 ls-color-theme ls-box ls-box-gray">80 a 89 - Recursos de acessibilidade para pessoas com deficiência ou mobilidade reduzida nas vias de circulação internas na escola</h4></label>


<label class="ls-label col-md-4">
        <b class="ls-label-text">80 - Corrimão e guarda-corpos</b>
        <div class="ls-custom-select">
            <select name="escola_info_corrimao_e_guardacorpos_80" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_corrimao_e_guardacorpos_80'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_corrimao_e_guardacorpos_80'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">81 - Elevador</b>
        <div class="ls-custom-select">
            <select name="escola_info_elevador_81" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_elevador_81'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_elevador_81'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">82 - Pisos táteis</b>
        <div class="ls-custom-select">
            <select name="escola_info_pisos_tateis_82" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_pisos_tateis_82'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_pisos_tateis_82'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">83 - Portas com vão livre de no mínimo 80 cm</b>
        <div class="ls-custom-select">
            <select name="escola_info_portas_com_vao_livre_de_no_minimo_80_cm_83" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_portas_com_vao_livre_de_no_minimo_80_cm_83'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_portas_com_vao_livre_de_no_minimo_80_cm_83'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">84 - Rampas</b>
        <div class="ls-custom-select">
            <select name="escola_info_rampas_84" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_rampas_84'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_rampas_84'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">85 - Sinalização/alarme luminoso</b>
        <div class="ls-custom-select">
            <select name="escola_info_sinalizacaoalarme_luminoso_85" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_sinalizacaoalarme_luminoso_85'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_sinalizacaoalarme_luminoso_85'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">86 - Sinalização sonora</b>
        <div class="ls-custom-select">
            <select name="escola_info_sinalizacao_sonora_86" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_sinalizacao_sonora_86'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_sinalizacao_sonora_86'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">87 - Sinalização tátil</b>
        <div class="ls-custom-select">
            <select name="escola_info_sinalizacao_tatil_87" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_sinalizacao_tatil_87'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_sinalizacao_tatil_87'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">88 - Sinalização visual (piso/paredes)</b>
        <div class="ls-custom-select">
            <select name="escola_info_sinalizacao_visual_pisoparedes_88" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_sinalizacao_visual_pisoparedes_88'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_sinalizacao_visual_pisoparedes_88'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">89 - Nenhum dos recursos de acessibilidade listados</b>
        <div class="ls-custom-select">
            <select name="escola_info_nenhum_dos_recursos_de_acessibilidade_listados_89" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_nenhum_dos_recursos_de_acessibilidade_listados_89'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_nenhum_dos_recursos_de_acessibilidade_listados_89'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-5">
        <b class="ls-label-text">90 - Número de salas de aula utilizadas na escola dentro do prédio escolar</b>

        <input type="text" name="escola_info_numero_de_salas_de_aula_utilizadas_na_escola_den_90" value="<?php echo $row_EscolaLogada['escola_info_numero_de_salas_de_aula_utilizadas_na_escola_den_90']; ?>">

    </label>

<label class="ls-label col-md-5">
        <b class="ls-label-text">91 - Número de salas de aula utilizadas na escola fora do prédio escolar</b>
		<input type="text" name="escola_info_numero_de_salas_de_aula_utilizadas_na_escola_for_91" value="<?php echo $row_EscolaLogada['escola_info_numero_de_salas_de_aula_utilizadas_na_escola_for_91']; ?>">
    </label>

<label class="ls-label col-md-6">
        <b class="ls-label-text">92 - Número de salas de aula climatizadas (ar condicionado, aquecedor ou climatizador)</b>
		<input type="text" name="escola_info_numero_de_salas_de_aula_climatizadas_ar_condicio_92" value="<?php echo $row_EscolaLogada['escola_info_numero_de_salas_de_aula_climatizadas_ar_condicio_92']; ?>">

    </label>

<label class="ls-label col-md-6">
        <b class="ls-label-text">93 - Número de salas de aula com acessibilidade para pessoas com deficiência ou mobilidade reduzida</b>
		<input type="text" name="escola_info_numero_de_salas_de_aula_com_acessibilidade_para__93" value="<?php echo $row_EscolaLogada['escola_info_numero_de_salas_de_aula_com_acessibilidade_para__93']; ?>">
    </label>

<label class="ls-label col-md-12">
<h4 class="ls-title-4 ls-color-theme ls-box ls-box-gray">94 a 100 - Equipamentos existentes na escola para uso técnico e administrativo</h4></label>


<label class="ls-label col-md-4">
        <b class="ls-label-text">94 - Antena parabólica</b>
        <div class="ls-custom-select">
            <select name="escola_info_antena_parabolica_94" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_antena_parabolica_94'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_antena_parabolica_94'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">95 - Computadores</b>
        <div class="ls-custom-select">
            <select name="escola_info_computadores_95" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_computadores_95'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_computadores_95'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">96 - Copiadora</b>
        <div class="ls-custom-select">
            <select name="escola_info_copiadora_96" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_copiadora_96'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_copiadora_96'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">97 - Impressora</b>
        <div class="ls-custom-select">
            <select name="escola_info_impressora_97" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_impressora_97'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_impressora_97'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">98 - Impressora Multifuncional</b>
        <div class="ls-custom-select">
            <select name="escola_info_impressora_multifuncional_98" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_impressora_multifuncional_98'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_impressora_multifuncional_98'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">99 - Scanner</b>
        <div class="ls-custom-select">
            <select name="escola_info_scanner_99" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_scanner_99'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_scanner_99'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">100 - Nenhum dos equipamentos listados</b>
        <div class="ls-custom-select">
            <select name="escola_info_nenhum_dos_equipamentos_listados_100" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_nenhum_dos_equipamentos_listados_100'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_nenhum_dos_equipamentos_listados_100'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-12">
<h4 class="ls-title-4 ls-color-theme ls-box ls-box-gray">101 a 105 - Quantidade de equipamentos para o processo ensino aprendizagem</h4></label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">101 - Aparelho de DVD/Blu-ray</b>
		<input type="text" name="escola_info_aparelho_de_dvdbluray_101" value="<?php echo $row_EscolaLogada['escola_info_aparelho_de_dvdbluray_101']; ?>">

    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">102 - Aparelho de som</b>
		<input type="text" name="escola_info_aparelho_de_som_102" value="<?php echo $row_EscolaLogada['escola_info_aparelho_de_som_102']; ?>">

    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">103 - Aparelho de Televisão</b>
		<input type="text" name="escola_info_aparelho_de_televisao_103" value="<?php echo $row_EscolaLogada['escola_info_aparelho_de_televisao_103']; ?>">
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">104 - Lousa digital</b>
		<input type="text" name="escola_info_lousa_digital_104" value="<?php echo $row_EscolaLogada['escola_info_lousa_digital_104']; ?>">
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">105 - Projetor Multimídia (Data show)</b>
		<input type="text" name="escola_info_projetor_multimidia_data_show_105" value="<?php echo $row_EscolaLogada['escola_info_projetor_multimidia_data_show_105']; ?>">
    </label>

<label class="ls-label col-md-12">
<h4 class="ls-title-4 ls-color-theme ls-box ls-box-gray">106 a 108 - Quantidade de computadores em uso pelos alunos</h4></label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">106 - Computadores de mesa (desktop)</b>
		<input type="text" name="escola_info_computadores_de_mesa_desktop_106" value="<?php echo $row_EscolaLogada['escola_info_computadores_de_mesa_desktop_106']; ?>">
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">107 - Computadores portáteis</b>
		<input type="text" name="escola_info_computadores_portateis_107" value="<?php echo $row_EscolaLogada['escola_info_computadores_portateis_107']; ?>">
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">108 - Tablets</b>
		<input type="text" name="escola_info_tablets_108" value="<?php echo $row_EscolaLogada['escola_info_tablets_108']; ?>">
    </label>

<label class="ls-label col-md-12">
<h4 class="ls-title-4 ls-color-theme ls-box ls-box-gray">109 a 113 - Acesso à internet</h4></label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">109 - Para uso administrativo</b>
        <div class="ls-custom-select">
            <select name="escola_info_para_uso_administrativo_109" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_para_uso_administrativo_109'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_para_uso_administrativo_109'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">110 - Para uso no processo de ensino e aprendizagem</b>
        <div class="ls-custom-select">
            <select name="escola_info_para_uso_no_processo_de_ensino_e_aprendizagem_110" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_para_uso_no_processo_de_ensino_e_aprendizagem_110'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_para_uso_no_processo_de_ensino_e_aprendizagem_110'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">111 - Para uso dos aluno(a)s</b>
        <div class="ls-custom-select">
            <select name="escola_info_para_uso_dos_alunoas_111" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_para_uso_dos_alunoas_111'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_para_uso_dos_alunoas_111'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">112 - Para uso da comunidade</b>
        <div class="ls-custom-select">
            <select name="escola_info_para_uso_da_comunidade_112" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_para_uso_da_comunidade_112'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_para_uso_da_comunidade_112'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">113 - Não possui acesso à internet</b>
        <div class="ls-custom-select">
            <select name="escola_info_nao_possui_acesso_a_internet_113" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_nao_possui_acesso_a_internet_113'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_nao_possui_acesso_a_internet_113'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-12">
<h4 class="ls-title-4 ls-color-theme ls-box ls-box-gray">114 a 115 - Equipamentos que os alunos usam para acessar a internet da escola</h4></label>

<label class="ls-label col-md-8">
        <b class="ls-label-text">114 - Computadores de mesa, portáteis e tablets da escola (no laboratório de informática, biblioteca, sala de aula etc.)</b>
        <div class="ls-custom-select">
            <select name="escola_info_computadores_de_mesa_portateis_e_tablets_da_esco_114" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_computadores_de_mesa_portateis_e_tablets_da_esco_114'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_computadores_de_mesa_portateis_e_tablets_da_esco_114'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-6">
        <b class="ls-label-text">115 - Dispositivos pessoais (computadores portáteis, celulares, tablets etc.)</b>
        <div class="ls-custom-select">
            <select name="escola_info_dispositivos_pessoais_computadores_portateis_cel_115" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_dispositivos_pessoais_computadores_portateis_cel_115'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_dispositivos_pessoais_computadores_portateis_cel_115'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">116 - Internet banda larga</b>
        <div class="ls-custom-select">
            <select name="escola_info_internet_banda_larga_116" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_internet_banda_larga_116'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_internet_banda_larga_116'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-12">
<h4 class="ls-title-4 ls-color-theme ls-box ls-box-gray">117 a 119 - Rede local de interligação de computadores</h4></label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">117 - A cabo</b>
        <div class="ls-custom-select">
            <select name="escola_info_a_cabo_117" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_a_cabo_117'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_a_cabo_117'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">118 - Wireless</b>
        <div class="ls-custom-select">
            <select name="escola_info_wireless_118" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_wireless_118'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_wireless_118'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">119 - Não há rede local interligando computadores</b>
        <div class="ls-custom-select">
            <select name="escola_info_nao_ha_rede_local_interligando_computadores_119" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_nao_ha_rede_local_interligando_computadores_119'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_nao_ha_rede_local_interligando_computadores_119'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-12">
<h4 class="ls-title-4 ls-color-theme ls-box ls-box-gray">120 a 138 - Total de profissionais que atuam nas seguintes funções na escola</h4></label>

<label class="ls-label col-md-8">
        <b class="ls-label-text">120 - Agrônomos(as), horticultores(as), técnicos ou monitores(as) responsáveis pela gestão da área de horta, plantio e/ou produção agrícola</b>
		<input type="text" name="escola_info_agronomosas_horticultoresas_tecnicos_ou_monitore_120" value="<?php echo $row_EscolaLogada['escola_info_agronomosas_horticultoresas_tecnicos_ou_monitore_120']; ?>">

    </label>

<label class="ls-label col-md-5">
        <b class="ls-label-text">121 - Auxiliares de secretaria ou auxiliares administrativos, atendentes</b>
		<input type="text" name="escola_info_auxiliares_de_secretaria_ou_auxiliares_administr_121" value="<?php echo $row_EscolaLogada['escola_info_auxiliares_de_secretaria_ou_auxiliares_administr_121']; ?>">

    </label>

<label class="ls-label col-md-5">
        <b class="ls-label-text">122 - Auxiliar de serviços gerais, porteiro(a), zelador(a), faxineiro(a), jardineiro(a)</b>
		<input type="text" name="escola_info_auxiliar_de_servicos_gerais_porteiroa_zeladora_f_122" value="<?php echo $row_EscolaLogada['escola_info_auxiliar_de_servicos_gerais_porteiroa_zeladora_f_122']; ?>">

    </label>

<label class="ls-label col-md-5">
        <b class="ls-label-text">123 - Bibliotecário(a), auxiliar de biblioteca ou monitor(a) da sala de leitura</b>
		<input type="text" name="escola_info_bibliotecarioa_auxiliar_de_biblioteca_ou_monitor_123" value="<?php echo $row_EscolaLogada['escola_info_bibliotecarioa_auxiliar_de_biblioteca_ou_monitor_123']; ?>">
		

    </label>

<label class="ls-label col-md-9">
        <b class="ls-label-text">124 - Bombeiro(a) brigadista, profissionais de assistência a saúde (urgência e emergência), enfermeiro(a), técnico(a) de enfermagem e socorrista</b>
		<input type="text" name="escola_info_bombeiroa_brigadista_profissionais_de_assistenci_124" value="<?php echo $row_EscolaLogada['escola_info_bombeiroa_brigadista_profissionais_de_assistenci_124']; ?>">
		
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">125 - Coordenador(a) de turno/disciplinar</b>
		<input type="text" name="escola_info_coordenadora_de_turnodisciplinar_125" value="<?php echo $row_EscolaLogada['escola_info_coordenadora_de_turnodisciplinar_125']; ?>">
		
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">126 - Fonoaudiólogo(a)</b>
		<input type="text" name="escola_info_fonoaudiologoa_126" value="<?php echo $row_EscolaLogada['escola_info_fonoaudiologoa_126']; ?>">
		

    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">127 - Nutricionista</b>
		<input type="text" name="escola_info_nutricionista_127" value="<?php echo $row_EscolaLogada['escola_info_nutricionista_127']; ?>">
		

    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">128 - Psicólogo(a) escolar</b>
		<input type="text" name="escola_info_psicologoa_escolar_128" value="<?php echo $row_EscolaLogada['escola_info_psicologoa_escolar_128']; ?>">

    </label>

<label class="ls-label col-md-7">
        <b class="ls-label-text">129 - Profissionais de preparação e segurança alimentar, cozinheiro(a), merendeira e auxiliar de cozinha</b>
		<input type="text" name="escola_info_profissionais_de_preparacao_e_seguranca_alimenta_129" value="<?php echo $row_EscolaLogada['escola_info_profissionais_de_preparacao_e_seguranca_alimenta_129']; ?>">

    </label>

<label class="ls-label col-md-12">
        <b class="ls-label-text">130 - Profissionais de apoio e supervisão pedagógica: (pedagogo(a), coordenador(a) pedagógico(a), orientador(a) educacional, supervisor(a) escolar e coordenador(a) de área de ensino</b>
		<input type="text" name="escola_info_profissionais_de_apoio_e_supervisao_pedagogica_p_130" value="<?php echo $row_EscolaLogada['escola_info_profissionais_de_apoio_e_supervisao_pedagogica_p_130']; ?>">
		

    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">131 - Secretário(a) escolar</b>
		<input type="text" name="escola_info_secretarioa_escolar_131" value="<?php echo $row_EscolaLogada['escola_info_secretarioa_escolar_131']; ?>">

    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">132 - Segurança, guarda ou segurança patrimonial</b>
		<input type="text" name="escola_info_seguranca_guarda_ou_seguranca_patrimonial_132" value="<?php echo $row_EscolaLogada['escola_info_seguranca_guarda_ou_seguranca_patrimonial_132']; ?>">
		
    </label>

<label class="ls-label col-md-10">
        <b class="ls-label-text">133 - Técnicos(as), monitores(as), supervisores(as) ou auxiliares de laboratório(s), de apoio a tecnologias educacionais ou em multimeios/multimídias eletrônico-digitais.</b>
		<input type="text" name="escola_info_tecnicosas_monitoresas_supervisoresas_ou_auxilia_133" value="<?php echo $row_EscolaLogada['escola_info_tecnicosas_monitoresas_supervisoresas_ou_auxilia_133']; ?>">
		
    </label>

<label class="ls-label col-md-7">
        <b class="ls-label-text">134 - Vice-diretor(a) ou diretor(a) adjunto(a), profissionais responsáveis pela gestão administrativa e/ou financeira</b>
		<input type="text" name="escola_info_vicediretora_ou_diretora_adjuntoa_profissionais__134" value="<?php echo $row_EscolaLogada['escola_info_vicediretora_ou_diretora_adjuntoa_profissionais__134']; ?>">
		
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">135 - Orientador(a) comunitário(a) ou assistente social</b>
		<input type="text" name="escola_info_orientadora_comunitarioa_ou_assistente_social_135" value="<?php echo $row_EscolaLogada['escola_info_orientadora_comunitarioa_ou_assistente_social_135']; ?>">
    </label>

<label class="ls-label col-md-7">
        <b class="ls-label-text">136 - Tradutor e Intérprete de Libras para atendimento em outros ambientes da escola que não seja sala de aula</b>
		<input type="text" name="escola_info_tradutor_e_interprete_de_libras_para_atendimento_136" value="<?php echo $row_EscolaLogada['escola_info_tradutor_e_interprete_de_libras_para_atendimento_136']; ?>">
		

    </label>

<label class="ls-label col-md-9">
        <b class="ls-label-text">137 - Revisor de texto Braille, assistente vidente (assistente de revisão do texto em Braille)</b>
		<input type="text" name="escola_info_revisor_de_texto_braille_assistente_vidente_assi_137" value="<?php echo $row_EscolaLogada['escola_info_revisor_de_texto_braille_assistente_vidente_assi_137']; ?>">
		

    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">138 - Não há funcionários para as funções listadas</b>
        <div class="ls-custom-select">
            <select name="escola_info_nao_ha_funcionarios_para_as_funcoes_listadas_138" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_nao_ha_funcionarios_para_as_funcoes_listadas_138'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">139 - Alimentação escolar para os aluno(a)s</b>
        <div class="ls-custom-select">
            <select name="escola_info_alimentacao_escolar_para_os_alunoas_139" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_alimentacao_escolar_para_os_alunoas_139'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_alimentacao_escolar_para_os_alunoas_139'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-12">
<h4 class="ls-title-4 ls-color-theme ls-box ls-box-gray">140 a 156 - Instrumentos e materiais socioculturais e/ou pedagógicos em uso na escola para o desenvolvimento de atividades de ensino-aprendizagem</h4></label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">140 - Acervo multimídia</b>
        <div class="ls-custom-select">
            <select name="escola_info_acervo_multimidia_140" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_acervo_multimidia_140'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_acervo_multimidia_140'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">141 - Brinquedos para educação infantil</b>
        <div class="ls-custom-select">
            <select name="escola_info_brinquedos_para_educacao_infantil_141" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_brinquedos_para_educacao_infantil_141'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_brinquedos_para_educacao_infantil_141'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">142 - Conjunto de materiais científicos</b>
        <div class="ls-custom-select">
            <select name="escola_info_conjunto_de_materiais_cientificos_142" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_conjunto_de_materiais_cientificos_142'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_conjunto_de_materiais_cientificos_142'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-5">
        <b class="ls-label-text">143 - Equipamento para amplificação e difusão de som/áudio</b>
        <div class="ls-custom-select">
            <select name="escola_info_equipamento_para_amplificacao_e_difusao_de_somau_143" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_equipamento_para_amplificacao_e_difusao_de_somau_143'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_equipamento_para_amplificacao_e_difusao_de_somau_143'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-7">
        <b class="ls-label-text">144 - Equipamentos e instrumentos para atividades em área de horta, plantio e/ou produção agrícola</b>
        <div class="ls-custom-select">
            <select name="escola_info_equipamentos_e_instrumentos_para_atividades_em_a_144" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_equipamentos_e_instrumentos_para_atividades_em_a_144'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_equipamentos_e_instrumentos_para_atividades_em_a_144'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-6">
        <b class="ls-label-text">145 - Instrumentos musicais para conjunto, banda/fanfarra e/ou aulas de música</b>
        <div class="ls-custom-select">
            <select name="escola_info_instrumentos_musicais_para_conjunto_bandafanfarr_145" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_instrumentos_musicais_para_conjunto_bandafanfarr_145'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_instrumentos_musicais_para_conjunto_bandafanfarr_145'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">146 - Jogos educativos</b>
        <div class="ls-custom-select">
            <select name="escola_info_jogos_educativos_146" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_jogos_educativos_146'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_jogos_educativos_146'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">147 - Materiais para atividades culturais e artísticas</b>
        <div class="ls-custom-select">
            <select name="escola_info_materiais_para_atividades_culturais_e_artisticas_147" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_materiais_para_atividades_culturais_e_artisticas_147'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_materiais_para_atividades_culturais_e_artisticas_147'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">148 - Materiais para educação profissional</b>
        <div class="ls-custom-select">
            <select name="escola_info_materiais_para_educacao_profissional_148" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_materiais_para_educacao_profissional_148'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_materiais_para_educacao_profissional_148'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">149 - Materiais para prática desportiva e recreação</b>
        <div class="ls-custom-select">
            <select name="escola_info_materiais_para_pratica_desportiva_e_recreacao_149" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_materiais_para_pratica_desportiva_e_recreacao_149'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_materiais_para_pratica_desportiva_e_recreacao_149'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-5">
        <b class="ls-label-text">150 - Materiais pedagógicos para a educação bilíngue de surdos</b>
        <div class="ls-custom-select">
            <select name="escola_info_materiais_pedagogicos_para_a_educacao_bilingue_d_150" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_materiais_pedagogicos_para_a_educacao_bilingue_d_150'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_materiais_pedagogicos_para_a_educacao_bilingue_d_150'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-5">
        <b class="ls-label-text">151 - Materiais pedagógicos para a educação escolar indígena</b>
        <div class="ls-custom-select">
            <select name="escola_info_materiais_pedagogicos_para_a_educacao_escolar_in_151" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_materiais_pedagogicos_para_a_educacao_escolar_in_151'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_materiais_pedagogicos_para_a_educacao_escolar_in_151'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-5">
        <b class="ls-label-text">152 - Materiais pedagógicos para a educação das relações étnicos raciais</b>
        <div class="ls-custom-select">
            <select name="escola_info_materiais_pedagogicos_para_a_educacao_das_relaco_152" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_materiais_pedagogicos_para_a_educacao_das_relaco_152'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_materiais_pedagogicos_para_a_educacao_das_relaco_152'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">153 - Materiais pedagógicos para a educação do campo</b>
        <div class="ls-custom-select">
            <select name="escola_info_materiais_pedagogicos_para_a_educacao_do_campo_153" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_materiais_pedagogicos_para_a_educacao_do_campo_153'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_materiais_pedagogicos_para_a_educacao_do_campo_153'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-5">
        <b class="ls-label-text">154 - Materiais pedagógicos para a educação escolar quilombola</b>
        <div class="ls-custom-select">
            <select name="escola_info_materiais_pedagogicos_para_a_educacao_escolar_qu_154" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_materiais_pedagogicos_para_a_educacao_escolar_qu_154'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_materiais_pedagogicos_para_a_educacao_escolar_qu_154'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">155 - Materiais pedagógicos para a educação especial</b>
        <div class="ls-custom-select">
            <select name="escola_info_materiais_pedagogicos_para_a_educacao_especial_155" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_materiais_pedagogicos_para_a_educacao_especial_155'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_materiais_pedagogicos_para_a_educacao_especial_155'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">156 - Nenhum dos instrumentos listados</b>
        <div class="ls-custom-select">
            <select name="escola_info_nenhum_dos_instrumentos_listados_156" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_nenhum_dos_instrumentos_listados_156'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_nenhum_dos_instrumentos_listados_156'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">157 - Escola indígena</b>
        <div class="ls-custom-select">
            <select name="escola_info_escola_indigena_157" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_escola_indigena_157'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_escola_indigena_157'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-12">
<h4 class="ls-title-4 ls-color-theme ls-box ls-box-gray">158 a 159 - Língua em que o ensino é ministrado</h4></label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">158 - Língua indígena</b>
        <div class="ls-custom-select">
            <select name="escola_info_lingua_indigena_158" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_lingua_indigena_158'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_lingua_indigena_158'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">159 - Língua portuguesa</b>
        <div class="ls-custom-select">
            <select name="escola_info_lingua_portuguesa_159" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_lingua_portuguesa_159'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_lingua_portuguesa_159'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">160 - Código da língua indígena 1</b>
		<input type="text" name="escola_info_codigo_da_lingua_indigena_1_160" value="<?php echo $row_EscolaLogada['escola_info_codigo_da_lingua_indigena_1_160']; ?>">
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">161 - Código da língua indígena 2</b>
		<input type="text" name="escola_info_codigo_da_lingua_indigena_2_161" value="<?php echo $row_EscolaLogada['escola_info_codigo_da_lingua_indigena_2_161']; ?>">
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">162 - Código da língua indígena 3</b>
		<input type="text" name="escola_info_codigo_da_lingua_indigena_3_162" value="<?php echo $row_EscolaLogada['escola_info_codigo_da_lingua_indigena_3_162']; ?>">
    </label>

<label class="ls-label col-md-7">
        <b class="ls-label-text">163 - A escola faz exame de seleção para ingresso de seus aluno(a)s (avaliação por prova e /ou analise curricular)</b>
        <div class="ls-custom-select">
            <select name="escola_info_a_escola_faz_exame_de_selecao_para_ingresso_de_s_163" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_a_escola_faz_exame_de_selecao_para_ingresso_de_s_163'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_a_escola_faz_exame_de_selecao_para_ingresso_de_s_163'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-12">
<h4 class="ls-title-4 ls-color-theme ls-box ls-box-gray">164 a 169 - Reserva de vagas por sistema de cotas para grupos específicos de aluno(a)s</h4></label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">164 - Autodeclarado preto, pardo ou indígena (PPI)</b>
        <div class="ls-custom-select">
            <select name="escola_info_autodeclarado_preto_pardo_ou_indigena_ppi_164" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_autodeclarado_preto_pardo_ou_indigena_ppi_164'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_autodeclarado_preto_pardo_ou_indigena_ppi_164'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">165 - Condição de renda</b>
        <div class="ls-custom-select">
            <select name="escola_info_condicao_de_renda_165" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_condicao_de_renda_165'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_condicao_de_renda_165'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">166 - Oriundo de escola pública</b>
        <div class="ls-custom-select">
            <select name="escola_info_oriundo_de_escola_publica_166" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_oriundo_de_escola_publica_166'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_oriundo_de_escola_publica_166'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">167 - Pessoa com deficiência (PCD)</b>
        <div class="ls-custom-select">
            <select name="escola_info_pessoa_com_deficiencia_pcd_167" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_pessoa_com_deficiencia_pcd_167'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_pessoa_com_deficiencia_pcd_167'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">168 - Outros grupos que não os listados</b>
        <div class="ls-custom-select">
            <select name="escola_info_outros_grupos_que_nao_os_listados_168" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_outros_grupos_que_nao_os_listados_168'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_outros_grupos_que_nao_os_listados_168'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-6">
        <b class="ls-label-text">169 - Sem reservas de vagas para sistema de cotas (ampla concorrência)</b>
        <div class="ls-custom-select">
            <select name="escola_info_sem_reservas_de_vagas_para_sistema_de_cotas_ampl_169" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_sem_reservas_de_vagas_para_sistema_de_cotas_ampl_169'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_sem_reservas_de_vagas_para_sistema_de_cotas_ampl_169'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-6">
        <b class="ls-label-text">170 - A escola possui site ou blog ou página em redes sociais para comunicação institucional</b>
        <div class="ls-custom-select">
            <select name="escola_info_a_escola_possui_site_ou_blog_ou_pagina_em_redes__170" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_a_escola_possui_site_ou_blog_ou_pagina_em_redes__170'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_a_escola_possui_site_ou_blog_ou_pagina_em_redes__170'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-6">
        <b class="ls-label-text">171 - A escola compartilha espaços para atividades de integração escola-comunidade</b>
        <div class="ls-custom-select">
            <select name="escola_info_a_escola_compartilha_espacos_para_atividades_de__171" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_a_escola_compartilha_espacos_para_atividades_de__171'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_a_escola_compartilha_espacos_para_atividades_de__171'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-8">
        <b class="ls-label-text">172 - A escola usa espaços e equipamentos do entorno escolar para atividades regulares com os aluno(a)s</b>
        <div class="ls-custom-select">
            <select name="escola_info_a_escola_usa_espacos_e_equipamentos_do_entorno_e_172" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_a_escola_usa_espacos_e_equipamentos_do_entorno_e_172'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_a_escola_usa_espacos_e_equipamentos_do_entorno_e_172'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-12">
<h4 class="ls-title-4 ls-color-theme ls-box ls-box-gray">173 a 178 - Órgãos colegiados em funcionamento na escola</h4></label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">173 - Associação de Pais</b>
        <div class="ls-custom-select">
            <select name="escola_info_associacao_de_pais_173" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_associacao_de_pais_173'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_associacao_de_pais_173'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">174 - Associação de pais e mestres</b>
        <div class="ls-custom-select">
            <select name="escola_info_associacao_de_pais_e_mestres_174" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_associacao_de_pais_e_mestres_174'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_associacao_de_pais_e_mestres_174'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">175 - Conselho escolar</b>
        <div class="ls-custom-select">
            <select name="escola_info_conselho_escolar_175" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_conselho_escolar_175'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_conselho_escolar_175'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">176 - Grêmio estudantil</b>
        <div class="ls-custom-select">
            <select name="escola_info_gremio_estudantil_176" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_gremio_estudantil_176'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_gremio_estudantil_176'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">177 - Outros 177</b>
        <div class="ls-custom-select">
            <select name="escola_info_outros_177_177" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_outros_177_177'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_outros_177_177'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">178 - Não há órgãos colegiados em funcionamento</b>
        <div class="ls-custom-select">
            <select name="escola_info_nao_ha_orgaos_colegiados_em_funcionamento_178" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_nao_ha_orgaos_colegiados_em_funcionamento_178'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_nao_ha_orgaos_colegiados_em_funcionamento_178'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-10">
        <b class="ls-label-text">179 - O projeto político pedagógico ou a proposta pedagógica da escola (conforme art. 12 da LDB) foi atualizada nos últimos 12 meses até a data de referência</b>
        <div class="ls-custom-select">
            <select name="escola_info_o_projeto_politico_pedagogico_ou_a_proposta_peda_179" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_o_projeto_politico_pedagogico_ou_a_proposta_peda_179'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_o_projeto_politico_pedagogico_ou_a_proposta_peda_179'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-6">
        <b class="ls-label-text">180 - A escola desenvolve ações na área de educação ambiental?</b>
        <div class="ls-custom-select">
            <select name="escola_info_a_escola_desenvolve_acoes_na_area_de_educacao_am_180" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_a_escola_desenvolve_acoes_na_area_de_educacao_am_180'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_a_escola_desenvolve_acoes_na_area_de_educacao_am_180'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-12">
<h4 class="ls-title-4 ls-color-theme ls-box ls-box-gray">181 a 186 - Informe de qual(quais) forma(s) a educação ambiental é desenvolvida na escola:</h4></label>

<label class="ls-label col-md-6">
        <b class="ls-label-text">181 - Como conteúdo dos componentes/campos de experiências presentes no currículo</b>
        <div class="ls-custom-select">
            <select name="escola_info_como_conteudo_dos_componentescampos_de_experienc_181" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_como_conteudo_dos_componentescampos_de_experienc_181'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_como_conteudo_dos_componentescampos_de_experienc_181'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-6">
        <b class="ls-label-text">182 - Como um componente curricular especial, específico, flexível ou eletivo</b>
        <div class="ls-custom-select">
            <select name="escola_info_como_um_componente_curricular_especial_especific_182" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_como_um_componente_curricular_especial_especific_182'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_como_um_componente_curricular_especial_especific_182'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">183 - Como um eixo estruturante do currículo</b>
        <div class="ls-custom-select">
            <select name="escola_info_como_um_eixo_estruturante_do_curriculo_183" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_como_um_eixo_estruturante_do_curriculo_183'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_como_um_eixo_estruturante_do_curriculo_183'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">184 - Em eventos</b>
        <div class="ls-custom-select">
            <select name="escola_info_em_eventos_184" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_em_eventos_184'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_em_eventos_184'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">185 - Em projetos transversais ou interdisciplinares</b>
        <div class="ls-custom-select">
            <select name="escola_info_em_projetos_transversais_ou_interdisciplinares_185" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_em_projetos_transversais_ou_interdisciplinares_185'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_em_projetos_transversais_ou_interdisciplinares_185'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>

<label class="ls-label col-md-4">
        <b class="ls-label-text">186 - Nenhuma das opções listadas</b>
        <div class="ls-custom-select">
            <select name="escola_info_nenhuma_das_opcoes_listadas_186" class="ls-select">
<option value=""></option>
                <option value="1" <?php if (!(strcmp("1", htmlentities($row_EscolaLogada['escola_info_nenhuma_das_opcoes_listadas_186'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>SIM</option>
                <option value="0" <?php if (!(strcmp("0", htmlentities($row_EscolaLogada['escola_info_nenhuma_das_opcoes_listadas_186'], ENT_COMPAT, "utf-8")))) {echo "SELECTED";} ?>>NÃO</option>
            </select>
        </div>
    </label>    
		  
		  
          <hr>
		  <label class="ls-label col-md-12">	
          <div class="ls-actions-btn">
          <input type="submit" value="ATUALIZAR" class="ls-btn-primary">
          </div>            
		  </label>    
	
          <input type="hidden" name="MM_update" value="form1">
          <input type="hidden" name="escola_id" value="<?php echo $row_EscolaLogada['escola_id']; ?>">
          <input type="hidden" name="usu_id" value="<?php echo $row_UsuLogado['usu_id']; ?>">

<hr>

        </form>
      </div>

    </div>
    <p>&nbsp;</p>
    <hr>
  </div>
</main>
<aside class="ls-notification">
  <nav class="ls-notification-list" id="ls-notification-curtain" style="left: 1716px;">
    <h3 class="ls-title-2">Notificações</h3>
    <ul>
      <?php include "notificacoes.php"; ?>
    </ul>
  </nav>
  <nav class="ls-notification-list" id="ls-help-curtain" style="left: 1756px;">
    <h3 class="ls-title-2">Feedback</h3>
    <ul>
      <li><a href="https://webmail.smecel.com.br" target="_blank">&gt; Acesse o webmail de sua escola</a></li>
    </ul>
  </nav>
  <nav class="ls-notification-list" id="ls-feedback-curtain" style="left: 1796px;">
    <h3 class="ls-title-2">Ajuda</h3>
    <ul>
      <li class="ls-txt-center hidden-xs"> <a href="tutoriais_video.php" class="ls-btn-dark ls-btn-tour">Tutorial (Vídeos)</a> </li>
      <li><a href="#">&gt; Guia</a></li>
      <li><a href="#">&gt; Wiki</a></li>
    </ul>
  </nav>
</aside>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script> 
<script src="js/mascara.js"></script>

</body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);
?>
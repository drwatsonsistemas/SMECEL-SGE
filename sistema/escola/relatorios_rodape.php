<style>
html{
  -webkit-print-color-adjust: exact;
}
.page-footer, .page-footer-space {
    height:30px;

}

.page-footer {
  position: fixed;
  bottom: 10px;
  width: 100%;
  text-align: center;
  margin-top:10px;
}

tfoot td {
  border: none !important;
}

.page {
  page-break-after: always;
}

@page {
  margin: 20mm;
}

@media print {


  tfoot td {
  border: none !important;
}
  
  tfoot { display: table-footer-group; }
  button { display: none; }
  body { margin: 0; }
}
</style>

<div class="page-footer print-footer" style='background:#f0f0f0;' >
  <small ></i>Relatório impresso em <?php echo date('d/m/Y'); ?> às <?php echo date('H:i') .' por '. $row_UsuLogado['usu_nome'] ?>. <br>SMECEL - Sistema de Gestão Escolar (www.smecel.com.br)</i></small>
</div>

<tfoot>
    <tr>
        <td>
            <div class="page-footer-space"></div>
        </td>
    </tr>
</tfoot>
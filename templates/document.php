<?php

use Moloni\Tools;
use Moloni\Error;
use Moloni\Model\WhmcsDB;

?>
<section id="moloni">

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" scope href="<?= Tools::getPublicUrl('compiled.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="../modules/addons/moloni/assets/datatables/css/jquery.dataTables.css">

    <div class="row">
        <div class="col">
            <a href='https://moloni.pt' target='_BLANK'>
                <img src='<?= Tools::getPublicUrl('img/logo.png') ?>' class='moloni-logo'>
            </a>
        </div>
        <div class="right">
            <div>
                <a href="addonmodules.php?module=moloni&action=logout"
                   class="waves-effect waves-light btn red logoutMoloni">
                    <span>Sair</span>
                    <i class='material-icons'>logout</i>
                </a>
            </div>
        </div>
    </div>

    <?php

    if (Error::$exists) {
        if (!empty(Error::$success)) {
            $echoSuccess = "<div class='alert alert-success'>" . Error::$success['text'];
            echo $echoSuccess . "</div>";
        }

        if (!empty(Error::$error)) {
            $echoError = "<div class='alert alert-danger'>" . Error::$error['where'] . " - " . Error::$error['message'];
            echo $echoError . '</div>';
        }
    }

    ?>

    <div class="row menuMoloni">
        <div class="col">
            <a class="black-text" href="addonmodules.php?module=moloni">Faturação</a>
        </div>
        <div class="col menuAtivo">
            <a class="black-text" href="addonmodules.php?module=moloni&action=docs">Documentos</a>
        </div>
        <div class="col">
            <a class="black-text" href="addonmodules.php?module=moloni&action=config">Configuração</a>
        </div>
    </div>

    <div class="row">
        <div class="col s12" style='margin-top: 5px;'>
            <div>
                <?php $documents = WhmcsDB::getAllDocuments(); ?>

                <table class='highlight display moloniTable'>
                    <thead>
                    <tr>
                        <th class='center-align' data-field="id">Número</th>
                        <th data-field="name">Cliente</th>
                        <th data-field="documentset">Série</th>
                        <th data-field="date">Data</th>
                        <th data-field="status">Estado</th>
                        <th data-field="total">Total</th>
                        <th data-field="acts" style="width:190px !important;">
                            <div>Ações</div>
                        </th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php
                    foreach ($documents as $document) {
                        echo "<tr" . ((empty($document['detail'])) ? ' class="naoGeradoMoloni"' : null) . ">";;
                        $orderNumero = (!empty($document['invoice']['invoicenum'])) ? $document['invoice']['invoicenum'] : $document['invoice']['order_id'];
                        echo "<td class='center-align'><a style='text-transform: none' class='waves-effect waves-light btn blue white-text' href='invoices.php?action=edit&id=" . $document['invoice']['order_id'] . "' target='_BLANK'>" . $orderNumero . "</a></td>";
                        echo "<td>" . $document['invoice']['name'] . "</td>";
                        echo "<td>" . $document['invoice']['set'] . "</td>";
                        echo "<td>" . date('d-m-Y', strtotime($document['invoice']['date'])) . "</td>";
                        echo "<td>" . (($document['invoice']['status'] == 1) ? 'Fechado' : (($document['invoice']['status'] == -1) ? 'Não gerado' : 'Rascunho')) . "</td>";
                        echo "<td >" . (($document['invoice']['net_value'] == "") ? "0.00" : $document['invoice']['net_value']) . "</td>";
                        echo "
							<td>
							<div class='acoesBtnMoloniDocs'>
								  <a class='waves-effect waves-light btn orange' href='" . Tools::genURL("docs", "redo&id=" . $document['invoice']['order_id']) . "'><i class='material-icons'>repeat</i></a>
						";
                        if (!empty($document['detail'])) {
                            echo "<a class='waves-effect waves-light btn blue' target='_BLANK' href='" . $document['detail'] . "'><i class='material-icons'>visibility</i></a>";
                        }
                        if (!empty($document['download'])) {
                            echo "<a class='waves-effect waves-light btn green' target='_BLANK' href='" . $document['download'] . "'><i class='material-icons'>cloud_download</i></a>";
                        }
                        echo "</div>";
                        echo "</td>";
                        echo "</tr>";
                    }

                    ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</section>

<script type="text/javascript" src="<?= Tools::getPublicUrl('compiled.min.js') ?>"></script>

<script>
    $(document).ready(function() {
        var index = 0;
        var order = 'asc';
        if (localStorage.getItem("docsIndex") !== null && localStorage.getItem("docsOrder") !== null) {
            switch (localStorage.getItem("docsIndex")) {
                case 'id':
                    index = 0;
                    break;
                case 'name':
                    index = 1;
                    break;
                case 'documentset':
                    index = 2;
                    break;
                case 'date':
                    index = 3;
                    break;
                case 'status':
                    index = 4;
                    break;
                case 'total':
                    index = 5;
                    break;
                case 'acts':
                    index = 6
                    break;
            }
            if (localStorage.getItem("docsOrder") == 'descending') {
                order = 'desc';
            }
        }
        $('.highlight').dataTable({
            "aaSorting": [[index, order]],
            "sPaginationType": "simple_numbers",
            "sDom": '<"top"<"MolSearch"f><"MolShowing"l>>rt<"bottom row"<"MolInfo col s6"i><"MolPagination col s6"p>><"clear">',
            "oLanguage": {
                "sLengthMenu": "_MENU_",
                "sZeroRecords": "Sem resultados encontrados",
                "sInfo": "A mostrar <b>_START_</b> - <b>_END_</b> de <b>_TOTAL_</b> Faturas",
                "sInfoEmpty": "Sem resultados para apresentar",
                "sInfoFiltered": "(Filtrados de _MAX_)",
                "sSearch": "",
                "sSearchPlaceholder": "Pesquisar...",
                "oPaginate": {
                    "sPrevious": "Anterior",
                    "sNext": "Seguinte",
                }
            }
        }).on('draw.dt', function() {
            var info = ($(this).DataTable()).page.info();
            //ultima pagina
            if (info.page + 1 === info.pages) {
                $('.paginate_button.previous').removeClass('paginateInativo');
                $('.paginate_button.next').addClass('paginateInativo');
            } else if (info.page == 0) { //primeira pagina
                $('.paginate_button.previous').addClass('paginateInativo');
                $('.paginate_button.next').removeClass('paginateInativo');
            } else {
                $('.paginate_button.next').removeClass('paginateInativo');
                $('.paginate_button.previous').removeClass('paginateInativo');
            }
            $('#mydiv').hide();

        });

        //unica pagina
        $('.paginate_button.previous').addClass('paginateInativo');
        if (($('.highlight').DataTable()).page.info().pages == 1) {
            $('.paginate_button.next').addClass('paginateInativo');
        }

        function deselect(e) {
            $('.pop').slideFadeToggle(function() {
                e.removeClass('selected');
            });
        }

        $(function() {
            $('.check_error').on('click', function() {
                if ($(this).hasClass('selected')) {
                    deselect($(this));
                } else {
                    $(this).addClass('selected');
                    $('.pop').slideFadeToggle();
                }
                return false;
            });

            $('.close').on('click', function() {
                deselect($('.check_error'));
                return false;
            });
        });

        $.fn.slideFadeToggle = function(easing, callback) {
            return this.animate({opacity: 'toggle', height: 'toggle'}, 'fast', easing, callback);
        };

        //guardar ultima ordenacao
        $(".moloniTable th").on('click', function() {
            localStorage.setItem('docsIndex', $(this).attr('data-field'));
            localStorage.setItem('docsOrder', $(this).attr('aria-sort'));
        });
    });
</script>

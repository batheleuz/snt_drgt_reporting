<?php
function page (){

    ob_start();

    ?>
        <link rel="stylesheet" href="<?= URL; ?>css/dataTables/dataTables.min.css">
        <link rel="stylesheet" href="<?= URL; ?>css/dataTables/dataTables.jquery-ui.min.css">
        <div class="w3-card w3-animate-left" id="reporting-view">
            <div class="w3-container">
                <h1><i class="fa fa-calendar"></i> Reportings du Service. </h1>
                <hr class="w3-border w3-border-blue">
            </div>
            <div class="w3-responsive w3-padding w3-white ">
                <table class="w3-table w3-striped w3-hoverable" id="list_tab">
                    <thead>
                    <tr><th>projet</th>
                        <th>reporting</th>
                        <th>ajouté le</th>
                        <th>périodicité</th>
                        <th>créé par</th>
                        <th>action</th> </tr>
                    </thead>

                    <tbody>
                    <?php foreach ( ReportingCRUD::getReportings($_SESSION['service']['nom']) as $reporting ): ?>
                        <tr class="w3-large w3-text-dark-grey" id="<?= $reporting['id']; ?>">
                            <td class="w3-center ouvrir" style="cursor:pointer;"><i class="fa fa-folder fa-2x  "></i></td>
                            <td> <?= $reporting['name']; ?> </td>
                            <td> <?=  $reporting['id']; ?>   </td> <!-- ici id = date  -->
                            <td> <?= $reporting['periodicite']; ?> jours</td>
                            <td> <?= ucfirst($reporting['user']); ?> </td>
                            <td class="w3-center">
                                <a href="#" class="w3-button  w3-hover-text-blue ouvrir "> <i
                                        class="fa fa-folder-open-o fa-2x "></i> </a>
                                <?php if ( $_SESSION['service']['id_admin'] == $_SESSION['user']['id'] ): ?>
                                <a href="#" class="w3-button  w3-hover-text-red suppr "> <i
                                            class="fa fa-trash-o fa-2x"></i> </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="w3-card w3-padding w3-white w3-animate-right" id="content" style="display:none;">
            <div class="w3-row-padding">
                <div class="w3-col m1">
                    <a class="btn w3-btn-floating w3-text-gray w3-light-gray w3-hover-dark-gray w3-margin-top "
                       onclick="switcher('content' , 'reporting-view' )">
                        <i class="fa fa-arrow-left"></i>
                    </a></div>
                <div class="w3-col m10">
                    <h1 class="w3-text-gray" id="titre_report"> Titre Reporting </h1>
                </div>
            </div>
            <hr class="w3-border w3-border-gray ">
            <div class="w3-container w3-padding w3-border">

                <div class="w3-row-padding w3-border w3-light-gray"><h4 class="w3-text-dark-gray"><i class="fa fa-wrench"></i> Configurations du projet </h4></div>
                <div class="w3-row-padding">
                    <div class="w3-quarter"><h5><b>Périodicité:</b></h5></div>
                    <div class="w3-quarter" id="periodicite_report"></div>
                    <div class="w3-quarter"><h5><b>Utilisateur:</b></div>
                    <div class="w3-quarter " id="user_report" style="text-transform: capitalize;"></div>
                </div>
                <div style="padding: 0 30px; "><hr class="w3-border w3-border-light w3-margin-0" ></div>
                <div class="w3-row-padding">
                    <div class="w3-quarter"><h5><b> KPI </b></h5></div>
                    <div class="w3-threequarter" id="kpi_report"></div>
                </div>
                <div style="padding: 0 30px; "><hr class="w3-border w3-border-light w3-margin-0" ></div>
                <div class="w3-row-padding ">
                    <div class="w3-quarter"><h5><b> COLONNES </b></h5></div>
                    <div class="w3-threequarter " id="column_report"></div>
                </div>
                <hr>
                <div class="w3-row-padding w3-border w3-margin-top w3-margin-bottom w3-light-gray">
                    <h4 class="w3-text-dark-gray"><i class="fa fa-list-ul"></i>
                        Liste des Reportings disponibles. </h4></div>
                <ul class="w3-padding-0 w3-border w3-ul w3-hoverable" id="list_report" style="max-height:400px;overflow-y:scroll;"></ul>
            </div>
        </div>
        <div id="result" class=" w3-card-4 w3-animate-right" style="display:none;">

        </div>

     <script type="text/javascript" src="<?= URL; ?>js/dataTables.min.js"></script>
     <script type="text/javascript" src="<?= URL; ?>js/dataTables.jquery-ui.min.js"></script>
     <script type="text/javascript" src="<?= URL; ?>js/dt.js"></script>

    <?php

    return ob_get_clean();
}

echo CodeCompressor::compress_html( page() ) ;

CodeCompressor::importer( 'vue/app/reportingFilesScript.php' , "js" ) ;

?>
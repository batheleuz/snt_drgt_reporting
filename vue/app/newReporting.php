<?php
function page  ($pageTitle )
{

    ob_start();
    ?>
    <div id="content" class="w3-animate-left">
        <form class="form" method="post" enctype="multipart/form-data" id="form">
            <div class=" w3-card-4 w3-white  w3-border">
                <header class="w3-container">
                    <h1><i class="fa fa-plus-circle"></i> <?= $pageTitle; ?> </h1>
                    <hr class="w3-boder w3-border-teal">
                </header>
                <div class="w3-container w3-white" style="margin-top:25px;margin-bottom:40px;">

                    <div class="w3-row" style="">
                        <div class="w3-row-padding">
                            <div class="w3-quarter"><b>Relévés Entre:</b></div>
                            <div class="w3-third"><input type="text" name="date_debut"
                                                         class="w3-input w3-border datepicker" placeholder="Date début"
                                                         required></div>
                            <div class="w3-third"><input type="text" name='date_fin'
                                                         class="w3-input w3-border datepicker" placeholder="Date Fin"
                                                         required></div>
                        </div>
                        <hr>
                        <div class="w3-row-padding">
                            <div class="w3-quarter"><b>Nom du reporting: </b></div>
                            <div class="w3-twothird ">
                                <input type="text" name="nom_reporting" class="w3-input w3-border" autocomplete="off"
                                       placeholder="Ex: reporting_semaine_53">
                            </div>
                        </div>
                        <hr>

                        <div class="w3-row">
                            <div class="w3-col w3-padding" style="width:170px"><b> Direction: </b></div>
                            <div class="w3-rest w3-padding  ">
                                <select name="direction" class="uniq" id="">
                                    <option value="sonatel"> ---- Choisir ----</option>
                                    <option value="sonatel">SONATEL</option>
                                    <option value="dint">DINT</option>
                                    <option value="dsc"> DSC</option>
                                </select>
                            </div>
                        </div>

                        <?php if ($_GET['param2'] == "global"): ?>
                            <div class="w3-row w3-margin-top">
                                <span class="w3-padding" style="display:inline-block;width:180px"> <b> Groupe
                                        d'Intervention: </b></span>

                                <select data-placeholder="Sous traitant , Services , ...." name='groupe_intervention[]'
                                        multiple class="chosen-select">
                                    <?php foreach (all("groupe_intervention") as $gi): ?>
                                        <option value="<?= $gi['id'] ?>"> <?= $gi['nom'] ?>  </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" name="action" value="reporting_global">
                            </div>
                        <?php endif; ?>
                        <hr>
                        <div class="w3-row">
                            <div class="w3-col w3-padding" style='width:170px;'><b>Périodicité:</b></div>
                            <div class="w3-rest w3-padding">
                                <select name="par" class="w3-input uniq" style='width:350px;'>
                                    <option value="week"> ---- Choisir ----</option>
                                    <option value="day">Journalière</option>
                                    <option value="week">Hebdomadaire</option>
                                    <option value="month">Mensuelle</option>
                                    <option value="trimester">Trimestrielle</option>
                                </select>
                            </div>
                        </div>
                        <hr>
                        <div class="w3-row w3-margin-top">
                            <span class="w3-padding" style="display:inline-block;width:180px"> <b> KPI à calculer: </b></span>
                            <select data-placeholder="Choisir vos kpi" id="multi" name='kpi[]' multiple
                                    class="chosen-select">
                                <?php foreach (all("kpi") as $kpi): ?>
                                    <option value="<?= $kpi['id'] ?>"> <?= $kpi['abreviation'] ?>  </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <?php if ($_GET['param2'] == "autre"): ?>
                        <div class="w3-row">
                            <div class="w3-col w3-padding" style='width:170px;'>
                                <a class="w3-btn w3-text-orange w3-hover-text-deep-orange w3-border w3-border-black w3-hover-white"
                                   style="width:100%" onclick="getValue();">
                                    <i class="fa fa-plus-square"></i> colonne </a></div>
                            <div class="w3-rest w3-twothird" id="filtered_col">
                            </div>
                        </div>
                        <input type="hidden" name="action" value="autre_reporting">
                    <?php endif; ?>
                </div>
                <hr>
                <div class="w3-container" style='height:200px;'>
                    <div class="w3-row-padding margin-top">
                        <button type="submit"
                                class="w3-btn w3-large w3-teal w3-hover-white w3-border w3-border-teal w3-wide  w3-padding"
                                name='enregistrer'> suivant <i class="fa fa-arrow-circle-right"></i></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php if ($_GET['param2'] == "autre"): ?>
    <div id="modal_add_filter" class="w3-modal ">
        <div class="w3-modal-content w3-card-4 w3-animate-zoom w3-round" style="width:500px;">
            <div class="w3-container">
        <span onclick="document.getElementById('modal_add_filter').style.display='none'"
              class="w3-closebtn">&times;</span>
                <div class="w3-container w3-border w3-margin w3-padding "><H2 class="w3-center">FILTRE</H2></div>
                <div class="container w3-margin-bottom">
                    <div class="w3-row-padding  w3-margin ">
                        <label><b>Colonne:</b></label>
                        <select name="colonne" id="colonnes">
                            <option value="0"> -- choisir --</option>
                            <?php foreach (Database::getDb()->all("colonnes", "filterable", 1) as $col): ?>
                                <option value="<?= $col['col_label']; ?>"> <?= $col['col_label']; ?> </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="w3-row-padding w3-margin  w3-border" id="col_distincts_values"
                         style="height:200px;overflow-y:scroll;">
                    </div>
                    <div class="w3-row-padding w3-margin w3-right">
                        <button class="w3-btn w3-deep-orange w3-border w3-hover-white" onclick="addFilter()"> ajouter
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
    <div id="result" class=" w3-card-4 w3-animate-right" style="display:none;"></div>

    <script src="<?= URL ?>js/jquery.table2excel.js"></script>

    <?php
    return ob_get_clean();
}
$page = page( $pageTitle );

echo CodeCompressor::compress_html( $page ) ;

CodeCompressor::importer( 'vue/app/newReportingScript.php' , "js" ) ;

?>

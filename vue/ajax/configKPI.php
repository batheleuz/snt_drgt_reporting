<?php
/**
 * @return string
 */
function page (){

    ob_start();

    ?>
    <div id='dialog-confirm'></div>
    <div class="w3-row-padding">
        <div class="w3-twothird w3-card">
            <div class="w3-row-padding ">
                <div id="rsl"></div>
                <div class="w3-col" style="width: 100px">
                    <br><button class="w3-btn w3-teal w3-hover-white  w3-large w3-round-large w3-border w3-border-grey w3-hover-border-teal kpi_config "
                                data-title = "Ajouter un KPI" data-action='add_kpi_form' >
                        <i class="fa fa-plus "></i> kpi
                    </button>
                </div>
                <div class="w3-rest w3-padding">
                    <input type="text" id="kpi_filter" placeholder=" Rechercher un KPI "
                           class="w3-margin-top w3-margin-bottom w3-input w3-border w3-round" onkeyup="myFunction()"
                           autofocus >
                </div>
            </div>

            <div style="max-height:600px; overflow:wrap;overflow-y: scroll;" class="w3-padding-bottom">
                <ul class="w3-ul w3-hoverable" id="myUL">
                    <?php foreach ( $_SESSION['kpi'] as $kpi ): ?>
                        <li class="w3-border" ><div class="w3-container">
                                <div class="w3-left"><h6><b><?= $kpi['abreviation']; ?></b></h6></div>
                                <div class="w3-right"><button data-abrev="<?= $kpi['abreviation']; ?>"" data-id="<?= $kpi['id']; ?>"
                                    class="w3-btn w3-hover-teal w3-btn-floating" onclick="suppr(this)"><i class="fa fa-trash"></i></button></div>
                            </div></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="w3-third">
            <div class="w3-row-padding w3-padding">
                <div class="w3-card">
                    <div class="w3-container w3-blue">
                        <h5><i class="fa fa-bars"></i> Indicateurs </h5>
                    </div>
                    <div class="w3-container w3-padding w3-white">
                        <div class="w3-row">
                            <div class="w3-third w3-padding-left">
                                <h4 class="w3-opacity ">_</h4>
                                <hr class="w3-padding-0">
                            </div>
                            <div class="w3-twothird w3-padding-left">
                                <h4 class="w3-opacity  ">Nom Complet</h4>
                                <hr class="w3-padding-0">
                            </div>
                        </div>
                        <div class="w3-row">
                            <?php foreach ( all("indicateur") as $ind ): ?>
                                <div class="w3-row">
                                    <div class="w3-third w3-padding-0">
                                        <p><b> <?= $ind['abreviation'] ?> </b></p>
                                    </div>
                                    <div class="w3-twothird w3-padding-left">
                                        <p class="w3-tiny"><?= utf8_encode($ind['nom_indicateur']); ?></p>
                                    </div>
                                </div><hr class="w3-margin-0">
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="dialog-form" title="Création d'un KPI" >

        <div class=" w3-card w3-margin-bottom">
            <div class="w3-container w3-padding">
                <div id="rsl_d"></div>
                <div class="w3-row-padding">
                    <div class="w3-half w3-padding ">Type de KPI:</div>
                    <div class="w3-half w3-padding ">
                        <select name="type_kpi" id="type_kpi" class="w3-input" >
                            <option> -- Choisir -- </option>
                            <?php  foreach ( all("indicateur", "type = 'pourcentage' ") as $kpi): ?>
                                <option value="<?= $kpi['type'] ?>"> <?= $kpi['abreviation'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="w3-row-padding">
                    <div class="w3-half w3-padding "> Valeur de n </div>
                    <div class="w3-half w3-padding ">
                        <input type="number" min=1 name="" placeholder="Valeur de n" id="delai" class="w3-input w3-border w3-paddin-0" style="padding:4px;">
                    </div>
                </div>
                <div class="w3-row-padding">
                    <div class="w3-half w3-padding "> Produit </div>
                    <div class="w3-half w3-padding ">
                        <select name="origine"  class="w3-input" >
                            <option> -- Choisir -- </option>
                            <?php  foreach ( all("indicateur", "type = 'origine' ") as $kpi): ?>
                                <option value="<?= $kpi['abreviation'] ?>"> <?= $kpi['abreviation'] ?> </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="w3-container w3-dark-grey w3-border w3-border-black w3-margin-top" >
            <div class="w3-row">
                <div class="w3-half w3-padding "><h4> Nom KPI  = </h4></div>
                <div class="w3-half w3-padding "><h4 class="abreviation"> </h4> </div>
            </div>
            <form id="form_add_kpi"  >
                <input type="hidden" name="action" value="add_kpi" >
                <input type="hidden" name="abreviation" value="" class="abreviation">
                <input type="hidden" name="delai" value="">
                <input type="hidden" name="type_drgt" value="">
                <input type="hidden" name ="delai_time" >
                <div class="w3-row w3-padding"> <button type="submit" disabled  class="w3-btn w3-teal w3-padding w3-border w3-hover-white w3-border-white w3-hover-border-teal "> <i class="fa fa-plus-square"></i> ajouter </button> </div>
            </form>
        </div>
    </div>
<?php
    return ob_get_clean();
}

$page = page();

echo CodeCompressor::compress_html($page);

CodeCompressor::importer("vue/ajax/configKPIScript.php" , "js");

?>


<?php
/**
 * Created by PhpStorm.
 * User: Daboss
 * Date: 23/02/2017
 * Time: 23:06
 */
function page()
{

    ob_start();

    ?>
    <div class="w3-container  w3-border">
        <div class="w3-half">
            <h3>Charger ou mettre à jour un fichier de Codification:</h3>
            <hr>
            <div id="verbose"></div>
            <form class="w3-form" id="form_add_codif">
                <div class="w3-row w3-padding"><b>Colonne:</b></div>
                <div class="w3-row w3-padding">
                    <select class="chosen-select" name="colonne">
                        <?php foreach (Database::getDB()->all("colonnes") as $key => $value): ?>
                            <option value="<?= $value['col_label']; ?>"><?= $value['col_label']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="w3-row w3-padding">
                    <b>Choisir le fichier (.csv)</b>
                </div>
                <div class="w3-row w3-padding">
                    <input type="hidden" name="action" value="upload_codif">
                    <input type="file" required name="fichier_codif" value="" class="w3-input w3-border">
                </div>
                <div class="w3-row w3-padding">
                    <button type="submit" name="button" class="w3-btn w3-teal w3-border w3-hover-white">
                        <i class="fa fa-upload"></i> charger
                    </button>
                </div>
            </form>
        </div>
        <div class="w3-half">
            <div class="w3-panel w3-pale-blue w3-round w3-padding"><i class="fa fa-info"></i> UTILISATION</div>
            <div class="w3-code w3-margin"> -- Choisir la colonne à codifier: <br>
                -- Le fichier doit être convertit en .csv avec séparateur ";" <br>
                -- L'Entête du fichier.csv doit contenir les mots clés :
                <ul type="square">
                    <li>signification</li>
                    <li>code</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="w3-container">

        <div class="w3-row w3-margin ">
            <div class="w3-col m6 ">
                <div class="w3-container w3-border "><h6><b>Listes des colonnes codifiées:</b></h6></div>
                <?php $liste = Database::getDb()->rqt("SELECT * FROM colonnes WHERE codification = 1 "); ?>
                <div class='w3-border' style="height:400px;overflow-x:wrap;overflow-y:scroll">
                    <ul class="w3-ul w3-border w3-hoverable" id="list_col">
                        <?php foreach ($liste as $li): ?>
                            <li onclick="fichier(this);" style="cursor:pointer;" data-col='<?= $li['col_label']; ?>'>
                                <b><?= $li['col_label']; ?></b> <span
                                    class="w3-badge w3-right"><?= count_code($li['col_label']) ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <div class="w3-col m5 w3-light-gray w3-margin-left w3-border w3-border-dark-gray">
                <input type="text" class="w3-input" placeholder="Chercher un code ... " id="code_filter"
                       onkeyup="chercher()">
                <div style="height:400px;overflow-x:wrap;overflow-y:scroll" id="" class="w3-padding">
                    <ul class="w3-ul" id="list_code">
                        <h4 class="w3-text-grey"> Sélectionner une colonne. </h4>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

echo CodeCompressor::compress_html( page() );

CodeCompressor::importer( "vue/ajax/codificationScript.php" , "js" );
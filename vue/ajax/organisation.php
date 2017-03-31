<?php
function page(){

	ob_start();

?>
	<div class="w3-row">
		<div class="w3-col m3 " >
			<div class="w3-container w3-border w3-padding"><h5> Catégorie </h5></div>
			<div class="w3-container w3-border w3-pale-green w3-padding "  style="height:550px;"  >
				<table class="w3-table w3-large " id="list_cat">
					<tr class="w3-hover-light-green w3-border w3-light-green cat" ><th>Toute <i class="fa fa-arrow-circle-o-right w3-right"></i> </th></tr>
					<tr class="w3-hover-light-green w3-border cat" data-cat="direction"><th>Direction <i class="fa fa-arrow-circle-o-right w3-right"></i></th></tr>
					<tr class="w3-hover-light-green w3-border cat" data-cat="sous_traitant"><th>Sous traitant <i class="fa fa-arrow-circle-o-right w3-right"></i> </th></tr>
					<tr class="w3-hover-light-green w3-border cat" data-cat="autre" ><th> Autre <i class="fa fa-arrow-circle-o-right w3-right"></i> </th></tr>
				</table>

				<div class="w3-container w3-border w3-margin-0 w3-margin-top w3-white "><h3> Options </h3></div>
				<div class="w3-container w3-padding">

					<div class="w3-col m12 w3-margin-bottom "><a class="   w3-text-teal w3-hover-text-blue  " style="cursor:pointer;"
												 onclick="document.getElementById('modal_add_gi').style.display='block'">
							<i class='fa fa-plus-square'></i> groupe intervention</a> </div>
					<div class="w3-col m12 "> <a class="w3-margin-top w3-text-teal w3-hover-text-blue  " style="cursor:pointer;"
												onclick="document.getElementById('modal_add_ui').style.display='block'">
							<i class='fa fa-plus-square'></i> unité intervention </a> </div>
				</div>
			</div>

	  </div>
	  <div class="w3-col m4 ">
		  <div class="w3-container w3-border w3-padding"> <h5> Groupe d'Intervention </h5> </div>
		  <div class="w3-container w3-border w3-padding w3-light-grey"  style="height:550px;overflow:wrap;overflow-y:scroll">
			  <div class="w3-row w3-margin-bottom "> <div id="gi_rsl"></div> </div>
			  <table class="w3-table w3-striped w3-bordered w3-responsive w3-hoverable " id="list_gi" >
				  <?php

				  foreach ( $_SESSION['groupe_intervention'] as $li): ?>
					  <tr class="gi" onclick="afficherGI(this)" id="<?= $li['id'] ?>" style="cursor: pointer;">
						  <td><b><?= $li['nom'] ?></b></td>
					  </tr>
				  <?php endforeach; ?>
			  </table>
		  </div>
	  </div>
	  <div class="w3-col m5" >
		  <div class="w3-container w3-border w3-padding"><h5 id="gi_title"> Groupe : </h5></div>
		  <div class="w3-container w3-border w3-padding w3-pale-green "  style="height:550px;overflow-x:wrap;overflow-y:scroll"  >
			  <div class="w3-row w3-margin-bottom">
				  <div id="ui_rsl"></div>
			  </div>
			  <ul class="w3-ul" id="list_ui">  </ul>
		  </div>
	  </div>
	</div>
	<div id="modal_add_gi" class="w3-modal w3-animate-opacity" >
			<div class="w3-modal-content w3-round w3-card-8 " style="width:500px;">
				<div class="w3-container w3-border">
					<span onclick="document.getElementById('modal_add_gi').style.display='none'" class="w3-closebtn">&times;</span>
					<div class="w3container w3-border w3-padding w3-margin"><h4 class="w3-center">Ajouter un Groupe d'intervention</h4></div>
					<div id="gi_modal_rsl"></div>
					<form class="w3-container w3-padding" id="form_add_gi">
						<input type="hidden" name="action" value="add_gi" >
						<label class="w3-label w3-text-teal"><b>Nom du groupe.</b></label>
						<input class="w3-input w3-border w3-margin-bottom" name="nom_gi" type="text" required>

						<label class="w3-label w3-text-teal"><b>catégorie</b></label>
						<fieldset>
							<label for="radio-1">Direction </label>
							<input type="radio" name="categorie" id="radio-1" value="direction" required>
							<label for="radio-2">Sous Traitant </label>
							<input type="radio" name="categorie" id="radio-2" value="sous_traitant" required>
							<label for="radio-3">Autre </label>
							<input type="radio" name="categorie" id="radio-3" value="autre" required>
						</fieldset>
						<button class="w3-btn w3-border w3-hover-border-teal w3-hover-white w3-teal w3-margin-top w3-right"><i class="fa fa-plus-square"></i> ajouter</button>
					</form>

				</div>
			</div>
		</div>
	<div id="modal_add_ui" class="w3-modal w3-animate-opacity" >
			<div class="w3-modal-content w3-round w3-card-8" style="width:500px;">
				<div class="w3-container w3-border">
					<span onclick="document.getElementById('modal_add_ui').style.display='none'" class="w3-closebtn">&times;</span>
					<div class="w3container w3-border w3-padding w3-margin"><h4 class="w3-center">Ajouter une Unité d'Intervention </h4></div>
					<div id="ui_modal_rsl"></div>
					<form class="w3-container w3-padding" id="form_add_ui">
						<input type="hidden" name="action" value="add_ui" >
						<label class="w3-label w3-text-teal"><b>Nom de l'UI.</b></label>
						<input class="w3-input w3-border w3-margin-bottom" name="nom_ui" type="text">
						<label class="w3-label w3-text-teal"><b>Choisir les groupes .</b></label>
						<select data-placeholder="Groupes ... " id="multi_grpe" name='groupes[]' multiple class="chosen-select" required>
							<?php
							foreach ( $_SESSION['groupe_intervention'] as $gr ): ?>
								<option value="<?= $gr['id']; ?>"> <?= $gr['nom']; ?> </option>
							<?php endforeach; ?>
						</select>
						<button class="w3-btn w3-border w3-hover-border-teal w3-hover-white w3-teal w3-margin-top w3-right"><i class="fa fa-plus-square"></i> ajouter</button>
					</form>
				</div>
			</div>
		</div>
	<div id="modal_add_existant_ui" class="w3-modal w3-animate-opacity" >
		<div class="w3-modal-content w3-round w3-card-8" style="width:400px;">
			<div class="w3-container w3-border">
				<span onclick="document.getElementById('modal_add_existant_ui').style.display='none'" class="w3-closebtn">&times;</span>
				<div class="w3container w3-border w3-padding w3-margin"><h5 class="w3-center"> Gerer les ui  </h5>
				</div><div class="w3container w3-padding ">
					<input type="text" id="search_ui" class="w3-input w3-margin-right w3-border w3-light-grey"  onkeyup="filterListUI()" placeholder="Rechercher ... " >
				</div>
				<form class="w3-container  w3-padding" id="form_add_existant_ui">
					<input type="hidden" name="action" value="update_ui" >
					<div class="w3-row w3-light-grey w3-border w3-round w3-padding" id="myUL_ui" style="height:300px;overflow:scroll;">
						<?php
						foreach ( $_SESSION['ui'] as $ui ): ?>
							<div class="w3-half">
								<input class="w3-checkbox" type="checkbox" name="liste_ui[]" value="<?= $ui['id']; ?>" id="ui_<?= $ui['id']; ?>" >
								<?= $ui['nom']; ?> </input>
							</div>
						<?php endforeach; ?>
					</div>
					<button class="w3-btn w3-border w3-hover-border-teal w3-hover-white w3-teal w3-margin-top w3-right"><i class="fa fa-save"></i> Enregistrer</button>
				</form>
			</div>
		</div>
	</div>
<?php
	return ob_get_clean();
}

echo CodeCompressor::compress_html(page());

CodeCompressor::importer("vue/ajax/organisationScript.php" , "js") ;

?>
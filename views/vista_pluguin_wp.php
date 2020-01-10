<?php

 ?>

 <div id="titol_vista_pluguin" class="panel panel-primary">
  <div class="panel-body">
			<div class="logo_col col-xs col-sm-fit logo_documental_pluguin">
					<img src="<?php echo plugins_url("woocomercestockwp/assets/img/logocompsa.png"); ?>" />
          <?php echo "<br><p class='versioAplicacio'>Versió 1.1</p>" ?>
          <?php echo "<br><p class='versioAplicacio'>Desarrollat per Eloi</p>" ?>
			</div>
  </div>
</div>

<div id="cos_vista_pluguin">
    <div id="contenedor_del_cos" class="col-md-12">
        <div id="cos_primerPerfil" class="panel panel-primary">
            <div class="panel-body">

                <div id="opcions_parametres">
                  <div id="parametres_pluguins">
                      <p class="titol_parametres">Fitxer CSV a Importar</p>
                  </div>

                  <form>
                      <div class="row">
                        <div class="col-md-12">

                          <label for="documental"><?php echo get_home_path()."TraspasEstocGesta/Estoc.csv" ?></label>
                      </div>
                    </div>
                  </form>


                </div>


								 <div id="opcions_parametres">
                  <div id="parametres_pluguins">
  										<p class="titol_parametres">Que s'actualitzarà</p>
  								</div>

                  <ul style="margin-left: 15px;"><li class="li_boleta">Woocomerce Stock Status i Stock Quantity</li></ul>

                </div>


                <div id="opcions_parametres">
                  <div id="parametres_pluguins">
  										<p class="titol_parametres">Executar</p>
  								</div>

                  <br>

                  <div class="row">
                    <div class="col-md-12">

                      <div class="dades_backups" >
                        <p>Al fer clic s'actualitzara el stock de la Web. També hi ha un cron php que ho faria autmàticament.</p>
                        <form method='post' action='admin-post.php'>
                          <input name='action' type="hidden" value='enviar_execucio_importacio'>
                          <input type="submit" name="submito" id="botoDuplicar" class="button button-primary btn_duplicarFotosAlbum" value="ACTUALITZAR STOCK"></p>
                        </form>


                      </div>
                    </div>
                  </div>
								</div>



						</div>
				</div>
		</div>
</div>

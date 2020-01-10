<?php
/*
Plugin Name: WoocommerceUpdateStockWP
Plugin URI:
Description: Pluguin que augmentara les funcionalitats deL woocommerce
Version: 1.1.0
Author: EloiRoca
Author URI:
License:
License URI:
*/
$versio = "1.1.0";

//Encuarem els estils, JS, etc dins del wordpress
function encuar_estils_pluguin_stock(){
    $versio = "1.1.0";
    wp_enqueue_style( 'style-compsaonline', plugins_url( 'woocomercestockwp/assets/css/estil_gd.css'), array(), $versio);
}

//add_action('wp_enqueue_scripts', 'encuar_estils_pluguin',10);
add_action('admin_enqueue_scripts', 'encuar_estils_pluguin_stock',10);

define( 'COMPSA_PLUGIN_STOCK', __FILE__ );
define( 'COMPSA_PLUGIN_STOCK_DIR', untrailingslashit( dirname( COMPSA_PLUGIN_STOCK ) ) );

function crear_menu_pluguin_stock(){
  add_menu_page('WooStockUpdate', 'WooStockUpdate', 'manage_options', 'gestio-menu-stock', 'crear_menu_stock',plugins_url( 'woocomercestockwp/assets/img/icon.png' ));
}
add_action('admin_menu', 'crear_menu_pluguin_stock');

function crear_menu_stock(){
  require_once COMPSA_PLUGIN_STOCK_DIR.'/views/vista_pluguin_wp.php';
}

add_action('admin_post_enviar_execucio_importacio','executar_importacio');
function executar_importacio(){
    //INICIEM BD
    global $wpdb;

    //ArticlesActualitzats
    $articlesActualitzats=0;

    //Creem l'array dels productes que recollirem de la BD
    $productesBD =array();

    //Agafem el CSV
    $csv = get_home_url().'/TraspasEstocGesta/Estoc.csv';

    //Agafem tots els articles amb estoc de la bd i ho fiquem al array productesBD
    $arrayESTOC = array();
    $SQL_Estoc="select ID, post_title, wp_postmeta.meta_key ESTOC, wp_postmeta.meta_value VALOR_ESTOC FROM wp_posts inner join wp_postmeta on wp_posts.ID = wp_postmeta.post_id where post_type = 'product' and meta_key = '_stock'";
    $rowEstoc = $wpdb->get_results($SQL_Estoc);

    for ($i=0; $i<count($rowEstoc);$i++){
      array_push($productesBD, array('Nom_producte_BD'=>$rowEstoc[$i]->post_title,'ID_producte_BD'=>$rowEstoc[$i]->ID,'estoc_article_BD'=>intval($rowEstoc[$i]->VALOR_ESTOC)));
    }

    //Agafem tots els articles amb el SKU de referencia de la bd i ho fiquem al array
    $arraySKU = array();
    $SQL_Sku="select ID, post_title, wp_postmeta.meta_key SKU, wp_postmeta.meta_value VALOR_SKU FROM wp_posts inner join wp_postmeta on wp_posts.ID = wp_postmeta.post_id where post_type = 'product' and meta_key = '_sku'";
    $rowSku = $wpdb->get_results($SQL_Sku);

    for ($i=0; $i<count($rowSku);$i++){
      foreach ($productesBD as $producteBD => $val) {
        if ($val['ID_producte_BD'] === $rowSku[$i]->ID) {
          $productesBD[$producteBD]['SKU_producte_BD'] = $rowSku[$i]->VALOR_SKU;
        }
      }
    }

    //Recorrem el CSV
    $linea = 0;
    //Obrim el CSV
    $archivo = fopen($csv, "r");
    //Lo recorremos
    while (($datos = fgetcsv($archivo, ",")) == true){
        $num = count($datos);
        $linea++;

        $SKU_article = $datos[0];
        $stock_article = intval($datos[1]);

        for ($row = 0; $row < count($productesBD); $row++) {
            if ($productesBD[$row]['SKU_producte_BD']==$SKU_article){
                  //echo $productesBD[$row]['ID_producte_BD']." ".$productesBD[$row]['SKU_producte_BD']." Estoc BD = ".$productesBD[$row]['estoc_article_BD']." Estoc CSV = ".$stock_article."<br>";
                  $ID_producte_BD = $productesBD[$row]['ID_producte_BD'];
                  $estoc_article_BD = $productesBD[$row]['estoc_article_BD'];

                  if ($stock_article!=$estoc_article_BD){
                      update_post_meta($ID_producte_BD, '_stock', $stock_article);
                      if ($stock_article>0){
                        update_post_meta($ID_producte_BD, '_stock_status', 'instock');

                      }else {
                        update_post_meta($ID_producte_BD, '_stock_status', 'outofstock');

                      }
                      $articlesActualitzats++;
                  }
            }
          }
    }
    //Cerramos el archivo
    fclose($archivo);

   echo $articlesActualitzats." Articles Actualitzats";

   echo "<pre>";
   print_r($productesBD);
   echo "</pre>";

   $wpdb->get_results('insert into wp_logs (log) VALUES ("'.$articlesActualitzats.' Articles actualitzats correctament. Executat a les '.date("Y-m-d H:i:s").'")');
}

//REGISTREM EL CRON
//Aquest shortcode esta ficat en una pagina que va consultan el CRON LINUX
function cron_importacio(){
	do_action('admin_post_enviar_execucio_importacio');
}
add_shortcode('importacio_estoc', 'cron_importacio');

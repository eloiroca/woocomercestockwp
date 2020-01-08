<?php
/*
Plugin Name: WoocommerceUpdateStockWP
Plugin URI:
Description: Pluguin que augmentara les funcionalitats deL woocommerce
Version: 1.0.0
Author: EloiRoca
Author URI:
License:
License URI:
*/
$versio = "1.0.0";
/*include 'controladors\functions_carpetes_gd.php';
include 'controladors\functions_contrasenyes_gd.php';
include 'controladors\functions_codi_gd.php';
include 'controladors\functions_backups_gd.php';*/
//Encuarem els estils, JS, etc dins del wordpress

function encuar_estils_pluguin(){
    $versio = "1.0.0";
    wp_enqueue_style( 'style-compsaonline', plugins_url( 'woocomercestockwp/assets/css/estil_gd.css'), array(), $versio);

}

//add_action('wp_enqueue_scripts', 'encuar_estils_pluguin',10);
add_action('admin_enqueue_scripts', 'encuar_estils_pluguin',10);


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
	
	//ArticlesActualitzats
    $articlesActualitzats=0;
    //Agafem el CSV
    $csv = get_home_url().'/TraspasEstocGesta/Estoc.csv';
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

        //Actualitzem el Stock segons la referencia del Producte
        global $wpdb;
        $ID_producte = $wpdb->get_row( "select * from wp_postmeta where meta_value = ".$SKU_article);

        //Comparem el stock de la BD amb el del fixer, si son diferents l'actualitzem
        $stock_article_BD = $wpdb->get_row( "select * from wp_postmeta where meta_key = '_stock' and post_id = ".$ID_producte->post_id);
        $stock_article_BD = $stock_article_BD->meta_value;

        if ($stock_article_BD!=$stock_article){
            update_post_meta($ID_producte->post_id, '_stock', $stock_article);
            if ($stock_article>0){
              update_post_meta($ID_producte->post_id, '_stock_status', 'instock');
            }else {
              update_post_meta($ID_producte->post_id, '_stock_status', 'outofstock');
            }
            $articlesActualitzats++;
        }
    }
    //Cerramos el archivo
    fclose($archivo);
    //you can access $_POST, $GET and $_REQUEST values here.
    //wp_redirect(admin_url('admin.php?page=your_custom_page_where_form_is'));
   //apparently when finished, die(); is required.
   echo $articlesActualitzats." Articles Actualitzats";
   $wpdb->get_results('insert into wp_logs (log) VALUES ("'.$articlesActualitzats.' Articles actualitzats correctament. Executat a les '.date("Y-m-d H:i:s").'")');
}

//REGISTREM EL CRON
//Aquest shortcode esta ficat en una pagina que va consultan el CRON LINUX
function cron_importacio(){
	do_action('admin_post_enviar_execucio_importacio');
}
add_shortcode('importacio_estoc', 'cron_importacio');
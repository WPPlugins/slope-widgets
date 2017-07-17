<?php
/*
* Plugin Name: Slope Widgets
* Description: Aggiungi gli widget di Slope al sito web WordPress della tua struttura ricettiva! Questo plugin ti permette di posizionare dove vuoi la barra delle prenotazioni, i pacchetti e le promozioni, grazie agli shortcode.
* Version: 1.1.2
* Author: Slope Team
* Author URI: https://www.slope.it/
*/

// INIZIALIZZAZIONE

// Hook
add_action('admin_init', 'slope_init');
add_action('admin_menu', 'slope_add_page');
add_action('admin_enqueue_scripts', 'slope_color_picker');
add_action('wp_enqueue_scripts', 'slope_load_css_js');
add_action('wp_enqueue_scripts', 'slope_layout_select');

function slope_color_picker( $options ) {
  wp_enqueue_style('wp-color-picker');
  wp_enqueue_script('slope-color-picker', plugins_url('js/slope-colorpicker.js', __FILE__ ), array( 'wp-color-picker' ), false, true);
}

function slope_load_css_js() {
  wp_enqueue_style('slope_jquery_ui_style', plugins_url('css/jquery-ui.css', __FILE__ ));
  wp_enqueue_style('slope_css', plugins_url('css/slope-widgets.css', __FILE__ ));
  wp_enqueue_script('slope_js', plugins_url('js/slope-widgets.js', __FILE__ ), array( 'jquery-ui-core', 'jquery-ui-datepicker' ), false, true);
}

// Registra le impostazioni dei campi
function slope_init(){
  register_setting('slope_options', 'slope_options', 'slope_options_validate' );
  add_settings_section('main_section', 'Impostazioni', 'slope_intro', __FILE__);
  add_settings_field('slope_uuid', 'ID struttura', 'slope_set_uuid', __FILE__, 'main_section');
  add_settings_field('slope_button_value', 'Testo del pulsante invio', 'slope_set_button_value', __FILE__, 'main_section');
  add_settings_field('slope_button_color', 'Colore del pulsante invio', 'slope_set_button_color', __FILE__, 'main_section');
  add_settings_field('slope_show_children', 'Mostra selezione bambini', 'slope_set_show_children', __FILE__, 'main_section');
  add_settings_field('slope_show_infants', 'Mostra selezione neonati', 'slope_set_show_infants', __FILE__, 'main_section');
  add_settings_field('slope_children_age', 'Fascia di età dei bambini espressa in anni', 'slope_set_children_age', __FILE__, 'main_section');
  add_settings_field('slope_layout', 'Scegli un layout', 'slope_set_layout', __FILE__, 'main_section');
}

// Aggiunge la voce al menù laterale
function slope_add_page() {
  add_menu_page( 'Impostazioni di Slope Widgets', 'Slope Widgets', 'manage_options', __FILE__, 'slope_options_page', plugins_url('images/icon.png', __FILE__));
}

// CALLBACK

// HTML mostrato prima delle impostazioni
function slope_intro() {
  echo '<p>Personalizza il widget di prenotazione di Slope. Usa lo shortcode <strong>[slope-reservations]</strong> per inserirlo dove vuoi!</p>';
}

// Text field: slope_options[uuid]
function slope_set_uuid() {
  $options = get_option('slope_options');
  echo "<input id='slope_uuid' name='slope_options[uuid]' size='40' type='text' value='{$options['uuid']}' placeholder='Il tuo Slope ID' />";
}

// Text field: slope_options[button_value]
function slope_set_button_value() {
  $options = get_option('slope_options');
  echo "<input id='slope_button_value' name='slope_options[button_value]' size='40' type='text' value='{$options['button_value']}' placeholder='Es: Prenota' />";
}

// Colorpicker: button_color
function slope_set_button_color() {
  $options = get_option('slope_options');
  echo "<input id='colorpicker' name='slope_options[button_color]' type='text' class='slope_button_color' value='{$options['button_color']}' />";
}
// Checkbox: slope_options[show_children]
function slope_set_show_children() {
  $options = get_option('slope_options');
  if($options['show_children']) { $checked = ' checked="checked" '; }
  echo "<input ".$checked." id='slope_show_children' name='slope_options[show_children]' type='checkbox' />";
}

// Checkbox: slope_options[show_infants]
function slope_set_show_infants() {
  $options = get_option('slope_options');
  if($options['show_infants']) { $checked = ' checked="checked" '; }
  echo "<input ".$checked." id='slope_show_infants' name='slope_options[show_infants]' type='checkbox' />";
}

// Text field: slope_options[children_age]
function slope_set_children_age() {
  $options = get_option('slope_options');
  echo "<input id='slope_children_age' name='slope_options[children_age]' size='40' type='text' value='{$options['children_age']}' placeholder='Es: 0 - 9' />";
}

// Radio: slope_options[option_set]
function slope_set_layout() {
  $options = get_option('slope_options');
  $items = array("Orizzontale", "Verticale", "Automatico");
  foreach($items as $item) {
    $checked = ($options['option_set']==$item) ? ' checked="checked" ' : '';
    echo "<label><input ".$checked." value='$item' name='slope_options[option_set]' type='radio' /> $item</label><br />";
  }

}

// Mostra la pagina di admin delle opzioni
function slope_options_page() {
  ?>
  <div class="wrap">
    <div class="icon32" id="icon-options-general"><br></div>
    <h1>Slope Widgets</h1>
    <p>Inserisci qui di seguito l'identificativo della tua struttura e clicca su <strong>Salva modifiche</strong>.</p>
    <p>Non hai ancora l'ID? <a href="mailto:info@slope.it?subject=Richiesta ID Struttura">Richiedilo ora</a>.</p>
    <form action="options.php" method="post">
      <?php settings_fields('slope_options'); ?>
      <?php do_settings_sections(__FILE__); ?>
      <p class="submit">
        <input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
      </p>
    </form>
  </div>
  <?php
}

// Valida i campi testo per escludere input HTML
function slope_options_validate($input) {
  $input['uuid'] =  wp_filter_nohtml_kses($input['uuid']);
  $input['button_value'] =  wp_filter_nohtml_kses($input['button_value']);
  $input['children_age'] =  wp_filter_nohtml_kses($input['children_age']);
  return $input;
}

// Contenuto da mostrare (con shortcode)
function slope_reservations() {

  $options = get_option('slope_options');

  $html = '<div class="slope-widgets-container"><form action="https://booking.slope.it/widgets/search/' . $options['uuid'] . '" method="POST">
    <div class="slope-block"><label>Data di arrivo:</label><input id="arrival" name="reservation[stay][arrival]"></div>
    <div class="slope-block"><label>Data di partenza:</label><input id="departure" name="reservation[stay][departure]"></div>
    <div class="slope-block"><label>Adulti:</label><select name="reservation[guests][adults]" id="adults">
        <option value="1">1</option>
        <option selected value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
        <option value="7">7</option>
        <option value="8">8</option>
        <option value="9">9</option>
    </select></div>';
    if (($options['show_children']) && ($options['show_infants'])) {
      $html .= '<div class="slope-block"><label>Bambini (età '. $options['children_age'] .'):</label>
      <select name="reservation[guests][children]" id="children">
        <option selected value="0">0</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
        <option value="7">7</option>
        <option value="8">8</option>
        <option value="9">9</option>
      </select></div>
      <div class="slope-block"><label>Neonati:</label>
      <select name="reservation[guests][infants]" id="infants">
        <option selected value="0">0</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
        <option value="7">7</option>
        <option value="8">8</option>
        <option value="9">9</option>
      </select></div>';
    }
  else if ($options['show_children']) {
    $html .= '<div class="slope-block"><label>Bambini (età '. $options['children_age'] .'):</label>
    <select name="reservation[guests][children]" id="children">
      <option selected value="0">0</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
        <option value="7">7</option>
        <option value="8">8</option>
        <option value="9">9</option>
      </select></div>';
  }
  else if ($options['show_infants']) {
    $html .= '<div class="slope-block"><label>Neonati:</label>
    <select name="reservation[guests][infants]" id="infants">
      <option selected value="0">0</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
        <option value="7">7</option>
        <option value="8">8</option>
        <option value="9">9</option>
      </select></div>';
  }

  return $html . '<div class="slope-block"><input type="submit" value="' . $options['button_value'] . '"
  style="background:' . $options['button_color'] . '"></div></form></div>' . $content;
}

add_shortcode( 'slope-reservations', 'slope_reservations' );

// Carica il CSS in base al layout scelto
function slope_layout_select() {

  $options = get_option('slope_options');

  if ( $options['option_set'] == 'Orizzontale') {
    wp_enqueue_style('slope_horizontal_css', plugins_url('css/slope-widgets-horizontal.css', __FILE__ ));
  }

  else if ( $options['option_set'] == 'Verticale') {
    wp_enqueue_style('slope_vertical_css', plugins_url('css/slope-widgets-vertical.css', __FILE__ ));
  }

}
?>

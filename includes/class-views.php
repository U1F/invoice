<?php

// Daniel 26.06.2021 - ggf komplett löschen, da nicht relevant

require_once plugin_dir_path(__FILE__) . '../includes/class-constants.php';

if (!class_exists('QMLI_Mailing_Views')) {
  class QMLI_Mailing_Views
  {
    public function __construct()
    {
    }


    public function get_location_room_form()
    {
      $id = sanitize_key($_REQUEST['id']);
      $location_id = sanitize_key($_REQUEST['location_id']);
?>
      <h2><?php esc_html_e('Neue Räume'); ?></h2>
      <form name="add_location_room" method="POST">
        <?php
        if (isset($id) && strlen($id)) {
          echo "<input type='hidden' name='id' value='$id'>";
        }
        if (isset($location_id) && strlen($location_id)) {
          echo "<input type='hidden' name='location_id' value='" . esc_attr($location_id) . "'>";
        }
        ?>
        <table>
          <tr class="form-field">
            <th scope="row"><label for="name"><?php esc_html_e('Name', 'q-mailing-pro'); ?></label></th>
            <td>
              <input type="text" name="name" value="">
            </td>
          </tr>

          <tr class="form-field">
            <th scope="row"><label for="amount"><?php esc_html_e('Kapazität', 'q-mailing-pro'); ?></label></th>
            <td>
              <input type="text" name="amount" value="" class="q_loc_amount">
            </td>
          </tr>

          <tr class="form-field">
            <th scope="row"><label for="comment"><?php esc_html_e('Kommentar', 'q-mailing-pro'); ?></label></th>
            <td>
              <textarea name="comment" cols="50" rows="3"></textarea>
            </td>
          </tr>
        </table>
        <p class="submit">
          <input type="submit" name="submit" class="button button-primary button-large button-submit add_room" value="<?php esc_attr_e('Eintragen', 'q-mailing-pro'); ?>">
        </p>
      </form>
    <?php
      exit;
    }





    public function get_genre_form()
    {
    ?>
      <h2><?php esc_html_e('Neues Genre', 'q-mailing-pro'); ?></h2>
      <form name="add_genre" method="POST">
        <table>
          <tr class="form-field">
            <th scope="row"><label for="genre"><?php esc_html_e('Genre', 'q-mailing-pro'); ?></label></th>
            <td>
              <input type="text" name="genre" value="" require>
            </td>
          </tr>
        </table>
        <p class="submit">
          <input type="submit" name="submit" class="button button-primary button-large button-submit add_genre" value="<?php esc_attr_e('Eintragen', 'q-mailing-pro'); ?>">
        </p>
      </form>
    <?php
      exit;
    }

    public function get_genre_table()
    {
      ob_clean();

      $genres = $_REQUEST['genres'];

      if (!$genres || count($genres) == 0) {
        echo '';
        exit;
      }
    ?>
      public function get_typ_form()
      {
      ?>
      <h2><?php esc_html_e('Neuer Typ', 'q-mailing-pro'); ?></h2>
      <form name="add_typ" method="POST">
        <table>
          <tr class="form-field">
            <th scope="row"><label for="typ"><?php esc_html_e('Typ', 'q-mailing-pro'); ?></label></th>
            <td>
              <input type="text" name="typ" value="" require>
            </td>
          </tr>
        </table>
        <p class="submit">
          <input type="submit" name="submit" class="button button-primary button-large button-submit add_typ" value="<?php esc_attr_e('Eintragen', 'q-mailing-pro'); ?>">
        </p>
      </form>
<?php
      exit;
    }
  }
} // end of class QMLI_Mailing_Views
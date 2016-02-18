<div>
  <div class="button add-igft-post"><?= __('Neuen Beitrag hinzufügen') ?></div>
  
  <ul id="igft_post-list">
    <?php 
    $igft_posts = get_post_meta( $post->ID, 'newsletter_igft_posts', TRUE );
      // echo $field['id'];
      // print_r( get_post_meta( $post->ID ) );
      // $meta=array();
    $index = 1;
    if( $igft_posts ):
      $used_ids = array();
      foreach ($igft_posts as $igft_post_id ): 
        if( in_array( $igft_post_id, $used_ids ))
          break;
        array_push( $used_ids, $igft_post_id );
        $igft_post = get_post( $igft_post_id );
          // print_r( $igft_post );
      // print_r( get_post_meta( $igft_post_id ) );
      ?>
      <li id="igft-<?= $index ?>">
        <input type="text" placeholder="Beitragstitel" class="igft-title" value=<?= $igft_post->post_title ?> >
        <div class="igft-options">
          <div class="toggle-editor">
            <span class="dashicons dashicons-arrow-up-alt2"></span>
            <span class="dashicons dashicons-arrow-down-alt2 arrow-down"></span>
            <span class="editor-text"> Editor aus-/ einklappen </span>
          </div>
          <?php $menus = get_terms( 'menu' );

            $cur_menu = wp_get_post_terms( $igft_post_id, 'menu', array("fields" => "slugs") )[0];
            $cur_submenu = wp_get_post_terms( $igft_post_id, 'submenu', array("fields" => "slugs") )[0];
           // print_r($menus);?>
          <select class="menu-select">
            <option value=""> Menü auswählen </option>
            <?php foreach($menus as $item) { ?>
            <option value="<?= $item->slug ?>" <?= $cur_menu == $item->slug ? ' selected="selected"' : '' ?> >
              <?= $item->name ?>
            </option>
            <?php  } ?>
          </select>
          <?php $menus = get_terms( 'submenu' ); ?>
          <select class="submenu-select">
            <option value=""> Submenü auswählen </option>
            <?php foreach($menus as $item) { ?>
            <option value="<?= $item->slug ?>"  <?= $cur_submenu == $item->slug ? ' selected="selected"' : '' ?> >
              <?= $item->name ?>
            </option>
            <?php  } ?>
          </select>
          <div class="igft-position">
            <span> Position: </span>
            <input type="text" size='1' style="text-align: center;" value=<?= $index ?> def-value=<?= $index ?> >
          </div>
        </div>
        <div class="editor">
          <?php wp_editor( 
            $igft_post->post_content, 
            "igft_".$index."_content", 
            $settings = array('textarea_name'=> 'igft_'.$index.'_editor')  
            ); ?> 
          </div>
          <div class="button button-primary save-igft-post" post-id=<?= $post->ID ?>  igft-post-id="<?= $igft_post_id ?>" editor-id='igft_<?= $index ?>_content' ><?= __('Beitrag speichern') ?></div>
          <input type="text" class="igft-post-id" name="<?= $field['id'] ?>[<?= $index-1 ?>]" id="<?= $field['id'] ?>[<?= $index-1 ?>]" value=<?=$igft_post_id?> />
        </li>
        <?php
        $index++;
        endforeach;
        else:
          ?>
        <li id="igft-1">
          <input type="text" placeholder="Beitragstitel" class="igft-title" >
          <div class="editor">
       <!--   <?php wp_editor( 
              "Hier kommt der Inhalt", 
              "igft_1_content", 
              $settings = array('textarea_name'=> 'igft_1_editor')  
              ); ?>  -->
            </div>
            <div class="button button-primary save-igft-post" post-id=<?= $post->ID ?> editor-id='igft_1_content' ><?= __('Beitrag anlegen') ?></div>
            <input type="text" class="igft-post-id" name="<?= $field['id'] ?>[0]" id="<?= $field['id'] ?>[0]" />
          </li>
        <?php endif; ?>
      </ul>
    </div>
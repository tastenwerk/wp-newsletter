jQuery(function($) {

  $('#send-newsletter').on('click', function(){
    $current = $(this);
    $.ajax({
      url: ajaxpagination.ajaxurl,
      type: 'post',
      data: {
        action: 'send_newsletter',
        post_id: $current.attr('post-id')
      },
      success: function( result ) {
        console.log( "after: ", result );
      }
    });
  });

  $('.add-igft-post').on('click', function(){
    $orig = $('#igft_post-list li:last-child');
    $clone = $orig.clone();
    $clone.find('iframe').remove();
    $clone.find('.editor').remove();  
    $clone.find('.igft-title').val('');
    $clone.find('.save-igft-post').removeAttr('igft-post-id');

    editor_id = $clone.find('.save-igft-post').attr('editor-id');

    editor_id = editor_id.replace(/(\d+)/, function(fullMatch, n) {
      return Number(n) + 1;
    });
    $clone.find('.save-igft-post').attr('editor-id', editor_id );

    $clone.find('.igft-post-id').attr('name', function(index, name) {
      return name.replace(/(\d+)/, function(fullMatch, n) {
        return Number(n) + 1;
      });
    })

    $clone.find('.igft-post-id').attr('id', function(index, name) {
      return name.replace(/(\d+)/, function(fullMatch, n) {
        return Number(n) + 1;
      });
    })

    $clone.find('.igft-post-id').val();


    $clone.find('.save-igft-post').html("Beitrag erstellen").show();  
    $clone.find('.igft-options').hide();      

    $clone.attr('id', function(index, name) {
      return name.replace(/(\d+)/, function(fullMatch, n) {
        return Number(n) + 1;
      });
    })
    $clone.insertAfter( $orig );
  });


$(document).on('click', '#igft_post-list .toggle-editor', function(){
  $(this).parent().parent().find('iframe').height(300);
  $(this).parent().parent().find('.editor').slideToggle();
  $(this).parent().parent().find('.save-igft-post').slideToggle();    
  $(this).parent().parent().find('.editor').attr('height', '300px');
});


$(document).on('click', '.change-position', function() {
  $current = $(this).parent();
  post_id = $(this).attr('post-id');
  // igft_post_id = $current.attr('igft-post-id');
  // $current.parent().find('.igft-post-id').val( data['id']  );
  $position = $current.parent().find('.igft-position input');
  console.log( $position, $position.val(), $position.attr('def-value') );
  $.ajax({
    url: ajaxpagination.ajaxurl,
    type: 'post',
    data: {
      action: 'new_igft_position',
      post_id: post_id,
      new_pos: $position.val()-1,
      old_pos: $position.attr('def-value')-1
    },
    success: function( result ) {
      location.reload();
    }
  });

}); 

$(document).on('click', '.save-igft-post', function() {
  $current = $(this);
  title = $current.parent().find('.igft-title').val();
  igft_post_id = $current.attr('igft-post-id');


  var editorID = $current.attr('editor-id');
  if (jQuery('#wp-'+editorID+'-wrap').hasClass("tmce-active"))
    var content = tinyMCE.get(editorID).getContent({format : 'raw'});
  else
    var content = jQuery('#'+editorID).val();

  menu_id = $current.parent().find('.menu-select').val();
  submenu_id = $current.parent().find('.submenu-select').val();
  position =  $current.parent().find('.igft-position input').val();

  $.ajax({
    url: ajaxpagination.ajaxurl,
    type: 'post',
    data: {
      action: 'create_or_update_igft_post',
      post_id: $current.attr('post-id'),
      title: title,
      igft_post_id: igft_post_id,
      content: content,
      menu: menu_id,
      submenu: submenu_id,
      position: position
    },
    success: function( result ) {
      data = JSON.parse( result );
      $current.parent().find('.igft-post-id').val( data['id']  );
      // $position = $current.parent().find('.igft-position input');
      // console.log( $position, $position.val(), $position.attr('def-value') );
      // $.ajax({
      //   url: ajaxpagination.ajaxurl,
      //   type: 'post',
      //   data: {
      //     action: 'new_igft_position',
      //     post_id: $current.attr('post-id'),
      //     new_pos: $position.val()-1,
      //     old_pos: $position.attr('def-value')-1
      //   },
      //   success: function( result ) {
          $('#publishing-action input[name="save"]').click();
      //   }
      // });
        // $('#publishing-action input[name="save"]').click();
      }
    });
});

});
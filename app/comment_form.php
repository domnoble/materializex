<?php

namespace App;


// Customise textarea and submit button
add_filter('comment_form_defaults',function(){
  $defaults = array (
    'title_reply_before'   => '<h3 id="reply-title" class="header card-title">',
    'title_reply'          => __( 'Add a Comment' ),
    'class_submit'         => 'btn right-align',
    'name_submit'          => 'submit',
    'comment_field'        => '<div class="comment-form-comment input-field col s12">
                                 <textarea id="comment" name="comment" cols="45" rows="1" aria-required="true" class="materialize-textarea"></textarea>
                                 <label for="comment">Your Feedback Is Appreciated</label>
                               </div>'
  );
  return $defaults;
});


// Add placeholder for Name and Email
add_filter('comment_form_default_fields',function (array $defaults){
  $defaults = array (
    'author'              => '<div class="row">
                                <div class="comment-form-author input-field col s12 m6">
                                  <i class="material-icons prefix">account_circle</i>
                                  <input id="author" name="author" type="text" value="" size="30" class="validate"/>
                                  <label for="author">Your Name</label>
                                </div>
                              </div>',
    'email'               => '<div class="row">
                                <div class="comment-form-email  input-field col s12 m6">
                                  <i class="material-icons prefix">email</i>
                                  <input id="email" name="email" type="text" value="" size="30" class="validate"/>
                                  <label for="email">Your Email</label>
                                </div>
                              </div>',
    'url'                 => ''
  );
  return $defaults;
});

?>

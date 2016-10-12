jQuery(document).ready(function ($) {

  wpeddit_tab_id = 'link';
	
   $(".logged-in-only").bind("click", function () { 
    //show modal here instead
      $('#myModal').modal();

  });

  $(".share", this).bind("click", function(e) {
      e.preventDefault(), e.stopPropagation();
      var t = $(this).attr("href");
      if (o = $(this).attr("data-action"), "twitter" == o) {
          var a = $(this).attr("title"),
              n = EpicAjax.ph_tweet;
          window.open("http://twitter.com/share?url=" + t + "&text=" + a , "twitterwindow", "height=255, width=550, top=" + ($(window).height() / 2 - 225) + ", left=" + ($(window).width() / 2 - 275) + ", toolbar=0, location=0, menubar=0, directories=0, scrollbars=0")
      } else if ("facebook" == o) {
          var r = document.title;
          window.open("http://www.facebook.com/sharer.php?u=" + encodeURIComponent(t) + "&t=" + encodeURIComponent(r), "sharer", "status=0,width=626,height=436, top=" + ($(window).height() / 2 - 225) + ", left=" + ($(window).width() / 2 - 313) + ", toolbar=0, location=0, menubar=0, directories=0, scrollbars=0")
      } else "google" == o && window.open("https://plus.google.com/share?url=" + encodeURIComponent(t), "Share", "status=0,width=626,height=436, top=" + ($(window).height() / 2 - 225) + ", left=" + ($(window).width() / 2 - 313) + ", toolbar=0, location=0, menubar=0, directories=0, scrollbars=0")
  });



  $('.author .post-order li a').bind('click',function(e){
      $('.post-order li a').removeClass('active');
      $(this).addClass('active');
      var tab = $(this).attr('id');
      //debug .console.log(tab);
      $('.wpeddit-author-tab').addClass('hide');
      $('#wpeddit-author-' + tab).removeClass('hide');
  });

  $('.single .post-order li a').bind('click',function(e){
      $('.post-order li a').removeClass('active');
      $(this).addClass('active');
  });

  //handle the home navigation switching 
  $('.home .navigation-banner .post-order li a').bind('click',function(e){
    //debug .console.log('home navigation banner clicked');
    e.preventDefault();
    eleid = $(this).attr('id');
    //debug .console.log('element id is ' + eleid);
    if($(this).hasClass('active')){
      return false;
    }else{
      //debug .console.log('switching tabs');
      $('.post-order li a').removeClass('active');
      $(this).addClass('active');
      //lets get the contents of #main 

      var wpeddit_url = $('#wpeddit_post_url').data('url');
      var wpeddit_paged = $('#wpedditpaged').data('page');
      //debug .console.log(wpeddit_url);
      $('.loading').show();
      $.get( wpeddit_url, { tab: eleid, wpedditpaged: wpeddit_paged })
        .done(function( data ) {
          var content = $(data).find('.site-main').html();
          $('#main').html(($(data).find('.site-main').html()));
          $('.loading').fadeOut(1000);
          wpeddit_bind();
      });
    }
  });


    jQuery(".trigger-upload").unbind("click").bind("click", function(e) {
        wpeddit_upload_media();
    })

  $('.category .navigation-banner .post-order li a').bind('click',function(e){
    //debug .console.log('home navigation banner clicked');
    e.preventDefault();
    eleid = $(this).attr('id');
    //debug .console.log('element id is ' + eleid);
    if($(this).hasClass('active')){
      return false;
    }else{
      //debug .console.log('switching tabs');
      $('.post-order li a').removeClass('active');
      $(this).addClass('active');
      //lets get the contents of #main 

      var wpeddit_url = $('#wpeddit_post_url').data('url');
      var wpeddit_paged = $('#wpedditpaged').data('page');
      var wpeddit_cat = $('#wpeddit_cat').data('cat');
      //debug .console.log(wpeddit_url);
      $('.loading').show();
      $.get( wpeddit_url, { tab: eleid, wpedditpaged: wpeddit_paged, cat: wpeddit_cat })
        .done(function( data ) {
          var content = $(data).find('.site-main').html();
          $('#main').html(($(data).find('.site-main').html()));
          $('.loading').fadeOut(1000);
          wpeddit_bind();
      });
    }
  });


  $('.subred-nav .post-order li a').bind('click',function(e){
    e.preventDefault();
    eleid = $(this).attr('id');
    //debug .console.log('element id is ' + eleid);
    $('.wpeddit-sub-tab').addClass('hide');
    $('#wpeddit-'+eleid).removeClass('hide');
      $('.post-order li a').removeClass('active');
      $(this).addClass('active');

  });


  $("#wpeddit_submit .tabs li").bind("click",function(e){
      wpeddit_tab_id = $(this).attr('id');
      $('#wpeddit_submit .tabs .wpeddit-tab').removeClass('active');
      $(this).addClass('active');
      $('.tab-content .tab').addClass('hide');
      //debug .console.log(wpeddit_tab_id);
      $('.'+wpeddit_tab_id+'-tc').removeClass('hide');
  });

  $('#wpedditfront').bind("click",function(e){
    e.preventDefault();
    //debug .console.log('submitting form clicked');
    //debug .console.log('submitting a ' + wpeddit_tab_id);


        nonce = jQuery("#wpedditsub-ajax-nonce").val();

        //link fields
        if(wpeddit_tab_id == 'link'){
          var url = jQuery("#wpeddit-url").val();
          var image = jQuery("#wpeddit-img-url").val();
          var title = jQuery("#wpeddit-link-title").val();
          var cat = jQuery("#cat").val();
          var content = '';
        }else{
          var url = '';   //no image on text posts
          var image = ''; //no url on text posts
          var title = jQuery("#wpeddit-text-title").val();
          var cat = jQuery("#textcat").val();  
          var content = jQuery("#wpeddit-text-content").val();
        }

        var t = {
                action: "wpeddit_submitnew",
                security: nonce,
                url: url,
                image: image,
                title: title,
                cat: cat,
                type: wpeddit_tab_id,
                content: content
            }

            //debug .console.log(t);

            i = jQuery.ajax({
                url: EpicAjax.ajaxurl,
                type: "POST",
                data: t,
                dataType: "json"
            });
        i.done(function(m) {
          //debug .console.log(m);
          window.location.replace(m.perma);
        }), i.fail(function() {
        })



  });


  $(".wpeddit-sub").unbind("click").bind("click",function(e){
    //debug .console.log('element clicked');

    if(!wpeddit_allow()){
      $('#myModal').modal();
      return false;
    }
    var ele = $(this);
    var term_id = $(this).data('wpsid');

    if($(this).hasClass('wpeddit-subscribe')){
      wpaction = 'wpsub';
      ele.removeClass('wpeddit-subscribe').addClass('wpeddit-unsubscribe').html('unsubscribe');
    }else{
      wpaction = 'wpunsub';
      ele.removeClass('wpeddit-unsubscribe').addClass('wpeddit-subscribe').html('subscribe');
    }
    e.preventDefault();
    //debug .console.log(wpaction);
    var j = {
        action: wpaction,
        wpeddit_term: term_id,
    };
    
    var l = $.ajax({
        url: EpicAjax.ajaxurl,
        type: "POST",
        data: j,
        dataType: "json",
    });
    
    l.done(function (c) {
    });
    
    l.fail(function (d, c) {
        //debug .console.log(c);
    });    

  });




  wpeddit_bind();
    
    
    
  $('#thumbnail').change(function() {
    var thumb = $('#thumbnail').val();
    $('#thumbprev').html("<img src = " + thumb + ">");
  });

});   //end document ready


function wpeddit_allow(){
    if(window.wpeddit_loggedin == 'no'){
      return false;
    }else{
      return true;
    }
}

    function wpeddit_upload_media() {
        var e = null,
            t = wp.media.controller.Library.extend({
                defaults: _.defaults({
                    id: "insert-image",
                    title: "Upload media",
                    allowLocalEdits: !1,
                    displaySettings: !1,
                    displayUserSettings: !1,
                    multiple: !1,
                    type: "image"
                }, wp.media.controller.Library.prototype.defaults)
            }),
            e = wp.media({
                button: {
                    text: "Select"
                },
                state: "insert-image",
                states: [new t]
            });
        e.on("insert", function() {
            e.close()
        }), e.on("close", function() {
            var t, i = e.state("insert-image").get("selection");
            json = e.state().get("selection").first().toJSON();
            jQuery('#wpeddit-image-here').attr('src',json.url);
            jQuery('.wpeddit-image-wrap').show();
            jQuery('.trigger-wrap').hide();
            //debug .console.log(json);
            jQuery('#wpeddit-img-url').val(json.url);
         //   jQuery(".media-items").append('<div class="media-parent"><div class="media-item" style="background-image:url(' + json.url + ');" data-aid=""><a class="remove-media" href="#" data-aid="' + json.id + '"><i class="fa fa-times"></i></a></div></div>'), t = {}, t.url = json.url, t.source = "med", pluginHuntTheme_Global.imgArray.push(t), !i.length
        }), e.open()
    }




function wpeddit_bind(){
     

  jQuery('.show-share').bind("click",function(e){
    e.preventDefault();
    if(jQuery(this).hasClass('sharing')){
      jQuery('.post-share').addClass('hide');
      jQuery('.reddit-left .post').css("height","55px");
      jQuery(this).removeClass('sharing');
    }else{
      jQuery(this).addClass('sharing');
      var wpid = jQuery(this).data('wpid');
      console.log('wpid is' + wpid);
      jQuery('.post-share').addClass('hide');
      jQuery('.wpshareblock-'+wpid).removeClass('hide');
      jQuery('.reddit-left .post').css("height","55px");
      jQuery('.post-'+wpid).css("height","188px");
    }
  });

  jQuery('.close-share').bind("click",function(e){
      jQuery('.post-share').addClass('hide');
      jQuery('.reddit-left .post').css("height","55px");
      jQuery('.show-share').removeClass('sharing');   
  });


  jQuery('.show-share-sin').bind("click",function(e){
    e.preventDefault();
    if(jQuery(this).hasClass('sharing')){
      jQuery('.post-share-sin').addClass('hide');
      jQuery('.reddit-left .main-info-wrap').css("height","55px");
      jQuery(this).removeClass('sharing');
    }else{
      jQuery(this).addClass('sharing');
      var wpid = jQuery(this).data('wpid');
      jQuery('.post-share-sin').addClass('hide');
      jQuery('#wpshareblock-'+wpid).removeClass('hide');
      jQuery('.reddit-left .main-info-wrap').css("height","55px");
      jQuery('.post-'+ wpid +'.main-info-wrap').css("height","188px");
    }
  });

  jQuery('.close-share-sin').bind("click",function(e){
      jQuery('.post-share-sin').addClass('hide');
      jQuery('.reddit-left .main-info-wrap').css("height","55px");
      jQuery('.show-share-sin').removeClass('sharing');   
  });

     jQuery(".arrow").bind("click", function () { 

      var logged = window.loggedin;

      if(logged == 'false'){
      return false;
      }

      var like = jQuery(this).attr("data-red-like");
      var id = jQuery(this).attr("data-red-id");
      var curr = jQuery(this).attr("data-red-current");

        
      if(like == 'up'){
      jQuery(this).removeClass("up").addClass("upmod");
      jQuery(".arrow-down-" + id).removeClass("downmod").addClass("down");
      jQuery(".score-" + id).removeClass("unvoted").addClass("likes");
      jQuery(".score-" + id).removeClass("dislikes").addClass("likes");
      var vote = 1;
      }
      if(like == 'down'){
      jQuery(this).removeClass("down").addClass("downmod");
      jQuery(".arrow-up-" + id).removeClass("upmod").addClass("up");
      jQuery(".score-" + id).removeClass("unvoted").addClass("dislikes");
      jQuery(".score-" + id).removeClass("likes").addClass("dislikes");
      var vote = -1;
      }


      var j = {
          action: "epicred_vote",
          poll: id,
          option: vote,
          current: curr,
      };

      var l = jQuery.ajax({
          url: EpicAjax.ajaxurl,
          type: "POST",
          data: j,
          dataType: "json",
      });

      l.done(function (c) {
          var id = c.poll;
      jQuery(".score-" + id).html(c.vote);
      });

      l.fail(function (d, c) {
          alert("Request failed: " + c)
      });

      return true
    });
    
    
   jQuery(".arrowc").bind("click", function () { 
    
        var logged = window.loggedin;

        if(logged == 'false'){
          return false;
        }
        
        var like = jQuery(this).attr("data-red-like");
        var id = jQuery(this).attr("data-red-id");
        var curr = jQuery(this).attr("data-red-current");
        
            
        if(like == 'up'){
          jQuery(this).removeClass("up").addClass("upmod");
          jQuery(".arrowc-down-" + id).removeClass("downmod").addClass("down");
          jQuery(".scorec-" + id).removeClass("unvoted").addClass("likes");
          jQuery(".scorec-" + id).removeClass("dislikes").addClass("likes");
          var vote = 1;
        }
        if(like == 'down'){
          jQuery(this).removeClass("down").addClass("downmod");
          jQuery(".arrowc-up-" + id).removeClass("upmod").addClass("up");
          jQuery(".scorec-" + id).removeClass("unvoted").addClass("dislikes");
          jQuery(".scorec-" + id).removeClass("likes").addClass("dislikes");
          var vote = -1;
        }

          
          var j = {
              action: "epicred_vote_comment",
              poll: id,
              option: vote,
              current: curr,
          };
          
          var l = jQuery.ajax({
              url: EpicAjax.ajaxurl,
              type: "POST",
              data: j,
              dataType: "json",
          });
          
          l.done(function (c) {
              var id = c.poll;
        jQuery(".scorec-" + id).html(c.vote);
          });
          
          l.fail(function (d, c) {
              alert("Request failed: " + c)
          });
          
          return true
    });
}

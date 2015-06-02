/**
 *  Simple JQuery to handle ezy-front-image-upload plugins
 *  @author Nil
 */

(function(){
    // Uploading files
    var counter=1,target,ajaxurl = jQuery('div.ezy-wrapper').data('adminurl'),postId = jQuery('div.ezy-wrapper').data('post-id');
    
        jQuery(document).ready(function() {
            jQuery("img.ezy-image").load(function() {
                jQuery(this).wrap(function(){
                  return '<span class="image-wrap ' + jQuery(this).attr('class') + '" style="position:relative; display:inline-block; background:url(' + jQuery(this).attr('src') + ') no-repeat center center; width: ' + jQuery(this).width() + 'px; height: ' + jQuery(this).height() + 'px;" />';
                });
                jQuery(this).css("opacity","0");
            });

            jQuery('li.image').live('hover',
                    function(){
                        var jQuerycaption = jQuery(this).find('span.boxcaption');
                        console.log('testing');
                        jQuerycaption.stop(1);
                        jQuerycaption.slideUp(100);
                    },
                    function(){
                        var jQuerycaption = jQuery(this).find('span.boxcaption');
                        jQuerycaption.stop(1);
                        jQuerycaption.slideDown(100);
                    }
                );

            jQuery('.ezy-simple').bootstrapFileInput();
            
            jQuery('.bxslider').bxSlider({
                auto: true,
                autoControls: false,
                controls: false,
                pager: true,
                mode: 'horizontal',
                speed: 1000,
                autoHover: true,
                pause:  4000,
                easing: 'swing',
                displaySlideQty: 3,
                moveSlideQty: 2
            });

            jQuery('button.ezy-attach-btn').prop("disabled",true);
            jQuery('button.ezy-attach-btn').click(function(){
                document.getElementById("ezy-wrapper").submit();
            });

            jQuery('button.ezy-form-hide').click(function(){
                var text = jQuery(this).data('text');
                if(text == 'hide'){
                    jQuery(this).text('Show')
                    jQuery(this).data('text','show');
                }else{
                    jQuery(this).text('Hide')
                    jQuery(this).data('text','hide');
                }
                jQuery('div.ezy-wrapper').slideToggle( "slow");
                
            });


            jQuery('span.remove-me').live('click',function(event){
                parent = jQuery(this).closest('div');
                parent.remove();
            });

        }); 

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();            
            reader.onload = function (e) {
                jQuery('img.ezy-preview').attr('src', e.target.result);
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    jQuery(".ezy-image-name").change(function(){
        readURL(this);
    });
        

    jQuery('.click-me').live('click', function( event ){
        parent = jQuery(this).closest('div');
        event.preventDefault();
        // If the media frame already exists, reopen it.
        if ( undefined !== file_frame ) {
            file_frame.open();
         return;
     
        }
    }); 


    jQuery('button.add-more').on('click', function( event ){
        var cloneElement = jQuery('div.ezy-upload').clone();
        cloneElement.removeClass('ezy-upload').addClass('ezy-upload-'+counter);
        cloneElement.append('<span class="remove-me">-</span>');
        counter++;
        jQuery('div #ezy-wrapper').append(cloneElement);
    });
})();
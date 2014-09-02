jQuery.configPrettyPhotoConClase=function(clase){
    //alert('configPrettyPhoto');
$("a[rel^='prettyPhoto']"+clase).prettyPhoto({
     custom_markup: '<div id="map_canvas" style="width:260px; height:265px"></div>',
     animation_speed: 'fast',
     overlay_gallery: false,
     keyboard_shortcuts: true,
     deeplinking: true,
     theme: 'pp_default',
     ie6_fallback: true,
     markup: '<div class="pp_pic_holder"> \
          <div class="ppt">&nbsp;</div> \
          <div class="pp_top"> \
        <div class="pp_left"></div> \
        <div class="pp_middle"></div> \
        <div class="pp_right"></div> \
          </div> \
          <div class="pp_content_container"> \
             <div class="pp_left"> \
         <div class="pp_right"> \
         <div class="pp_content"> \
         <div class="pp_loaderIcon"></div> \
         <div class="pp_fade"> \
         <a class="pp_close pp_close_rich" href="#" style="cursor:pointer;width:30px;height:30px;position:absolute;right:-8px;text-indent:-10000px;top:-24px;z-index:20000">Close</a> \
         <div class="pp_hoverContainer"> \
         <a class="pp_next" href="#">next</a> \
         <a class="pp_previous" href="#">Anterior</a> \
         </div> \
         <div id="pp_full_res"></div> \
         <div class="pp_details"> \
         <div class="pp_nav"> \
         <a href="#" class="pp_arrow_previous">Previous</a> \
         <p class="currentTextHolder">0/0</p> \
         <a href="#" class="pp_arrow_next">Siguiente</a> \
         </div> \
         <p class="pp_description"></p> \
         {pp_social} \
        </div> \
        </div> \
        </div> \
        </div> \
        </div> \
        </div> \
        <div class="pp_bottom"> \
        <div class="pp_left"></div> \
        <div class="pp_middle"></div> \
        <div class="pp_right"></div> \
        </div> \
        </div> \
        <div class="pp_overlay"></div>',
        social_tools: ''
    
       
  });
}

jQuery.configPrettyPhoto=function(){
    //alert('configPrettyPhoto');
$("a[rel^='prettyPhoto']").prettyPhoto({
     custom_markup: '<div id="map_canvas" style="width:260px; height:265px"></div>',
     animation_speed: 'fast',
     overlay_gallery: false,
     keyboard_shortcuts: true,
     deeplinking: true,
     theme: 'pp_default',
     ie6_fallback: true,
     markup: '<div class="pp_pic_holder"> \
	      <div class="ppt">&nbsp;</div> \
	      <div class="pp_top"> \
		<div class="pp_left"></div> \
		<div class="pp_middle"></div> \
		<div class="pp_right"></div> \
	      </div> \
	      <div class="pp_content_container"> \
	         <div class="pp_left"> \
		 <div class="pp_right"> \
		 <div class="pp_content"> \
		 <div class="pp_loaderIcon"></div> \
		 <div class="pp_fade"> \
		 <a class="pp_close pp_close_rich" href="#" style="cursor:pointer;width:30px;height:30px;position:absolute;right:-8px;text-indent:-10000px;top:-24px;z-index:20000">Close</a> \
		 <div class="pp_hoverContainer"> \
		 <a class="pp_next" href="#">next</a> \
		 <a class="pp_previous" href="#">Anterior</a> \
		 </div> \
		 <div id="pp_full_res"></div> \
		 <div class="pp_details"> \
		 <div class="pp_nav"> \
		 <a href="#" class="pp_arrow_previous">Previous</a> \
		 <p class="currentTextHolder">0/0</p> \
		 <a href="#" class="pp_arrow_next">Siguiente</a> \
		 </div> \
		 <p class="pp_description"></p> \
		 {pp_social} \
		</div> \
		</div> \
		</div> \
		</div> \
		</div> \
		</div> \
		<div class="pp_bottom"> \
		<div class="pp_left"></div> \
		<div class="pp_middle"></div> \
		<div class="pp_right"></div> \
		</div> \
		</div> \
		<div class="pp_overlay"></div>',
        social_tools: ''
	
       
  });
}

jQuery.configPrettyPhotoDark=function(){
   //alert('configPrettyPhoto');
$("a[rel^='prettyPhoto']").prettyPhoto({
     custom_markup: '<div id="map_canvas" style="width:260px; height:265px"></div>',
     animation_speed: 'fast',
     overlay_gallery: false,
     keyboard_shortcuts: true,
     deeplinking: true,
     theme: 'dark_rounded',
     ie6_fallback: true,
     markup: '<div class="pp_pic_holder"> \
	      <div class="ppt">&nbsp;</div> \
	      <div class="pp_top"> \
		<div class="pp_left"></div> \
		<div class="pp_middle"></div> \
		<div class="pp_right"></div> \
	      </div> \
	      <div class="pp_content_container"> \
	         <div class="pp_left"> \
		 <div class="pp_right"> \
		 <div class="pp_content"> \
		 <div class="pp_loaderIcon"></div> \
		 <div class="pp_fade"> \
		 <a class="pp_close pp_close_rich" href="#" style="cursor:pointer;width:100px;height:25px;position:absolute;right:-8px;text-indent:-10000px;top:-24px;z-index:20000">Close</a> \
		 <div class="pp_hoverContainer"> \
		 <a class="pp_next" href="#">next</a> \
		 <a class="pp_previous" href="#">Anterior</a> \
		 </div> \
		 <div id="pp_full_res"></div> \
		 <div class="pp_details"> \
		 <div class="pp_nav"> \
		 <a href="#" class="pp_arrow_previous">Previous</a> \
		 <p class="currentTextHolder">0/0</p> \
		 <a href="#" class="pp_arrow_next">Siguiente</a> \
		 </div> \
		 <p class="pp_description"></p> \
		 {pp_social} \
		</div> \
		</div> \
		</div> \
		</div> \
		</div> \
		</div> \
		<div class="pp_bottom"> \
		<div class="pp_left"></div> \
		<div class="pp_middle"></div> \
		<div class="pp_right"></div> \
		</div> \
		</div> \
		<div class="pp_overlay"></div>',
        social_tools: ''
	
       
  });
}
jQuery.getFileTypeForUrl=function(urlRpsStm){
	if (urlRpsStm.match(/youtube\.com\/watch/i)) {
            return 'youtube';
	}else if (urlRpsStm.match(/vimeo\.com/i)) {
            return 'vimeo';
	}else if(urlRpsStm.match(/\b.mov\b/i)){
            return 'quicktime';
	}else if(urlRpsStm.match(/\b.swf\b/i)){
            return 'flash';
        }else if(urlRpsStm.match(/\biframe=true\b/i)){
            return 'iframe';
	}else if(urlRpsStm.match(/\bajax=true\b/i)){
            return 'ajax';
	}else if(urlRpsStm.match(/\bcustom=true\b/i)){
            return 'custom';
	}else if(urlRpsStm.substr(0,1) == '#'){
            return 'inline';
	}else{
            return 'image';
	};
}
jQuery.getParamUrlRpsStm=function(name,url){
  name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
  var regexS = "[\\?&]"+name+"=([^&#]*)";
  var regex = new RegExp( regexS );
  var results = regex.exec( url );
  return ( results == null ) ? "" : results[1];
}
/*
 * urlRpsStm: la direccion del recurso [video, imagen, link, direccion url, etc...]
 * idRpsStm: identificador del objeto que se va a crear
 * idContentUrl: identificador del objeto que es el contenedor de la urlRpsStm
 * idImageLoader: identificador de la imagen que se muestra para indicar que se esta cargando
 *
 */
jQuery.showContentUrlRpsStm = function(urlRpsStm,idRpsStm,idContentUrl,idImageLoader) {

    alert(urlRpsStm);
    
    var image_markup= '<div style="position: absolute;left: 50%;top: 50%; margin-left: -150px; margin-top: -150px; width:400px;height:400px;"><img src="{path}" class="image-dialog" id="{idRpsStm}" style="max-height: 400px; max-width: 400px;"/></div>',
	flash_markup= '<object id="{idRpsStm}" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="{width}" height="{height}"><param name="wmode" value="{wmode}" /><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="{path}" /><embed src="{path}" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="{width}" height="{height}" wmode="{wmode}"></embed></object>',
	quicktime_markup= '<object id="{idRpsStm}" classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab" height="{height}" width="{width}"><param name="src" value="{path}"><param name="autoplay" value="{autoplay}"><param name="type" value="video/quicktime"><embed src="{path}" height="{height}" width="{width}" autoplay="{autoplay}" type="video/quicktime" pluginspage="http://www.apple.com/quicktime/download/"></embed></object>',
	iframe_markup= '<iframe id="{idRpsStm}"  src ="{path}" width="{width}" height="{height}" frameborder="no"></iframe>',
	inline_markup= '<div id="{idRpsStm}" style="width:{width}px; height:{height}px" class="pp_inline">{content}</div>',
	custom_markup='',
        content='',
        wFile=0,
        hFile=0;

    $(idImageLoader).show();
    // Inject the proper content
    switch($.getFileTypeForUrl(urlRpsStm)){
        case 'image':
        content=image_markup.replace(/{path}/g,urlRpsStm).replace(/{idRpsStm}/g,idRpsStm);
	$(idContentUrl)
        .html(content.replace(/{idRpsStm}/g,idRpsStm))
        .find(idImageLoader).hide();
        break;

	case 'youtube':
	movie = 'http://www.youtube.com/embed/'+getParamUrlRpsStm('v',urlRpsStm);
	movie += "&autoplay=1";
        wFile=val(getParamUrlRpsStm('width',urlRpsStm));
        hFile=val(getParamUrlRpsStm('height',urlRpsStm));
	content = iframe_markup.replace(/{width}/g,wFile).replace(/{height}/g,hFile).replace(/{wmode}/g,'opaque').replace(/{path}/g,movie);
	$(idContentUrl).html(content.replace(/{idRpsStm}/g,idRpsStm)).find(idImageLoader).hide();
        break;

	case 'vimeo':
	movie_id = urlRpsStm;
	var regExp = /http:\/\/(www\.)?vimeo.com\/(\d+)/;
	var match = movie_id.match(regExp);
        movie = 'http://player.vimeo.com/video/'+ match[2] +'?title=0&amp;byline=0&amp;portrait=0';
	movie += "&autoplay=1;";
	vimeo_width = getParamUrlRpsStm('width',urlRpsStm) + '/embed/?moog_width='+getParamUrlRpsStm('width',urlRpsStm);
        hFile=val(getParamUrlRpsStm('height',urlRpsStm));
	content = iframe_markup.replace(/{width}/g,vimeo_width).replace(/{height}/g,hFile).replace(/{path}/g,movie);
        $(idContentUrl).html(content.replace(/{idRpsStm}/g,idRpsStm)).find(idImageLoader).hide();
	break;

	case 'quicktime':
	case 'flash':
	case 'iframe':
	frame_url = urlRpsStm;
	frame_url = frame_url.substr(0,frame_url.indexOf('iframe')-1);
        wFile=val(getParamUrlRpsStm('width',urlRpsStm));
        hFile=val(getParamUrlRpsStm('height',urlRpsStm));
	content = iframe_markup.replace(/{width}/g,wFile).replace(/{height}/g,hFile).replace(/{path}/g,frame_url);
        $(idContentUrl).html(content.replace(/{idRpsStm}/g,idRpsStm)).find(idImageLoader).hide();
	break;

	case 'ajax':
	case 'inline':
	$.get(urlRpsStm,function(responseHTML){
	content = inline_markup.replace(/{content}/g,responseHTML);
	$(idContentUrl).html(content.replace(/{idRpsStm}/g,idRpsStm)).find(idImageLoader).hide();
	});
	break;

        case 'custom':
        $(idContentUrl).show().find(idImageLoader).hide();
	break;

	};

    return true;
}
jQuery.fn.formAjaxNewsletterRichpolis = function(){
        var form = $(this);
        form.zinePretifyForm();
        form.submit(handleFormSubmit);
        form.find('input[type=submit]').click(handleFormSubmit);

        var submitFlag = false;

        function handleFormSubmit(){
            if(submitFlag){
                return false;
            }
            $("#preloader").show();
            submitFlag = true;

            if(formIsValid()){
                    //alert(form.serialize());
                    $.post(form.attr('action'),form.serialize(),function(data){
                        submitFlag = false;
                        $("#preloader").hide();
                        $('#dialog-form').html(data);
                        submitFlag = false;
                    });

            }else{
                    submitFlag = false;
                    $("#preloader").hide();
           }
           
            return false;
        }

        function displayOverlay(){
        }
        function formIsValid(){
            var name = $( "#sf_luis_lauro_newsletter_name"),
            email = $( "#sf_luis_lauro_newsletter_email"),
            country = $( "#sf_luis_lauro_newsletter_localition");
            allFields = $( [] ).add( name ).add( email ).add( country );

            var bValid = true;
            allFields.removeClass( "ui-state-error" );
            bValid = bValid && checkLength( name, "Name", 3, 70 );
            bValid = bValid && checkLength( country, "Country", 3, 100 );
            bValid = bValid && checkLength( email, "Email", 5, 100 );
            
            return bValid;
        }

        var errorTip=$('.errorTip');
        $('.errorIcon').each(function(index){
            $(this).hover(function(){
             errorTip.eq(index).stop().fadeIn(function(){
                errorTip.eq(index).css('opacity',1);
              });
             },function(){
                 errorTip.eq(index).stop().fadeOut('slow',function(){
                         errorTip.eq(index).hide().css('opacity',1);
                 });
             });
        });

    }
jQuery.fn.formAjaxCommentRichpolis = function(){
        var form = $(this);
        form.zinePretifyForm();
        form.submit(handleFormSubmit);
        form.find('input[type=submit]').click(handleFormSubmit);

        var submitFlag = false;

        function handleFormSubmit(){
            if(submitFlag){
                return false;
            }
            $("#preloader").show();
            submitFlag = true;

            if(formIsValid()){
                    //alert(form.serialize());
                    $.post(form.attr('action'),form.serialize(),function(data){
                        submitFlag = false;
                        $("#preloader").hide();
                        $('#dialog-form').html(data);
                        submitFlag = false;
                    });

            }else{
                    submitFlag = false;
                    $("#preloader").hide();
           }
           
            return false;
        }

        function displayOverlay(){
        }
        function formIsValid(){
            var commentDescription = $( "#sf_el_costeno_blog_article_description");
            allFields = $( [] ).add( commentDescription );

            var bValid = true;
            allFields.removeClass( "ui-state-error" );
            bValid = bValid && checkLength( commentDescription, "Descripcion", 5, 80 );
            
            
            return bValid;
        }

        var errorTip=$('.errorTip');
        $('.errorIcon').each(function(index){
            $(this).hover(function(){
             errorTip.eq(index).stop().fadeIn(function(){
                errorTip.eq(index).css('opacity',1);
              });
             },function(){
                 errorTip.eq(index).stop().fadeOut('slow',function(){
                         errorTip.eq(index).hide().css('opacity',1);
                 });
             });
        });

    }    
jQuery.fn.zinePretifyForm = function(){
        return this.each(function(){

            var form = $(this);

            form.find('input[type=button],input[type=submit]').each(function(){

                var originalButton = $(this),
                    button = $('<span>',{
                        className   : 'button',
                        html        : originalButton.val()+'<span></span>'
                    });

                button.insertAfter(originalButton.hide());

                button.click(function(){
                    originalButton.click();
                });

            });

            form.find('input[type=checkbox]').each(function(){

                var originalCheckBox = $(this),
                    checkBox = $('<span>',{
                        className   : 'checkBox '+(this.checked?'checked':'')
                    });

                checkBox.insertAfter(originalCheckBox.hide());

                checkBox.click(function(){
                    checkBox.toggleClass('checked');
                    originalCheckBox.attr('checked',checkBox.hasClass('checked'));
                });

            });

            form.find('input[type=radio]').each(function(){

                var originalRadio = $(this),
                    radio = $('<span>',{
                        className   : 'radio '+(this.checked?'checked':'')
                    });

                radio.insertAfter(originalRadio.hide());

                radio.click(function(){
                    $('input[type=radio][name='+originalRadio.attr('name')+']').each(function(){
                        $(this).next().removeClass('checked');
                    });

                    radio.addClass('checked');
                    originalRadio.attr('checked',true);
                });

            });

            form.find('select').each(function(i){

                var select = $(this);

                var selectBoxContainer = $('<span>',{
                    width       : select.outerWidth(),
                    className   : 'selectContainer',
                    html        : '<div class="selectBox"></div><span></span>',
                    css         : {zIndex : 1000-i}
                });

                var dropDown = $('<ul>',{className:'dropDown'});
                var selectBox = selectBoxContainer.find('.selectBox');

                select.find('option').each(function(i){
                    var option = $(this);

                    if(i==select.attr('selectedIndex')){
                        selectBox.html(option.text());
                    }

                    var li = $('<li>',{
                        html:   option.html()
                    });

                    li.click(function(){

                        selectBox.html(option.text());
                        dropDown.trigger('hide');

                        select.val(option.val());
                        return false;
                    });

                    dropDown.append(li);
                });

                selectBoxContainer.append(dropDown.hide());
                select.hide().after(selectBoxContainer);

                dropDown.bind('show',function(){

                    if(dropDown.is(':animated')){
                        return false;
                    }

                    selectBox.addClass('expanded');
                    dropDown.slideDown('fast');

                }).bind('hide',function(){

                    if(dropDown.is(':animated')){
                        return false;
                    }

                    selectBox.removeClass('expanded');
                    dropDown.slideUp('fast');

                }).bind('toggle',function(){
                    if(selectBox.hasClass('expanded')){
                        dropDown.trigger('hide');
                    }
                    else dropDown.trigger('show');
                });

                selectBoxContainer.click(function(){
                    dropDown.trigger('toggle');
                    return false;
                });

                $(document).click(function(){
                    dropDown.trigger('hide');
                });
            });

        });
}

function updateTips( t ) {
var tips = $( ".validateTips" );
    tips
	.text( t )
        .addClass( "ui-state-highlight" );
	setTimeout(function() {
	tips.removeClass( "ui-state-highlight", 1500 );
	}, 500 );
}

function checkLength( o, n, min, max ) {

    if ( o.val().length > max || o.val().length < min ) {
        o.addClass( "ui-state-error" ).select();
        updateTips( "Largo de " + n + " debe ser entre " +
		min + " y " + max + "." );
	return false;
    } else {
	return true;
    }
}

function checkRegexp( o, regexp, n ) {
    if ( !( regexp.test( o.val() ) ) ) {
	o.addClass( "ui-state-error" ).select();
	updateTips( n );
	return false;
    } else {
        return true;
    }
}


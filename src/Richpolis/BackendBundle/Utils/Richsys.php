<?php

namespace Richpolis\BackendBundle\Utils;

use Buzz\Browser;

class Richsys
{
    const TIPO_ARCHIVO_IMAGEN=1;
    const TIPO_ARCHIVO_VIDEO=2;
    const TIPO_ARCHIVO_LINK=3;
    const TIPO_ARCHIVO_MUSICA=4;
    const TIPO_ARCHIVO_FLASH=5;
        
    static public $sTipoArchivo=array(
        self::TIPO_ARCHIVO_IMAGEN=>'Imagen',
        self::TIPO_ARCHIVO_VIDEO=>'Video',
        self::TIPO_ARCHIVO_LINK=>'Link',
        self::TIPO_ARCHIVO_MUSICA=>'Musica',
        self::TIPO_ARCHIVO_FLASH=>'Flash',
    );
    
    
    
    static public function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('#[^\\pL\d]+#u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        if (function_exists('iconv'))
        {
          $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        }

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('#[^-\w]+#', '', $text);

        if (empty($text))
        {
          return 'n-a';
        }

        return $text;
    }
    
    static public function getArchivoView(array $opciones){
        $respuesta="";
        $tipoarchivo=$opciones['tipo_archivo'];
        switch($tipoarchivo){
            case self::TIPO_ARCHIVO_IMAGEN:
                $respuesta=Richsys::getArchivoViewImagen($opciones);
                break;
            case self::TIPO_ARCHIVO_LINK:
                $respuesta=Richsys::getArchivoViewLinkVideo($opciones);
                break;
            case self::TIPO_ARCHIVO_VIDEO:
                $respuesta=Richsys::getArchivoViewVideo($opciones);
                break;
            case self::TIPO_ARCHIVO_MUSICA:
                $respuesta= sprintf(<<<EOF
<link href="/css/jplayer.blue.monday.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/js/jquery.jplayer.min.js"></script>
<script>
$(document).ready(function(){
 $("#jquery_jplayer_%s").jPlayer({
  ready: function () {
   $(this).jPlayer("setMedia", {
    mp3: "/uploads/galeria/%s",
    oga: "/uploads/galeria/sound.ogg"
   });
  },
  swfPath: "/swf",
  supplied: "mp3, oga"
 });
}); 
</script>
<div id="jquery_jplayer_%s" class="jp-jplayer"></div>
    <div id="jp_container_1" class="jp-audio">
			<div class="jp-type-single">
				<div class="jp-gui jp-interface">
					<ul class="jp-controls">
						<li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
						<li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>
						<li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>
						<li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>
						<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>
						<li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>
					</ul>
					<div class="jp-progress">
						<div class="jp-seek-bar">
							<div class="jp-play-bar"></div>
						</div>
					</div>
					<div class="jp-volume-bar">
						<div class="jp-volume-bar-value"></div>
					</div>
					<div class="jp-time-holder">
						<div class="jp-current-time"></div>
						<div class="jp-duration"></div>

						<ul class="jp-toggles">
							<li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat">repeat</a></li>
							<li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off">repeat off</a></li>
						</ul>
					</div>
				</div>
				<div class="jp-title">
					<ul>
						<li>Cro Magnon Man</li>
					</ul>
				</div>
				<div class="jp-no-solution">
					<span>Update Required</span>
					To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
				</div>
			</div>
		</div>

EOF
      ,
      $opciones['id'],
      $opciones['archivo'],
      $opciones['id']
                  
    );
                break;
            case self::TIPO_ARCHIVO_FLASH:
                $respuesta=  Richsys::getArchivoViewFlash($opciones);
                break;
        }
        return $respuesta;
     }
     
     
     static public function getArchivoViewImagen(array $opciones2){
        $opciones=array(
            'path'      => 'http://'.$_SERVER['HTTP_HOST'].'/uploads',
            'carpeta'   => 'galeria',
            'archivo'   =>'sin_imagen.jpg',
            'width'     =>'600',
            'height'    =>'400',
            'style'     =>'',
            'title'     =>'',
        ); 
        $texto='<img src="{path}" style="max-width:{width}px;max-height:{height}px;{style}" title="{title}"/>';
        return Richsys::strReplaceOpciones($texto, $opciones, $opciones2);
     }
     
     static public function getArchivoViewLinkVideo(array $opciones2){
         $opciones=array(
            'archivo'  => 'http://vimeo.com/6862605',
            'height'  => '400',
            'width'    =>'560',
            'style' =>'',
            'title' =>'',
            'autoplay' =>'0', 
        );
        $texto="";       
        $video_link=(isset($opciones2['archivo'])?$opciones2['archivo']:$opciones['archivo']);
         if(preg_match('/youtube\.com\/watch/i',$video_link)){
           $texto='<iframe src ="http://www.youtube.com/embed/'.RichSys::getVideoIdYoutube($video_link).'?rel=1&autoplay={autoplay}" width="{width}px" height="{height}px" frameborder="no"></iframe>';
         }elseif(preg_match('/vimeo\.com/i',$video_link)){
            $regExp="/http:\/\/(www\.)?vimeo.com\/(\d+)/";
            preg_match($regExp,$video_link,$matches);
            $texto='<iframe src="http://player.vimeo.com/video/'. $matches[2].'?autoplay={autoplay}" width="{width}px" height="{height}px" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
         }
         return Richsys::strReplaceOpciones($texto, $opciones, $opciones2);
         
     }
     
     static public function getArchivoViewVideo(array $opciones2){
         $opciones=array(
            'path' => 'http://'.$_SERVER['HTTP_HOST'].'/uploads', 
            'carpeta' => 'videos',  
            'archivo'  => 'video.fly',
            'height'  => '330',
            'width'    =>'520',
            'id' =>'player',
            'js' =>'/js/flowplayer-3.2.9.min.js',
            'swf' =>'/swf/flowplayer-3.2.10.swf', 
        );
         
         $texto= <<<EOF
<script type="text/javascript" src="{js}"></script>
<a href="{path}/{carpeta}/{archivo}"
   style="display:block;width:{width}px;height:{height}px"  
   id="{id}"> 
</a> 
<!-- this will install flowplayer inside previous A- tag. -->
<script>
    flowplayer("{id}", "{swf}");
</script>
EOF;
         return Richsys::strReplaceOpciones($texto, $opciones, $opciones2);
         
     }
     
     static public function getArchivoViewFlash(array $opciones2){
         $opciones=array(
            'path' => 'http://'.$_SERVER['HTTP_HOST'].'/uploads', 
            'carpeta' => 'swf',  
            'archivo'  => 'flahs.swf',
            'height'  => '400',
            'width'    =>'600',
            'title' =>'',
            'id' =>'archivo_flash', 
        );
               
        $texto= '<object id="{id}" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="{width}" height="{hegith}"><param name="wmode" value="true" /><param name="allowfullscreen" value="false" /><param name="allowscriptaccess" value="always" /><param name="movie" value="{path}/{carpeta}/{archivo}" /><embed src="{path}/{carpeta}/{archivo}" type="application/x-shockwave-flash" allowfullscreen="false" allowscriptaccess="always" width="{width}" height="{height}" wmode="true"></embed></object>';
        return Richsys::strReplaceOpciones($texto, $opciones, $opciones2);
         
     }
     
     static public function strReplaceOpciones($texto,array $opciones, array $opciones2){
        foreach($opciones2 as $key => $value){
            if(isset($opciones[$key])){
                $opciones[$key]=$value;
            }
        }
        
         foreach($opciones as $key => $value){
             $texto=  str_replace('{'.$key.'}', $opciones[$key], $texto);
         }
         
         return $texto;
     }
     
     static public function createFilenameImage($path,$filename){
        $arregloFile=explode(".", $filename);
        $uploadDirectory=$path;
        while (file_exists($uploadDirectory . $arregloFile[0] . '.' . $arregloFile[1])) {
                 $arregloFile[0] = sha1($arregloFile[0].rand(11111, 99999));
        }
        return $arregloFile[0].'.'.$arregloFile[1];
        
    }
    static public function getTipoMime($archivo){
        $archivo=explode(".", $archivo);
        $resp="image/jpeg";
        switch ($archivo[1]){
            case "png":
                $resp="image/png";
                break;
            case "gif":
                "image/gif";
                break;
            case "jpg":
            case "jpeg":    
              $resp="image/jpeg";
              break;
            case "flv":
            $resp="flv-application/octet-stream";
              break;
            case "mpg":
              $resp="video/mpeg";
              break;
            case "mp4":
              $resp="application/mp4";
              break;  
            case "avi":    
              $resp="video/x-msvideo";
              break;
            default:    
              $resp="image/jpeg";
              break;
        }
        return $resp;
    }
    
    static public function getTipoArchivo($archivo){
        $archivo=explode(".", $archivo);
        $resp=0;
        switch ($archivo[1]){
            case "png":
            case "gif":
            case "jpg":
            case "jpeg":    
              $resp=self::TIPO_ARCHIVO_IMAGEN;
              break;
            case "flv":
            case "mpg":
            case "mp4":
            case "avi":    
              $resp=self::TIPO_ARCHIVO_VIDEO;
              break;
            case "swf":    
              $resp=self::TIPO_ARCHIVO_FLASH;
              break;
            case "mp3":    
              $resp=self::TIPO_ARCHIVO_MUSICA;
              break;
            default:    
              $resp=self::TIPO_ARCHIVO_LINK;
              break;
        }
        return $resp;
    }
    
    /*static public function getTitleAndImageVideoYoutube($link){

        $link=self::getLinkLargeYoutube($link);
        $videoId=self::getVideoIdYoutube($link);


        if(!$videoId==null){
            //$url = sprintf('http://www.youtube.com/oembed?url=http://www.youtube.com/watch?v=%s&format=json', $videoId);
            $url = "http://www.youtube.com/oembed?url=http://www.youtube.com/watch?v=$videoId&format=json";
            $browser = new Browser();
            try {
                $response = $browser->get($url);
            } catch (\RuntimeException $e) {
                throw new \RuntimeException('Unable to retrieve the video information for :' . $url, null, $e);
            }

                $metadata = json_decode($response->getContent(), true);

            if (!$metadata) {
                throw new \RuntimeException('Unable to decode the video information for :' . $url);
            }

            
            $arreglo['thumbnail']=$metadata['thumbnail_url'];
            $arreglo['title']=$metadata['title'];
            $arreglo['videoId']=$videoId;
            $arreglo['description']="";
            $arreglo['urlVideo']="http://www.youtube.com/watch?v=".$videoId;

            return $arreglo;
            
        }else{
            
            return self::getInfoFindVideoInWeb($link);
        }

    }*/
    
    static public function getTitleAndImageVideoYoutube($link){

        $link=self::getLinkLargeYoutube($link);
        $videoId=self::getVideoIdYoutube($link);


        if(!$videoId==null){
            
            $video = new Youtube($videoId);
            
            if (!$video) {
                throw new \RuntimeException('Unable to decode the video information for :' . $url);
            }

            
            $arreglo['thumbnail']=$video->getUrlImage('default');
            $arreglo['title']=$video->getTitle();
            $arreglo['videoId']=$videoId;
            $arreglo['description']=$video->getDescription();
            $arreglo['urlVideo']=$video->getUrl();

            return $arreglo;
            
        }else{
            
            return self::getInfoFindVideoInWeb($link);
        }

    }
    
    static public function getLinkLargeYoutube($link){
        $arreglo=explode("/", $link);
        if($arreglo['2']=="youtu.be"){
           $arreglo2=explode('&',$arreglo[3]); 
           $linkArreglado="http://www.youtube.com/watch?v=".$arreglo2[0];
        }elseif($arreglo['2']=='vimeo.com' || $arreglo['2']=='www.vimeo.com'){
            $linkArreglado="http://vimeo.com/".$arreglo[3];
        }else{
            $linkArreglado=$link;
        }
        return $linkArreglado;
    }
    static public function getVideoIdYoutube($link){
        $arreglo=explode("/", $link);
        if($arreglo['2']=="www.youtube.com"){
            preg_match('/youtube\.com\/watch\?v=([A-Za-z0-9._%-]*)[&\w;=\+_\-]*/',$link,$match);
            return $match[1];
        }else{
            return null;
        }
    }
    static public function getInfoFindVideoInWeb($link){
        $arreglo=explode("/", $link);
        if($arreglo['2']=="vimeo.com" || $arreglo['2']=="www.vimeo.com"){
            $url = sprintf('http://vimeo.com/api/oembed.json?url=http://vimeo.com/%s', $arreglo[3]);
            $browser = new Browser();
            try {
                $response = $browser->get($url);
            } catch (\RuntimeException $e) {
                throw new \RuntimeException('Unable to retrieve the video information for :' . $url, null, $e);
            }

                $metadata = json_decode($response->getContent(), true);

            if (!$metadata) {
                throw new \RuntimeException('Unable to decode the video information for :' . $url);
            }
            
            //$videoInfo=$html->getResponseText();
            
            //self::error('getInfoFindVideoInWeb.- videoInfo: ', var_dump($videoInfo));
            
            $arregloInfo['thumbnail']=$metadata['thumbnail_url'];
            $arregloInfo['title']=$metadata['title'];
            $arregloInfo['videoId']=$metadata['id'];
            $arregloInfo['description']=$metadata['description'];
            $arregloInfo['urlVideo']=$metadata['url'];
            
            //self::error('getInfoFindVideoInWeb.- arregloInfo: ', var_dump($arregloInfo));
            
            return $arregloInfo;
        }else{
            return null;
        }
    }
    
    static public function cut_string($string, $charlimit)
    {
        if(substr($string,$charlimit-1,1) != ' ')
        {
            $largo=strlen($string);
            if($charlimit>$largo)
                $charlimit=$largo;
            $string = substr($string,0,$charlimit);
            $array = explode(' ',$string);
            array_pop($array);
            $new_string = implode(' ',$array);

            return $new_string.' ...';
        }else{ 
            return substr($string,0,$charlimit-1).' ...';
        }
    }


    
    static public function cut_string2($str, $n, $delim='...')
    {
       $len = strlen($str);
       if ($len > $n) {
            preg_match('/(.{' . $n . '}.*?)\b/', $str, $matches);
            return rtrim($matches[1]) . $delim;
        }
        else {
            return $str;
        }
    }
    
    static public function strip_html_tags( $text )
    {
            $text = preg_replace(
                array(
                // Remove invisible content
                    '@<head[^>]*?>.*?</head>@siu',
                    '@<style[^>]*?>.*?</style>@siu',
                    '@<script[^>]*?.*?</script>@siu',
                    '@<object[^>]*?.*?</object>@siu',
                    '@<embed[^>]*?.*?</embed>@siu',
                    '@<applet[^>]*?.*?</applet>@siu',
                    '@<noframes[^>]*?.*?</noframes>@siu',
                    '@<noscript[^>]*?.*?</noscript>@siu',
                    '@<noembed[^>]*?.*?</noembed>@siu',
                // Add line breaks before and after blocks
                    '@</?((address)|(blockquote)|(center)|(del))@iu',
                    '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
                    '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
                    '@</?((table)|(th)|(td)|(caption))@iu',
                    '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
                    '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
                    '@</?((frameset)|(frame)|(iframe))@iu',
                ),
                array(
                    ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',"$0", "$0", "$0", "$0", "$0", "$0","$0", "$0",), $text );

            return $text;
     }
     
     static public function TextToUrls($text){
        $ret = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $text);
        $ret = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $ret);
        $ret = preg_replace("/@(\w+)/", "<a href=\"http://www.twitter.com/\\1\" target=\"_blank\">@\\1</a>", $ret);
        $ret = preg_replace("/#(\w+)/", "<a href=\"http://search.twitter.com/search?q=\\1\" target=\"_blank\">#\\1</a>", $ret);
        return $ret;
    }
    static public function getUrlsTwitters($twitters=null){
        if($twitters==null)
            return null;

        foreach($twitters as $key=>$value){
            $arregloTwitters[$key]=RichSys::TextToUrls($value->text);
        }
        
        return $arregloTwitters;
    }
    static public function twitter_time($a) {
            //get current timestampt
            $b = strtotime("now"); 
            //get timestamp when tweet created
            $c = strtotime($a);
            //get difference
            $d = $b - $c;
            //calculate different time values
            $minute = 60;
            $hour = $minute * 60;
            $day = $hour * 24;
            $week = $day * 7;

            if(is_numeric($d) && $d > 0) {
                //if less then 3 seconds
                if($d < 3) return " justo ahora";
                //if less then minute
                if($d < $minute) return "hace ". floor($d) . " segundos";
                //if less then 2 minutes
                if($d < $minute * 2) return "hace un minuto";
                //if less then hour
                if($d < $hour) return "hace ". floor($d / $minute) . " minutos";
                //if less then 2 hours
                if($d < $hour * 2) return "hace una hora";
                //if less then day
                if($d < $day) return "hace ". floor($d / $hour) . " horas";
                //if more then day, but less then 2 days
                if($d > $day && $d < $day * 2) return "ayer";
                //if less then year
                if($d < $day * 365) return "hace ". floor($d / $day) . " días";
                //else return more than a year
                return "hace mas de un año";
            }
    }
    
    static public function setDebugMensaje($mensaje){
        $fp = fopen(sfConfig::get('sf_upload_dir'). "/debug.txt","a");
        fwrite($fp, date('l jS \of F Y h:i:s A')." Mensaje: $mensaje" . PHP_EOL);
        fclose($fp);
    }

    static public function detectarNavegador(){
        $navegador = getenv("HTTP_USER_AGENT"); 
        if (preg_match("/MSIE/i", "$navegador")) 
        { 
            $resultado = "IE"; 
        } 
        else if (preg_match("/Mozilla/i", "$navegador")) 
        { 
            $resultado = "Mozilla"; 
        } 
        else 
        { 
            $resultado = "Estas usando $navegador"; 
        }
        return $resultado; 

    }
    
    static public function getInstagramMedia($usuario){
            $clienteId="4a712af2a5be4c8091138bd233057640";
            $url = sprintf('https://api.instagram.com/v1/users/3/media/recent/?client_id=%s', $clienteId);
            $browser = new Browser();
            try {
                $response = $browser->get($url);
            } catch (\RuntimeException $e) {
                throw new \RuntimeException('Unable to retrieve the video information for :' . $url, null, $e);
            }

                $metadata = json_decode($response->getContent(), true);

            if (!$metadata) {
                throw new \RuntimeException('Unable to decode the video information for :' . $url);
            }
            
            return $metadata;

    }
    
    static public function getErrorMessages(\Symfony\Component\Form\Form $form) {      
        $errors = array();
        foreach ($form->getErrors() as $key => $error) {
                $errors[] = $error->getMessage();
        }

        return $errors;
    }
    
    
}
<?php 
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Get an array that represents directory tree
 * @param string $directory     Directory path
 * @param bool $recursive         Include sub directories
 * @param bool $listDirs         Include directories on listing
 * @param bool $listFiles         Include files on listing
 * @param regex $exclude         Exclude paths that matches this regex
 */

add_filter('widget_text', 'do_shortcode');

function scmDirectoryToArray($directory, $recursive = true, $listDirs = false, $listFiles = true, $exclude = '') {
    $arrayItems = array();
    $skipByExclude = false;
    $handle = opendir($directory);
    if ($handle) {
        while (false !== ($file = readdir($handle))) {
        preg_match("/(^(([\.]){1,2})$|(\.(svn|git|md))|(Thumbs\.db|\.DS_STORE))$/iu", $file, $skip);
        if($exclude){
            preg_match($exclude, $file, $skipByExclude);
        }
        if (!$skip && !$skipByExclude) {
            if (is_dir($directory. DIRECTORY_SEPARATOR . $file)) {
                if($recursive) {
                    $arrayItems = array_merge($arrayItems, scmDirectoryToArray($directory. DIRECTORY_SEPARATOR . $file, $recursive, $listDirs, $listFiles, $exclude));
                }
                if($listDirs){
                    $file = $directory . DIRECTORY_SEPARATOR . $file;
                    $arrayItems[] = $file;
                }
            } else {
                if($listFiles){
                    $file = $directory . DIRECTORY_SEPARATOR . $file;
                    $arrayItems[] = $file;
                }
            }
        }
    }
    closedir($handle);
    }
    return $arrayItems;
}

function smc_cab($title=""){
  ?>
    <div style="height: 10px;"></div>
    <div class="container">
    <div class="row"  style="border:1px solid gray;padding: 10px; border-radius:10px; -moz-border-radius: 10px; -webkit-border-radius: 10px;">
    <div class="col-md-3">
        MSC WEB<br>
    </div>
    <div class="col-md-6">
        <div style="text-align: center;">
            <h1 >GERENCIAL</h1>
        </div>
    </div>
    <div class="col-md-3" style="text-align: right;">
        <a href="<?php echo WPSCM_PATH ?>/login/">login</a> 
    </div>

    </div>
    </div>

    <div style="height: 10px;"></div>


    <div class="container">
      <div class="row">
        <div style="min-height: 300px; border:1px solid gray;padding: 10px; border-radius:10px; -moz-border-radius: 10px; -webkit-border-radius: 10px;">


<?php 
}


function smc_bot(){
  ?> 
        </div>
        </div>
    </div>

    <div style="height: 10px;"></div>

    <div class="container">
      <div class="row">
        <div style="min-height: 100px; border:1px solid gray;padding: 10px; border-radius:10px; -moz-border-radius: 10px; -webkit-border-radius: 10px;">
          <div class="col-md-8"></div>
          <div class="col-md-4" style="text-align: right;">
            <div><a href="<?php echo bloginfo('url') ?>/msc/grupos/">GRUPOS</a></div>
            <div><a href="<?php echo bloginfo('url') ?>/msc/pessoas/">PESSOAS</a></div>
            <div><a href="<?php echo bloginfo('url') ?>/msc/eventos/">EVENTOS</a></div>
          </div>
        </div>
        </div>
    </div>
<?php  
}

function scmGeraSenha($tamanho = 8, $maiusculas = true, $numeros = true, $simbolos = false){
  $lmin = 'abcdefghijklmnopqrstuvwxyz';
  $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $num = '1234567890';
  $simb = '!@#$%*-';
  $retorno = '';
  $caracteres = '';
  $caracteres .= $lmin;
  if ($maiusculas) $caracteres .= $lmai;
  if ($numeros) $caracteres .= $num;
  if ($simbolos) $caracteres .= $simb;
  $len = strlen($caracteres);
  for ($n = 1; $n <= $tamanho; $n++) {
    $rand = mt_rand(1, $len);
    $retorno .= $caracteres[$rand-1];
  }
  return $retorno;
}

function scmArrayToObject($d) {
  if (is_object($d)) {
    $d = get_object_vars($d);
  }
  if (is_array($d)) {
    return array_map(__FUNCTION__, $d);
  }
  else {
    return $d;
  }
}

function scmGetFilterFixo($md){
  return '';
  $erro='';
  $ret = "";
  $sql = "
  select 
    i0003_comparison,
    i0003_type,
    i0003_value,
    i0003_field
  from ".scmPrefix(true)."i0003
  where 
  (
    (i0003_modulo = ".$md.")
    and
    (i0003_ativo = 's')
  )
  ";
  $f=0;
  $filter = array();
  $tb = scmDbExe($sql,'rows');
  $rows = $tb['rows'];
  foreach ($rows as $row){
    $filter[$f]['data']['type']       = $row['i0003_type'];
    $filter[$f]['data']['value']      = $row['i0003_value'];
    $filter[$f]['field']          = $row['i0003_field'];
    $filter[$f]['data']['comparison']   = $row['i0003_comparison'];
    $f++;
  }
  for ($i=0;$i<$f;$i++){
    if($filter[$i]['data']['value']=='_0_')     $filter[$i]['data']['value'] = 0;
    if($filter[$i]['data']['value']=='__md__')    $filter[$i]['data']['value'] = $md;
    if($filter[$i]['data']['value']=='__usr__')     $filter[$i]['data']['value'] = get_membro_codigo();
    
    if($filter[$i]['data']['value']=='__cx__')    $filter[$i]['data']['value'] = $_SESSION["cx"];//$cx;
    if($filter[$i]['data']['value']=='_dia_')     $filter[$i]['data']['value'] = date("j");
    if($filter[$i]['data']['value']=='_sem_')     $filter[$i]['data']['value'] = date("W");
    if($filter[$i]['data']['value']=='_sem_add1_')  $filter[$i]['data']['value'] = (date("W")+1);
    if($filter[$i]['data']['value']=='_mes_')     $filter[$i]['data']['value'] = date("n");
    if($filter[$i]['data']['value']=='_hoje_')    $filter[$i]['data']['value'] = date("Y-m-d");
    if($filter[$i]['data']['value']=='_uddmp_'){
      $filter[$i]['data']['value'] = date("Y-m-d",mktime (0, 0, 0, (date("m")) , 0, date("Y")));
    }
    if($filter[$i]['data']['value']=='_udm_'){
      $filter[$i]['data']['value'] = date("Y-m-d",mktime (0, 0, 0, (date("m")+1) , 0, date("Y")));
    }

    if($filter[$i]['data']['value']=='_pdm_'){
      $filter[$i]['data']['value'] = date("Y-m-d",mktime (0, 0, 0, (date("m")) , 0, date("Y")));
    }
    if($filter[$i]['data']['value']=='_mes_add1_') {
      $calcula = date("n");
      $calcula++;
      if($calcula==13){
        $calcula = '1';
      }
      $filter[$i]['data']['value'] = $calcula;
    }
    if($filter[$i]['data']['value']=='_ano_')     $filter[$i]['data']['value'] = date("Y");
  }
  
  $qs = '';
  $where = "";
  if (is_array($filter)) {
    for ($i=0;$i<$f;$i++){
      switch($filter[$i]['data']['type']){
        case 'string' : 
          switch ($filter[$i]['data']['comparison']) {
            case 'ig' : $qs .= " AND ".$filter[$i]['field']." = '".$filter[$i]['data']['value']."'"; Break; //igual
            case 'eq' : $qs .= " AND ".$filter[$i]['field']." LIKE '%".$filter[$i]['data']['value']."%'"; Break;//contem
          }
          Break;
        case 'list' :
          if (strstr($filter[$i]['data']['value'],',')){
            $fi = explode(',',$filter[$i]['data']['value']);
            for ($q=0;$q<count($fi);$q++){
              $fi[$q] = "'".$fi[$q]."'";
            }
            $filter[$i]['data']['value'] = implode(',',$fi);
            $qs .= " AND ".$filter[$i]['field']." IN (".$filter[$i]['data']['value'].")";
          }else{
            $qs .= " AND ".$filter[$i]['field']." = '".$filter[$i]['data']['value']."'";
          }
        Break;
        case 'boolean' : 
          if($filter[$i]['data']['value']=='true'){
            $qs .= " AND ".$filter[$i]['field']." = 1"; 
          }
          if($filter[$i]['data']['value']=='false'){
            $qs .= " AND ".$filter[$i]['field']." = 0"; 
          }
        Break;
        case 'numeric' :
          switch ($filter[$i]['data']['comparison']) {
            case 'ne' : $qs .= " AND ".$filter[$i]['field']." != ".$filter[$i]['data']['value']; Break;
            case 'eq' : $qs .= " AND ".$filter[$i]['field']." = ".$filter[$i]['data']['value']; Break;
            case 'lt' : $qs .= " AND ".$filter[$i]['field']." < ".$filter[$i]['data']['value']; Break;
            case 'gt' : $qs .= " AND ".$filter[$i]['field']." > ".$filter[$i]['data']['value']; Break;
            case 'mi' : $qs .= " AND ".$filter[$i]['field']." >= ".$filter[$i]['data']['value']; Break;
          }
        Break;
        case 'date' :
          switch ($filter[$i]['data']['comparison']) {
            case 'ne' : $qs .= " AND ".$filter[$i]['field']." != '".date('Y-m-d',strtotime($filter[$i]['data']['value']))."'"; Break;
            case 'eq' : $qs .= " AND ".$filter[$i]['field']." = '".date('Y-m-d',strtotime($filter[$i]['data']['value']))."'"; Break;
            case 'lt' : $qs .= " AND ".$filter[$i]['field']." < '".date('Y-m-d',strtotime($filter[$i]['data']['value']))."'"; Break;
            case 'gt' : $qs .= " AND ".$filter[$i]['field']." > '".date('Y-m-d',strtotime($filter[$i]['data']['value']))."'"; Break;
            case 'ii' : $qs .= " AND ".$filter[$i]['field']." >= '".date('Y-m-d',strtotime($filter[$i]['data']['value']))."'"; Break; //maior ou igual
            case 'mi' : $qs .= " AND ".$filter[$i]['field']." <= '".date('Y-m-d',strtotime($filter[$i]['data']['value']))."'"; Break; //menor ou igual
          }
        Break;
      }
    }
    $where .= $qs;
  } 
  return $where;    
}
function scmGetStart($mds){
  $start = 0;
  return $start;
}
function scmRetiraAcentoUtf($str, $enc = "UTF-8"){
  return $str;
  $acentos = array(
    'A' => '/&Agrave;|&Aacute;|&Acirc;|&Atilde;|&Auml;|&Aring;/',
    'a' => '/&agrave;|&aacute;|&acirc;|&atilde;|&auml;|&aring;/',
    'C' => '/&Ccedil;/',
    'c' => '/&ccedil;/',
    'E' => '/&Egrave;|&Eacute;|&Ecirc;|&Euml;/',
    'e' => '/&egrave;|&eacute;|&ecirc;|&euml;/',
    'I' => '/&Igrave;|&Iacute;|&Icirc;|&Iuml;/',
    'i' => '/&igrave;|&iacute;|&icirc;|&iuml;/',
    'N' => '/&Ntilde;/',
    'n' => '/&ntilde;/',
    'O' => '/&Ograve;|&Oacute;|&Ocirc;|&Otilde;|&Ouml;/',
    'o' => '/&ograve;|&oacute;|&ocirc;|&otilde;|&ouml;/',
    'U' => '/&Ugrave;|&Uacute;|&Ucirc;|&Uuml;/',
    'u' => '/&ugrave;|&uacute;|&ucirc;|&uuml;/',
    'Y' => '/&Yacute;/',
    'y' => '/&yacute;|&yuml;/',
    'a.' => '/&ordf;/',
    'o.' => '/&ordm;/'
  );
  return preg_replace($acentos,array_keys($acentos),htmlentities($str,ENT_NOQUOTES, $enc));
}

function scmRetiraAcento($string){
  return $string;
  $a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr';
  $b = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
  $string = utf8_decode($string);
  $string = strtr($string, ($a), $b); //substitui letras acentuadas por "normais"
  return ($string); //finaliza, gerando uma saída para a funcao
}
function mscRemoverCaracter($string) {
    return $string;
    $string = preg_replace("/[áàâãä]/", "A", $string);
    $string = preg_replace("/[ÁÀÂÃÄ]/", "A", $string);
    $string = preg_replace("/[éèê]/", "E", $string);
    $string = preg_replace("/[ÉÈÊ]/", "E", $string);
    $string = preg_replace("/[íì]/", "I", $string);
    $string = preg_replace("/[ÍÌ]/", "I", $string);
    $string = preg_replace("/[óòôõö]/", "O", $string);
    $string = preg_replace("/[ÓÒÔÕÖ]/", "O", $string);
    $string = preg_replace("/[úùü]/", "U", $string);
    $string = preg_replace("/[ÚÙÜ]/", "U", $string);
    $string = preg_replace("/ç/", "C", $string);
    $string = preg_replace("/Ç/", "C", $string);
    // $string = preg_replace("/[][><}{)(:;,!?*%~^`&#@]/", "", $string);
    // $string = preg_replace("/ /", "_", $string);
    return $string;
}

function scmDateBrFb($data_br){
  if($data_br){
    if(is_array($data_br)){
      $ano = $data_br[3];
      $mes = $data_br[2];
      $dia = $data_br[1];
    }else{
      @list($dia,$mes,$ano) = explode("/", $data_br); 
    }
    $vai = true;
    if(!is_numeric($ano)) $vai = false;
    if(!is_numeric($mes)) $vai = false;
    if(!is_numeric($dia)) $vai = false;
    if($ano=='00') $vai = false;
    if($mes=='00') $vai = false;
    if($dia=='00') $vai = false;
    if($vai){
      if(!checkdate($mes, $dia, $ano)) $vai = false;
    }
    if(strlen($ano)<>4) $vai = false;
    
    if($vai){
      return "'".$ano."-".$mes."-".$dia."'";
    }else{
      return 'null';
    }
  }else{
    return 'null';
  }
}

function scmGetCliterio2($df){
  $criterio2 = isset($df['criterio2']) ? $df['criterio2'] : '';
  return '';
}

function mscDateBrPhp($data_br){
  if($data_br){
    if(is_array($data_br)){
      $ano = $data_br[3];
      $mes = $data_br[2];
      $dia = $data_br[1];
    }else{
      @list($dia,$mes,$ano) = explode("/", $data_br); 
    }
    $vai = true;
    if(!is_numeric($ano)) $vai = false;
    if(!is_numeric($mes)) $vai = false;
    if(!is_numeric($dia)) $vai = false;
    if($ano=='00') $vai = false;
    if($mes=='00') $vai = false;
    if($dia=='00') $vai = false;
    if($vai){
      if(!checkdate($mes, $dia, $ano)) $vai = false;
    }
    if(strlen($ano)<>4) $vai = false;
    
    if($vai){
      return $ano."-".$mes."-".$dia;
    }else{
      return 'null';
    }
  }else{
    return 'null';
  }
}
function scmDateMysqlBr($data){
  $ex = explode("-", $data);
  if(count($ex)==3){
    list($dia,$mes,$ano) = explode("-", $data);
    return $ano."/".$mes."/".$dia;
  }else{
    return $data;
  }
}
function scmDateMysqlPhp($data){
  $ex = explode("-", $data);
  if(count($ex)==3){
    list($ano,$mes,$dia) = explode("-", $data);
    return $mes."/".$dia."/".$ano;
  }else{
    return $data;
  }
}

function mscDateBrMysql($data){
  return substr($data,6,4) ."-" .substr($data,3,2) . '-' .substr($data,0,2);
}
function mscGetSubmod($md){
  $sql = "
  select 
      md000004_codigo, 
      md000004_nome, 
      md000004_submodulo,
      md000004_op,
      md000004_filtro
  from ".scmPrefix(true)."i0004
  where
      md000004_modulo = ".$md."
      and
      md000004_ativo = 's'
  ";
  $tb = scmDbExe($sql,'rows',1);
  $rows = $tb['rows'];
  $submod = array();
  for ($i=0;$i<$tb['r'];$i++){
    $submod[$i]['codigo'] = $tb['rows'][$i]['md000004_codigo'];
    $submod[$i]['label'] = $tb['rows'][$i]['md000004_nome'];
    $submod[$i]['md'] = $tb['rows'][$i]['md000004_submodulo'];
    $submod[$i]['op'] = $tb['rows'][$i]['md000004_op'];
    $submod[$i]['ft'] = $tb['rows'][$i]['md000004_filtro'];
  }
  return $submod;
}


function scmMoedaBrUs($valor){
  $valor = preg_replace('/,/', '.',$valor);
  return $valor;
}

function scmGetModuloConf($md){
  $sql = "
  select
    md000002_md000002,
    md000002_titulo,
    md000002_url,
    md000002_renderto,
    md000002_tabela,
    md000002_sql_limit,
    md000002_show_tbar,
    md000002_show_context,
    md000002_show_pagin,
    md000002_show_sum,
    md000002_width,
    md000002_height,
    md000002_sql_sort,
    md000002_sql_dir,
    md000002_open_js,
    md000002_descricao,
    md000002_introd,
    md000002_show_pagin,
    md000002_show_col_title,
    md000002_conexao,
    md000002_de_sistema,
    md000002_access_root,
    md000002_grupo,
    md000002_user,

    md000002_retirar_acentos,
    md000002_caixa_alta,

    md000002_grupalizar,
    md000002_show_cp_option

  from ".scmPrefix(true)."md000002
    where md000002_codigo = ".$md."
    ;
  ";
  $ret = array();
  // $ret['sql']         = $sql;
  // print('<pre>');
  // print($sql);
  // die();
  $tb = scmDbExe($sql,'rows');
  // $ret['tb'] = $tb;
  if($tb['r']){
    $ret['retirar_acentos']   = scmConverteIsoToUtf8($tb['rows'][0]['md000002_retirar_acentos']);
    $ret['caixa_alta']      = scmConverteIsoToUtf8($tb['rows'][0]['md000002_caixa_alta']);
    $ret['modulo']        = scmConverteIsoToUtf8($tb['rows'][0]['md000002_md000002']);
    $ret['title']         = scmConverteIsoToUtf8($tb['rows'][0]['md000002_titulo']);
    $ret['url']         = scmConverteIsoToUtf8($tb['rows'][0]['md000002_url']);
    $ret['renderto']      = scmConverteIsoToUtf8($tb['rows'][0]['md000002_renderto']);
    $ret['sql_ordem']       = $tb['rows'][0]['md000002_sql_sort'];
    $ret['sql_dir']       = $tb['rows'][0]['md000002_sql_dir'];
    $ret['tabela']        = $tb['rows'][0]['md000002_tabela'];
    $ret['width']         = (int) $tb['rows'][0]['md000002_width']; if(!$ret['width']) $ret['width'] = 800;
    $ret['height']        = (int) $tb['rows'][0]['md000002_height']; if(!$ret['height']) $ret['height'] = 600;
    $ret['limit']         = ($tb['rows'][0]['md000002_sql_limit']) ? $tb['rows'][0]['md000002_sql_limit'] : 20;
    $ret['show_tbar']       = ($tb['rows'][0]['md000002_show_tbar']=='S') ? true : false;
    $ret['show_context']    = ($tb['rows'][0]['md000002_show_context']=='S') ? true : false;
    $ret['show_pagin']      = ($tb['rows'][0]['md000002_show_pagin']=='S') ? true : false;
    $ret['show_sum']      = ($tb['rows'][0]['md000002_show_sum']=='S') ? true : false;
    $ret['open_js']       = $tb['rows'][0]['md000002_open_js'];
    $ret['descricao']       = scmConverteIsoToUtf8($tb['rows'][0]['md000002_descricao']);
    $ret['introd']        = scmConverteIsoToUtf8($tb['rows'][0]['md000002_introd']);
    $ret['show_pagin']      = $tb['rows'][0]['md000002_show_pagin'];
    $ret['show_col_title']    = $tb['rows'][0]['md000002_show_col_title'];
    $ret['de_sistema']      = $tb['rows'][0]['md000002_de_sistema'];
    $ret['access_root']     = $tb['rows'][0]['md000002_access_root'];
    $ret['grupo']       = (int) $tb['rows'][0]['md000002_grupo'];
    $ret['user']        = (int) $tb['rows'][0]['md000002_user'];
    $ret['conexao']       = (int) $tb['rows'][0]['md000002_conexao'];
    $ret['grupalizar']      = (int) $tb['rows'][0]['md000002_grupalizar'];
    if($ret['de_sistema']=='s'){
      $ret['conexao'] = 1;
    }else{
      $ret['conexao'] = 2;
    }
    $ret['show_cp_option']      = (int) $tb['rows'][0]['md000002_show_cp_option'];
  }
  return $ret;
}

function scmDbExe($sql,$op='rows',$conn=1){
  $ret = array();
  // if ($conn > 1) {
  //   return $ret;
  // }
  // return $ret;

  if($op=='rows'){
    //$row = $wpdb->get_results($sql, 'ARRAY_A');
    $rr = $GLOBALS['wpdb']->get_results($sql, 'ARRAY_A');
    $ret['rows'] = $GLOBALS['wpdb']->get_results($sql, 'ARRAY_A');
    $rows['total'] = count($rr);
    $ret['r'] = $rows['total'];
    $ret['sql'] = $sql;
    return $ret;
  }
  return $GLOBALS['wpdb']->query($sql);
}

function scmMysqliNoGrupo($cod){
  global $wpdb;
  $sql = "select md000007_db_host, md000007_db_name, md000007_db_user, md000007_db_pass from ".SCM_WPDB_PREFIX."i0007 where md000007_codigo = ".$cod;
  $tb = scmDbExe($sql);
  if($tb['r']){
    $mysqli = new mysqli($tb['rows'][0]['md000007_db_host'], $tb['rows'][0]['md000007_db_user'], $tb['rows'][0]['md000007_db_pass'], $tb['rows'][0]['md000007_db_name']);
    // if (mysqli_connect_errno()) {
    //   return false;
    //   //trigger_error(mysqli_connect_error());
    // }
  }
  // print("<pre>");
  // print_r($tb);
  // print("</pre>");

  
  return $mysqli;
}

function scmDbDatax($sql,$op='rows',$cnn=''){
  return scmDbExe($sql,$op,$cnn);
}

function scmDbData($sql,$op='rows',$cnn=''){
  $ret = array();
  if ( is_user_logged_in() ) {
    $user_id = get_current_user_id();
    $wpmsc_grupo = get_user_meta($user_id,  'wpmsc_grupo', true );
    if($wpmsc_grupo){
      $grupo_db_host = get_post_meta( $wpmsc_grupo, 'grupo_db_host', true );
      $grupo_db_name = get_post_meta( $wpmsc_grupo, 'grupo_db_name', true );
      $grupo_db_user = get_post_meta( $wpmsc_grupo, 'grupo_db_user', true );
      $grupo_db_pass = get_post_meta( $wpmsc_grupo, 'grupo_db_pass', true );
      $mysqli = new mysqli($grupo_db_host, $grupo_db_user, $grupo_db_pass, $grupo_db_name);
      //echo '<!--'.$sql.' '.$grupo_db_name.'-->';
      $result = mysqli_query($mysqli, $sql);
      if($op=='rows'){
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
          $rows[] = $row;
        }
        $ret['rows']  = $rows;
        $ret['total'] = count($ret['rows']);
        $ret['r']     = $ret['total'];
        $ret['sql']   = $sql;
        return $ret;
      }
    }
  }
  
  // if ($cnn) {
  //   $mysqli = new mysqli("localhost", "ideiafixa", "Q4SO6FNEPWIU0RgS", "ideiafixa");      
  //   $result = mysqli_query($mysqli, $sql);
  //   if($op=='rows'){
  //     while($row = $result->fetch_array(MYSQLI_ASSOC)){
  //       $rows[] = $row;
  //     }
  //     $ret['rows']  = $rows;
  //     $ret['total'] = count($ret['rows']);
  //     $ret['r']     = $ret['total'];
  //     $ret['sql']   = $sql;
  //     return $ret;
  //   }
  // }

  if ( is_user_logged_in() ) {
    $user_id = get_current_user_id();
    $wpmsc_user_grupo = get_user_meta($user_id,  'wpmsc_user_grupo', true );
    if ($wpmsc_user_grupo) {

      $mysqli = scmMysqliNoGrupo($wpmsc_user_grupo);
      $result = mysqli_query($mysqli, $sql);
      if($op=='rows'){
        $rows = array();
        if($result)
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
          $rows[] = $row;
        }

        $ret['rows']  = $rows;
        $ret['total'] = count($ret['rows']);
        $ret['r']     = $ret['total'];
        $ret['sql']   = $sql;
        return $ret;
      }

    }

  }
  
  if($op=='rows'){
    //$row = $wpdb->get_results($sql, 'ARRAY_A');
    $rr = $GLOBALS['wpdb']->get_results($sql, 'ARRAY_A');
    $ret['rows'] = $GLOBALS['wpdb']->get_results($sql, 'ARRAY_A');
    $rows['total'] = count($rr);
    $ret['r'] = $rows['total'];
    $ret['sql'] = $sql;
    return $ret;
  }
  return $GLOBALS['wpdb']->query($sql);

}

function scmObjectToArray($data){
  if ((! is_array($data)) and (! is_object($data))) return 'xxx'; //$data;
  $result = array();
  $data = (array) $data;
  foreach ($data as $key => $value) {
      if (is_object($value)) $value = (array) $value;
      if (is_array($value)) 
      $result[$key] = scmObjectToArray($value);
      else
          $result[$key] = $value;
  }
  return $result;
}


function scmAddParam($querystring, $ParameterName, $ParameterValue){
    $queryStr = null; 
    $paramStr = null;
    if (strpos($querystring, '?') !== false)
        list($queryStr, $paramStr) = explode('?', $querystring);
    else if (strpos($querystring, '=') !== false)
        $paramStr = $querystring;
    else
        $queryStr = $querystring;
    $paramStr = $paramStr ? '&' . $paramStr : '';
    $paramStr = preg_replace ('/&' . $ParameterName . '(\[\])?=[^&]*/', '', $paramStr);
    if(is_array($ParameterValue)) {
        foreach($ParameterValue as $key => $val) {
            $paramStr .= "&" . urlencode($ParameterName) . "[]=" . urlencode($val);
        }
    } else {
        $paramStr .= "&" . urlencode($ParameterName) . "=" . urlencode($ParameterValue);
    }
    $paramStr = ltrim($paramStr, '&');
    return $queryStr ? $queryStr . '?' . $paramStr : $paramStr;
}

function scmRemoveParam($querystring, $ParameterName){
  $paramStr = '';
  $queryStr = '';
    if (strpos($querystring, '?') !== false)
        list($queryStr, $paramStr) = explode('?', $querystring);
    else if (strpos($querystring, '=') !== false)
        $paramStr = $querystring;
    else
        $queryStr = $querystring;
    $paramStr = $paramStr ? '&' . $paramStr : '';
    $paramStr = preg_replace ('/&' . $ParameterName . '(\[\])?=[^&]*/', '', $paramStr);
    $paramStr = ltrim($paramStr, '&');
    return $queryStr ? $queryStr . '?' . $paramStr : $paramStr;
}


function scmHeader(){
?>  

<!DOCTYPE html>
<html lang="">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>

    <!-- Bootstrap CSS -->
    <link href="<?php echo plugins_url( 'assets/css/style.css', __FILE__ ) ?>" rel="stylesheet">
    <style type="text/css">
    body {overflow: no;}
    .hidden_s {display: none;}
    input {
      text-transform: uppercase;
    }
    </style>
    <!--script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script-->
    <!--script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script-->
    

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <?php 
    //wp_head() 
  if (!scmIsRole('caixa')) {
    echo '
    <style>  
    .hidden_n { display:none !important; } 
    .hidden_s { display:none !important; } 
    .hidden_ { display:none !important; } 
    </style> 
    ';
  // } else{
    // echo '<style>  .hidden_s { display:none !important; } </style> ';
  }

    ?>
  </head>
  <body>
  
<?php 
}

function scmFooter(){
?>  

<div style="height: 50px;"></div>
    <!-- jQuery -->
    <!--script src="//code.jquery.com/jquery.js"></script-->
    <script src="/var/www/html/pacientes/wp-content/plugins/wpmsc/util/assets/js/jquery-3.2.1.min.js"></script>
    

    <!-- Bootstrap JavaScript -->
    <!--script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script-->
    <script src="/var/www/html/pacientes/wp-content/plugins/wpmsc/util/assets/js/libs/bootstrap.min.js"></script>
  </body>
</html>
<?php 
}

///---crud



if (!function_exists('scmGetMdRows')) :
function scmGetMdRows($md, $fields, $col, $df=array(),$cnn=""){

  // echo '<pre>';
  // print_r($col);
  // echo '</pre>';

  global $wpdb;
  $udir = wp_upload_dir();
  $rows = array();
  $sql_ordem = '';
  $modulo_conf = scmGetModuloConf($md);
  $grupo = $modulo_conf['grupo'];
  $user = $modulo_conf['user'];
  // if(!$grupo) $grupo = get_grupo_id($md);
  // if(!$user) $user = get_membro_codigo($md);
  $tabela = $modulo_conf['tabela'];
  $tabela_name = scmPrefix(true).$tabela;
  $tabela_cliente = scmPrefix(false).$tabela;
  $tabela_campo = $tabela;

  $limit = $modulo_conf['limit'];
  $sort = array();
  $start = 0;
  $wh = '';
  for ($i=0; $i < count($col); $i++) {
    $campo = $col[$i]['dataIndex'];
    $value = isset($_REQUEST[$campo]) ? $_REQUEST[$campo] : '';
    if($value){
      if($col[$i]['filter_type'] == 'date'){
        $value = strip_tags($value);
        $value = "'".mscDateBrMysql($value)."'";//'---';//
      }
      if($col[$i]['filter_type'] == 'string'){
        $value = "'".($value)."'";
      }
      $wh .= ' and '.$campo." = ". $value;
    }
    $campo_ini = $col[$i]['dataIndex'].'_ini_' ;
    $value_ini = isset($_REQUEST[$campo_ini]) ? $_REQUEST[$campo_ini] : '';
    if($value_ini){
      if($col[$i]['filter_type'] == 'date'){
        $value_ini = strip_tags($value_ini);
        $value_ini = "'".mscDateBrMysql($value_ini)."'";//'---';//
      }
      $wh .= ' and '.$campo." >= ". $value_ini;
    }
    $campo_end = $col[$i]['dataIndex'].'_end_' ;
    $value_end = isset($_REQUEST[$campo_end]) ? $_REQUEST[$campo_end] : '';
    if($value_end){
      $value_end = strip_tags($value_end);
      $value_end = "'".mscDateBrMysql($value_end)."'";//'---';//
      $wh .= ' and '.$campo." <= ". $value_end;
    }
  }
  // $sorts = array();
  if(isset($_REQUEST['start']) ? $_REQUEST['start'] : 0) $start = $_REQUEST['start'];
  if(isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 0) $limit = $_REQUEST['limit'];
  //if(isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 0) $sorts = $_REQUEST['sort'];
  $sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
  if($sort){
    $sql_ordem = 'order by '.$sort;
  }
  $filters = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : null;
  if (is_array($filters)) {
      $encoded = false;
  } else {
      $encoded = true;
      $filters = json_decode($filters);
  }
  // criterio - ini
  $crit_e = array();
  $crit_cp = array();
  $crit_sql = '';
  $i=0;
  // $criterio = isset($_REQUEST['criterio']) ? $_REQUEST['criterio'] : '';
  $criterio = isset($df['criterio']) ? $df['criterio'] : '';
  if($criterio){
    $criterio = base64_decode($criterio);
    $crit_e = explode("&", $criterio);
    foreach($crit_e as $value){
      $values = explode("=", $value);
      $crit_cp[$i]['campo'] = $values[0];
      $crit_cp[$i]['value'] = '"'.$values[1].'"';
      // $crit_cp[$i]['value'] = $values[1];

      // if($i) $crit_sql .=", ";
      if($i) $crit_sql .=" and ";
      $operad = "=";
      // $operad = ">=";
      // $operad = "=";
      // $operad = preg_replace("/gt/i",  ">", $operad)
      $crit_sql .= $crit_cp[$i]['campo']." ".$operad." ".$crit_cp[$i]['value'];
      $i++;
    }
    $crit_sql = " AND (".$crit_sql.") ";
  }
  $rows['crit_sql'] = $crit_sql;
  // criterio - end
  $where = ' 0 = 0 ';
  $where .= $wh;
  // if(($modulo_conf['grupalizar']) && ($modulo_conf['conexao'] >=2)){
  //   $where .= ' and '.$modulo_conf['tabela']."_id_sysempresa = ".get_grupo_id($md);
  // }
  // print('<hr>');
  // die($crit_sql);
  $where .= $crit_sql;
  $where .= scmGetFilterFixo($md);
  $where .= scmGetCliterio2($df);
  $qs = '';
  // loop through filters sent by client
  if (is_array($filters)) {
      for ($i=0;$i<count($filters);$i++){
          $filter = $filters[$i];
          // assign filter data (location depends if encoded or not)
          if ($encoded) {
              $field = $filter->field;
              $value = $filter->value;
              $compare = isset($filter->comparison) ? $filter->comparison : null;
              $filterType = $filter->type;
          } else {
              $field = $filter['field'];
              $value = $filter['data']['value'];
              $compare = isset($filter['data']['comparison']) ? $filter['data']['comparison'] : null;
              $filterType = $filter['data']['type'];
          }
          switch($filterType){
              case 'string' : $qs .= " and ".$field." like '%".$value."%'"; Break;
              case 'list' :
                  if (strstr($value,',')){
                      $fi = explode(',',$value);
                      for ($q=0;$q<count($fi);$q++){
                          $fi[$q] = "'".$fi[$q]."'";
                      }
                      $value = implode(',',$fi);
                      $qs .= " and ".$field." in (".$value.")";
                  }else{
                      $qs .= " and ".$field." = '".$value."'";
                  }
              Break;
              case 'boolean' : $qs .= " and ".$field." = ".($value); Break;
              case 'numeric' :
                $value = preg_replace("/__user__/i",  get_membro_codigo($md), $value);
                  switch ($compare) {
                      case 'eq' : $qs .= " and ".$field." = ".$value; Break;
                      case 'lt' : $qs .= " and ".$field." <= ".$value; Break;
                      case 'gt' : $qs .= " and ".$field." >= ".$value; Break;
                  }
              Break;
              case 'date' :
                  switch ($compare) {
                      case 'eq' : $qs .= " and ".$field." = '".date('Y-m-d',strtotime($value))."'"; Break;
                      case 'lt' : $qs .= " and ".$field." <= '".date('Y-m-d',strtotime($value))."'"; Break;
                      case 'gt' : $qs .= " and ".$field." >= '".date('Y-m-d',strtotime($value))."'"; Break;
                  }
              Break;
          }
      }
      $where .= $qs;
  }
  // -- filters  -- end

  // tbarFilter -- ini
  $tbarFilter = isset($_REQUEST['tbarFilter']) ? $_REQUEST['tbarFilter'] : '';
  //die($tbarFilter);
  if($tbarFilter){
    $filtro = '';
    for ($i=0;$i<count($fields);$i++){
      if($fields[$i]['type']=='string'){
        if($filtro) $filtro .= ' OR ';
        $filtro .= " ".$fields[$i]['name']." LIKE '%".$tbarFilter."%' ";
      }
    }
    $where .= ' and ('.$filtro.') ';
  }
  $busca = isset($_REQUEST['busca']) ? $_REQUEST['busca'] : '';
  if($busca){
    //se ta buscando em determinada coluna indicado pelo "coluna:texto" - ini
    $if_busca_col = preg_match("/\:/", $busca);
    // echo '===if_busca_col:'.$if_busca_col.'====';
    // echo '<br>';
    if($if_busca_col){
      $tmp0 = explode(":", $busca);
      $tmp_coluna = $tmp0[0];
      $tmp_value = $tmp0[1];
      $tmp_table_prefix = scmPrefix(0);
      $tmp_table = $modulo_conf['tabela'];

      // echo '=== $tmp_coluna:'.$tmp_coluna.' ====';
      // echo '<br>';
      // echo '=== $tmp_value:'.$tmp_value.' ====';
      // echo '<br>';
      // echo '=== $tmp_table_prefix:'.$tmp_table_prefix.' ====';
      // echo '<br>';
      // echo '===modulo_conf[\'tabela\']:'.$modulo_conf['tabela'].'====';
      $where .= ' and ('.$tmp_table.'_'.$tmp_coluna.' = "'.$tmp_value.'") ';
      //se ta buscando em determinada coluna indicado pelo "coluna:texto" - end
    }else{


      
      $filtro = '';
      for ($i=0;$i<count($fields);$i++){
        // if($fields[$i]['type']=='string'){
        if(($fields[$i]['type']=='string') || ($fields[$i]['type']=='blob') || ($fields[$i]['type']=='varchar')){
          if($filtro) $filtro .= ' OR ';
          $filtro .= " ".$fields[$i]['name']." LIKE '%".$busca."%' ";
        }
      }
      $where .= ' and ('.$filtro.') ';
    }
  }
  // tbarFilter -- end
  // ref_loc -- ini
  // referente a pesquisa de localizar clientes
  //select pessoa_codigo,pessoa_pessoa,pessoa_nome,pessoa_nascimento,pessoa_fones,pessoa_vigencia from pessoa   where   0 = 0  AND ( = )  limit 0, 20
  $ref_loc = isset($_REQUEST['ref_loc']) ? $_REQUEST['ref_loc'] : '';
  if($ref_loc=='undefined') $ref_loc = '';
  if($ref_loc){
    $filtro = '';
    $ff=0;
    for ($i=0;$i<count($fields);$i++){
      if($fields[$i]['type']=='string'){
        if($filtro) $filtro .= ' OR ';
        $filtro .= " ".$fields[$i]['name']." LIKE '%".$ref_loc."%' ";
        $ff++;
      }
    }
    if($ff){
      $where .= ' and ('.$filtro.') ';
    }
  }
  $i = 0;
  if($sql_ordem){ //se vem da url
  } else{
    if($modulo_conf['sql_ordem']){
      $sql_ordem = ' order by '.$modulo_conf['sql_ordem'];
      if($modulo_conf['sql_dir']){
        $sql_ordem .= ' '.$modulo_conf['sql_dir'];
      }
    }
  }
  $field = '';
  for ($i=0;$i<count($fields);$i++){
    if($i>0) $field .= ',';
    $field .= $fields[$i]["name"];
  }
  $coluna = '';
  // $inner = '';
  // $inner = $df['join'];
  $inner = $df['inner'];
  
  for ($i=0;$i<count($col);$i++){
    if($i>0) $coluna .= ',';
    $coluna .= $col[$i]["dataIndex"];
    // $inner .= $col[$i]["inner"];
  }
  // if($md==682){
  //   $limit = 100;
  // }


  $de_sistema = ($modulo_conf['de_sistema']=='s') ? true : false;
  $tabela_cliente = scmPrefix($de_sistema).$modulo_conf['tabela'];

  $sql  = "";
  $sql .= "select ";

  $sql .= $coluna." ";

// echo '<pre>';
// echo '---'.$df['msc_col_add'].'---';
// echo $coluna;
// echo '</pre>';
// die();

  if($df['msc_col_add']){
    // $sql .= ', '.$df['msc_col_add']." ";

  }
  $sql .= "from ".$tabela_cliente." ";
  
  // if ($md==8208) {
  //   $sql .= " INNER JOIN wp_i8200 ON wp_i8208.i8208_pessoa = wp_i8200.i8200_codigo ";
  // } else {
  //   $sql .= $inner." ";
  // }
  $sql .= $inner." ";
  //INNER JOIN wp_i8207 ON wp_i8208.i8208_servico = wp_i8207.i8207_codigo
  $sql .= " where ";
  $sql .= " ".$where;
  $sql .= $sql_ordem." ";
  $sql .= "limit ".$start.", ".$limit;
  // $tb = scmDbExe($sql,'rows',$modulo_conf['conexao']);
  // echo "--n".$cnn."n--";
  // die();
$sql = preg_replace("/__user__/i",  get_current_user_id(), $sql);
  if($df['die_sql']){
    //echo "<pre>";
    print($sql);
    //echo "<pre>";
    // return '';
  }

    // echo "<pre>";
    // print($sql);
    // echo "<pre>";


// select 
//   i8208_codigo,
//   i8200_nome,
//   i8208_servico,
//   i8208_vigencia,
//   i8208_validade,
//   i8208_valor,
//   i8208_status 
// from wp_i8208 
// INNER JOIN wp_i8200 ON wp_i8208.i8208_pessoa = wp_i8200.i8200_codigo  
// where   0 = 0  
// order by i8208_codigo desc limit 0, 10

//tring offset 'total' in /var/www/clients/client32/web41/web/wp-content/plugins/wpmsc-0000/wpmsc-i0000.php 
  //on line 1204



  $tb = scmDbData($sql,'rows',$cnn);
  // print("<pre>");
  // print_r($tb);  
  // print("</pre>");
  $rows['row'] = array();
  $campo_codigo = $tabela_campo.'_codigo';

  if((isset($tb['r'])) && ($tb['r']))
  for ($i=0;$i<$tb['r'];$i++){
    for ($ii=0;$ii<count($fields);$ii++){
      $campo = $col[$ii]['dataIndex'];
      $rows['row'][$i][$campo] = $tb['rows'][$i][($campo)];
      if($fields[$ii]['type']=='string'){
        $rows['row'][$i][$campo] =  strip_tags( $rows['row'][$i][$campo] );
        $rows['row'][$i][$campo] = ($rows['row'][$i][$campo]);//esse resolveu
        // $rows['row'][$i][$campo] = strtoupper($rows['row'][$i][$campo]);//esse resolveu
      }
      if($fields[$ii]['type']=='date'){
        $rows['row'][$i][$campo] = scmDateMysqlBr($rows['row'][$i][$campo]);
      }
      if($fields[$ii]['type']=='blob'){
        $rows['row'][$i][$campo] = ($rows['row'][$i][$campo]);//esse resolveu
      }
      if($col[$ii]['dataIndex']==$campo_codigo){
        $rows['row'][$i][$campo] = str_pad($rows['row'][$i][$campo], 6, "0", STR_PAD_LEFT);
      }
    }
  }
  //troca url - ini



  $ret = "";
  $url = $_SERVER["REDIRECT_URL"];
  $add_class = "wpmsc";
  if(substr($url,1,6)=='wpmsc') {
    $add_class = "wpmsc_link_ajax";
  };



$codigo = isset($_GET['cod']) ? $_GET['cod'] : 0;
  if((isset($tb['r'])) && ($tb['r']))
  for ($i=0;$i<$tb['r'];$i++){
    for ($ii=0;$ii<count($fields);$ii++){
      $url_painel = $fields[$ii]['url_painel'];
      $vai = 1;
      if($url_painel){
        $vai = wpmsc_role_logic($url_painel);
      }
      if($vai){
        if($fields[$ii]['url']){
          $campo = $col[$ii]['dataIndex'];
          $value = $rows['row'][$i][$campo];
          $value = $fields[$ii]['url'];
          $campo_codigo = $tabela_campo.'_codigo';
          $value = html_entity_decode($value);
          $value = preg_replace("/__tcod__/i",  strip_tags($rows['row'][$i][$campo_codigo]), $value);
          $value = preg_replace("/__this_cod__/i",  $rows['row'][$i][$campo_codigo], $value);
          $value = preg_replace("/__cod__/i",  $rows['row'][$i][$campo_codigo], $value);
          $value = preg_replace("/__xxx__/i",  '__yyy__', $value);
          $value = preg_replace("/__codigo__/i",  $codigo, $value);
          $value = preg_replace("/__pai__/i",  (isset($_GET['pai']) ? $_GET['pai'] : ''), $value);
          $value = preg_replace("/__this__/i", $rows['row'][$i][$campo], $value);
          $value = preg_replace("/__ucod__/", (isset($_GET['cod']) ? $_GET['cod'] : 0) , $value);
          $value = preg_replace("/__site_url__/",site_url() , $value);
          $value = preg_replace("/__upload_dir__/",$udir['baseurl'] , $value);

          $value = preg_replace("/__wpmsc_ajax_url__/",$url , $value);
          $value = preg_replace("/__wpmsc_class_url__/",$add_class , $value);

          $value = preg_replace("/__user__/i",  get_current_user_id(), $value);


          // $value = '--=--';
          $value = html_entity_decode($value);
          $rows['row'][$i][$campo] = $value;
          for ($iii=0;$iii<  count($fields); $iii++){
            $campoiii = strtolower($fields[$iii]["name"]);
            if (preg_match("/__".$campoiii."__/i", $value)) {
              $value = preg_replace("/__".$campoiii."__/i",   $rows['row'][$i][$campoiii]   , $value);
            }
          }
          $rows['row'][$i][$campo] = $value;
        }
      }
    }
  }

  //troca url - end
//--- TOTAL

// echo '<pre>
// ---rows---';
// print_r($rows);
// echo '---fields---';
// print_r($fields);
// echo '---col---';
// print_r($col);

// echo '</pre>';

// if ($md==8208) {
//   $inner .= " INNER JOIN wp_i8200 ON wp_i8208.i8208_pessoa = wp_i8200.i8200_codigo ";
// }



  $sql = "select count(".$col[0]["dataIndex"].") qtd ";
  $sql .= " from ".$tabela_cliente;
  $sql .= " ".$inner;
  $sql .= " where ";
  $sql .= $where;
  $sql = preg_replace("/__user__/i",  get_current_user_id(), $sql);
  $tb = scmDbData($sql,'rows',$cnn);

  // print('<pre>');
  // print($sql);
  // print('</pre>');
  
  $rows['total'] = isset($tb['rows'][0]['qtd']) ? $tb['rows'][0]['qtd'] : 0;
  $somas = array();
  for ($ii=0;$ii<count($fields);$ii++){
    $somas[$ii] = '-';
      $field = strtoupper($fields[$ii]["name"]);
  }
  $rows['db_host'] = get_the_author_meta('db_host', get_current_user_id());
  return $rows;
}
endif;
















if (!function_exists('scmGetMdEdit')) :
function scmGetMdEdit($md,$cod,$cnn){
  global $wpdb;
  $ret_md_edit = array();
  $campo = array();
  $rules = array();
  $ret_md_edit['campo'] = $campo;
  $ret_md_edit['rules'] = $rules;
  $modulo_conf = scmGetModuloConf($md);
  $sql = "select * from ".scmPrefix(true)."md000001 where md000001_modulo = ".$md." and md000001_ativo = 's'order by md000001_ordem";
  $tb = scmDbExe($sql,'rows');
  $rows = $tb['rows'];
  $items = array();
  $i=0;
  $r=0;
  foreach ($rows as $row){
    $vai = scmSelectVai($row['md000001_ctr_edit'],'edit');
    if($vai){
      $campo[$i]["inputId"]     = $row['md000001_campo'];
      $campo[$i]["type"]      = $row['md000001_tipo'];
      $campo[$i]["name"]      = $row['md000001_campo'];
      $campo[$i]["xtype"]     = strtolower($row['md000001_ctr_edit']);
      $campo[$i]["fieldLabel"]  = (($row['md000001_label']));
      $campo[$i]['value']     = '';
      $campo[$i]['black']     = $row['md000001_black'];
      if($campo[$i]["xtype"]=='textfield') $campo[$i]["type"] = 'text';
      if($campo[$i]["xtype"]=='combobox'){
        $campo[$i]["type"] = 'select';
        $sql2  = "select ";
        $sql2 .= $row['md000001_cmb_codigo'].", ".$row['md000001_cmb_descri']." ";
        $sql2 .= "from ".$row['md000001_cmb_source']." ";
        $sql2 .= "order by ".$row['md000001_cmb_descri']." ";
        $sql2 .= " ;";
        $tb2 = scmDbExe($sql2,'rows');
        $rows2 = $tb2['rows'];
        $j = 0;
        $cmb_store = "";
        $c1 = ($row['md000001_cmb_codigo']);
        $c2 = ($row['md000001_cmb_descri']);
        foreach ($rows2 as $row2){
          $campo[$i]['store'][$j]['cod'] = $row2[$c1];
          $campo[$i]['store'][$j]['value'] = scmWin1252ToUtf8($row2[$c2]);
          $campo[$i]['store'][$j]['selected'] = '';
          $j++;
        }
      }
      if(!$campo[$i]['black']){
        $name = $campo[$i]["name"];
        $rules[$name]['required'] = true;
        $r++;
      }
      $i++;
    }
  }
  $tabela = $tb['rows'][0]['md000001_tabela'];
  $tabela_name = scmPrefix(true).$tb['rows'][0]['md000001_tabela'];
  $tabela_cliente = scmPrefix(false).$tb['rows'][0]['md000001_tabela'];
  $tabela_campo = $tb['rows'][0]['md000001_tabela'];

  $grupo = $modulo_conf['grupo'];
  // if(!$grupo) $grupo = get_grupo_id($md);
  $sql = "select ";
  for ($i=0;$i<count($campo);$i++){
    if($i>0) $sql .= ',';
    $sql .= $campo[$i]["name"];
  }

  $de_sistema = ($modulo_conf['de_sistema']=='s') ? true : false;
  $tabela_cliente = scmPrefix($de_sistema).$modulo_conf['tabela'];

  $sql .= ' from '.$tabela_cliente." ";
  $sql .= "where ";
  $sql .= $tabela_campo."_codigo = ".$cod." ";
  $tb = scmDbData($sql,'rows',$cnn);
  $r=0;
  for ($i=0;$i<count($campo);$i++){
    $ccampo = ($campo[$i]["name"]);
    $value = isset($tb['rows'][0][$ccampo]) ? $tb['rows'][0][$ccampo] : '';
    $type = $campo[$i]["type"];
    $xtype = $campo[$i]['xtype'];
    if($campo[$i]["xtype"]=='combobox'){
      for ($ii=0;$ii<count($campo[$i]["store"]);$ii++){
        if($campo[$i]["store"][$ii]['cod']==$value){
          $campo[$i]["store"][$ii]['selected'] = 'selected';
        }
      }
    }
    if(($type=='text') || ($type=='string')){
      // $value = scmRetiraAcentoUtf($value);
      $value = ($value);
    }
    if($type=='float'){
      $value = scmMoedaBr($value);
    }
    if($type=='date'){
      if($value=='null'){
        $value = '';
      }else{
        $value = scmDateMysqlBr($value);
      }
    }
    $campo[$i]["value"] = $value;
  }
  $ret_md_edit['campo'] = $campo;
  $ret_md_edit['rules'] = $rules;
  return $ret_md_edit;
}
endif;



if (!function_exists('scmGetMdView')) :
function scmGetMdView($md,$cod,$cnn,$df){
  global $wpdb;
  $ret_md_edit = array();
  $campo = array();
  $rules = array();
  $ret_md_edit['campo'] = $campo;
  $ret_md_edit['rules'] = $rules;
  $modulo_conf = scmGetModuloConf($md);
  $sql = "select * from ".scmPrefix(true)."md000001 where md000001_modulo = ".$md." and md000001_ativo = 's' order by md000001_ordem";
  $tb = scmDbExe($sql,'rows');
  $rows = $tb['rows'];
  // echo '<pre>';
  // print_r($rows);
  $items = array();
  $i=0;
  $r=0;
  foreach ($rows as $row){


    $vai = scmSelectVai($row['md000001_ctr_view'],'view');
    if($vai) {
      if($row['md000001_renderer']){
        // $vai = $row['md000001_renderer'];
        $vai = wpmsc_role_logic($row['md000001_renderer']);
      }

      //md000001_renderer
    }


    if($vai){
      $campo[$i]["inputId"]   = $row['md000001_campo'];
      $campo[$i]["type"]      = $row['md000001_tipo'];
      $campo[$i]["name"]      = $row['md000001_campo'];
      $campo[$i]["xtype"]     = strtolower($row['md000001_ctr_edit']);
      $campo[$i]["fieldLabel"]  = (($row['md000001_label']));
      $campo[$i]['value']     = '';
      $campo[$i]['black']     = $row['md000001_black'];
      $campo[$i]['url']       = $row['md000001_url'];
      $campo[$i]['url_painel']= $row['md000001_url_painel'];
      if($campo[$i]["xtype"]=='textfield') $campo[$i]["type"] = 'text';
      if($campo[$i]["xtype"]=='combobox'){
        $campo[$i]["type"] = 'select';
        $sql2  = "select ";
        $sql2 .= $row['md000001_cmb_codigo'].", ".$row['md000001_cmb_descri']." ";
        $sql2 .= "from ".$row['md000001_cmb_source']." ";
        $sql2 .= "order by ".$row['md000001_cmb_descri']." ";
        $sql2 .= " ;";
        $tb2 = scmDbExe($sql2,'rows');
        $rows2 = $tb2['rows'];
        $j = 0;
        $cmb_store = "";
        $c1 = ($row['md000001_cmb_codigo']);
        $c2 = ($row['md000001_cmb_descri']);
        foreach ($rows2 as $row2){
          $campo[$i]['store'][$j]['cod'] = $row2[$c1];
          $campo[$i]['store'][$j]['value'] = ($row2[$c2]);
          $campo[$i]['store'][$j]['selected'] = '';
          $j++;
        }
      }
      if(!$campo[$i]['black']){
        $name = $campo[$i]["name"];
        $rules[$name]['required'] = true;
        $r++;
      }
      $i++;
    }
  }
  $tabela = $tb['rows'][0]['md000001_tabela'];
  $tabela_name = scmPrefix(true).$tb['rows'][0]['md000001_tabela'];
  $tabela_cliente = scmPrefix(false).$tb['rows'][0]['md000001_tabela'];
  $tabela_campo = $tb['rows'][0]['md000001_tabela'];
  // $grupo = $modulo_conf['grupo'];
  // if(!$grupo) $grupo = get_grupo_id($md);

// echo $tabela;
  $de_sistema = ($modulo_conf['de_sistema']=='s') ? true : false;
  $tabela_cliente = scmPrefix($de_sistema).$modulo_conf['tabela'];

  $col_replace = $df['col_replace'];
  // echo $col_replace;
  // exit;
  if($col_replace){
    $resplace = explode(",", $col_replace);
    foreach ($resplace as $keyc => $valuec) {
      $arrray = explode(":", $valuec);
      foreach ($campo as $key => $value) {
        if ($value['name']==$arrray[0]) {
          $campo[$key]['name'] = $arrray[1];
          $campo[$key]['type'] = 'string';
        }
      }
    }
  }

  $sql = "select ";
  for ($i=0;$i<count($campo);$i++){
    if($i>0) $sql .= ',';
    $sql .= $campo[$i]["name"];
  }
  $sql .= ' from '.$tabela_cliente." ";
  $sql .= ' '.$df['inner']." ";

  

  

  $sql .= "where ";
  $sql .= $tabela_campo."_codigo = ".$cod." ";

  $tb = scmDbData($sql,'rows',$cnn);
  // echo $cnn;
  //echo $sql;
  
  // $tb = scmDbExe($sql,'rows');

  // echo "<pre>";
  // print_r($tb);
  // echo "</pre>"; 
  /// die();
  
  $r=0;
  for ($i=0;$i<count($campo);$i++){
    $ccampo = ($campo[$i]["name"]);
    $value = isset($tb['rows'][0][$ccampo]) ? $tb['rows'][0][$ccampo] : '';
    $type = $campo[$i]["type"];
    $xtype = $campo[$i]['xtype'];
    if($campo[$i]["xtype"]=='combobox'){
      for ($ii=0;$ii<count($campo[$i]["store"]);$ii++){
        if($campo[$i]["store"][$ii]['cod']==$value){
          $campo[$i]["store"][$ii]['selected'] = 'selected';
        }
      }
    }
    if(($type=='text') || ($type=='string')){
      $value = scmRetiraAcentoUtf($value);
      $value = ($value);
    }
    if($type=='float'){
      $value = scmMoedaBr($value);
    }
    if($type=='date'){
      if($value=='null'){
        $value = '';
      }else{
        $value = scmDateMysqlBr($value);
      }
    }


    $campo_codigo = $tabela_campo.'_codigo';
    if($ccampo==$campo_codigo){
      $campo[$i]["value"] = str_pad($campo[$i]["value"], 6, "0", STR_PAD_LEFT);
    }




    $campo[$i]["value"] = $value;
  }



  //troca url - ini
  $tabela = $modulo_conf['tabela'];
  $tabela_name = scmPrefix(true).$tabela;
  $tabela_cliente = scmPrefix(false).$tabela;
  $tabela_campo = $tabela;

  // echo '<pre>';
  // print_r($campo);

  $campo_codigo = $tabela_campo.'_codigo';
  for ($i=0;$i <  count($campo); $i++){
    if($campo[$i]['name'] == $campo_codigo){
      $codigo = $campo[$i]['value'];
    }
  }

  for ($i=0;$i<count($campo);$i++){
    if($campo[$i]['url']){

      $url_painel = $campo[$i]['url_painel'];
      $vai = 0;
      if($url_painel){
        $vai = wpmsc_role_logic($url_painel);
      }
      if($vai){



        $value = $campo[$i]['url'];
        // $value = html_entity_decode($campo[$i]["value"]);
        // $value = preg_replace("/__tcod__/i",  $rows['row'][$i][$campo_codigo], $value);
        $value = preg_replace("/__cod__/i",  $codigo, $value);
        $value = preg_replace("/__xxx__/i",  '__yyy__', $value);
        $value = preg_replace("/__this__/i", $campo[$i]["value"], $value);
        $value = preg_replace("/__pai__/i",  (isset($_GET['pai']) ? $_GET['pai'] : ''), $value);
        // $value = html_entity_decode($value);
        for ($iii=0;$iii <  count($campo); $iii++){
          $campoiii = strtolower($campo[$iii]["name"]);
          if (preg_match("/__".$campoiii."__/i", $value)) {
            $value = preg_replace("/__".$campoiii."__/i", $campo[$iii]["value"], $value);
          }
        }
        $campo[$i]["value"] = $value;
      }
    }
  }
  //troca url - end
  $ret_md_edit['campo'] = $campo;
  $ret_md_edit['rules'] = $rules;
  return $ret_md_edit;
}
endif;


if (!function_exists('scmMdInsert')) :




function scmMdInsert($md,$values=array(),$cnn,$insert_add){
  global $wpdb;
  $modulo_conf = scmGetModuloConf($md);
  // $grupo = $modulo_conf['grupo'];
  // if(!$grupo) $grupo = get_grupo_id($md);
  $sql = "select * from ".scmPrefix(true)."md000001 where md000001_modulo = ".$md." and md000001_ativo = 's' order by md000001_ordem ";
  $tb = scmDbExe($sql,'rows');
  $rows = $tb['rows'];
  // echo '<pre>';
  // print_r($rows);
  // die();
  $i=0;
  $campo = array();
  foreach ($rows as $row){
    $vai = scmSelectVai($row['md000001_ctr_new'],'novo');
    if($vai){
      $name = $row['md000001_campo'];
      if(isset($values[$name])){
        $campo[$i]['name']    = $row['md000001_campo'];
        $campo[$i]['type']    = $row['md000001_tipo'];
        $campo[$i]['value']   = isset($values[$name]) ? $values[$name] : '';
        $campo[$i]['xtype']   = strtolower($row['md000001_ctr_new']);
        $i++;
      }
    }
  }

  // echo '<pre>';
  // print_r($campo);
  // die();


/*
    if($vai){
      $name = $row['md000001_campo'];
      $nameU = strtoupper($name);
      $nameL = strtolower($name);
      $vai2 = false;
      // $vai2 = isset($_POST[$nameL]) ? true : false;
      $vai2 = isset($_REQUEST[$nameL]) ? true : false;
      if($vai2){
        $campo[$i]['name'] = $row['md000001_campo'];
        $campo[$i]['type']    = $row['md000001_tipo'];
        // $campo[$i]['value']   = $_POST[$name];
        $campo[$i]['value']   = $_REQUEST[$name];
        $i++;
      }
    }

*/
  $modulo_conf = scmGetModuloConf($md);
  $tabela = scmPrefix(true).$modulo_conf['tabela'];
  $tabela_cliente = scmPrefix(false).$modulo_conf['tabela'];
  // $de_sistema = ($modulo_conf['md000002_de_sistema']=='s') ? true : false;
  $i_old = $i;
  for ($i=0;$i<$i_old;$i++){
    if(($campo[$i]['xtype']=="checkbox") && ($campo[$i]['type']=='string')){
      if(!$campo[$i]['value']) {
        $campo[$i]['value'] = 'N';
      } else {
        $campo[$i]['value'] = 'S';
      }
    }
    if(($campo[$i]['xtype']=="checkbox") && ($campo[$i]['type']=='int')){
      if(!$campo[$i]['value']) {
        $campo[$i]['value'] = 0;
      } else {
        $campo[$i]['value'] = 1;
      }
    }
    if($campo[$i]['type']=='date'){
      if(!$campo[$i]['value']){
        $campo[$i]['value'] = 'null';
      }else{
        $campo[$i]['value'] = mscDateBrPhp($campo[$i]['value']);
        $campo[$i]['value'] = "'".$campo[$i]['value']."'";
      }
    }
    // if($campo[$i]['type']=='blob')    $campo[$i]['value'] = "'".scmUtf8ToWin1252($campo[$i]['value'])."'";
    if($campo[$i]['type']=='blob')    $campo[$i]['value'] = "'".($campo[$i]['value'])."'";
    if(($campo[$i]['type']=='string') || ($campo[$i]['type']=='varchar')){
      $campo[$i]['value'] = "'".($campo[$i]['value'])."'";
      // $campo[$i]['value'] = scmRetiraAcentoUtf($campo[$i]['value']);
      $de_sistema = $modulo_conf['de_sistema'];
    }
    if($campo[$i]['type']=='int')   {if(!$campo[$i]['value']) $campo[$i]['value'] = 0;}
    if($campo[$i]['type']=='float')   {
      if(!$campo[$i]['value']) $campo[$i]['value'] = 0;
      $campo[$i]['value'] = scmMoedaBrUs($campo[$i]['value']);
    }
  }
  $c = $i;


  // echo '<pre>';
  // print_r($campo);
  // die();

  // //===== fielter add - ini =================
  // $sql = "
  // select
  //     i0005.i0005_field,
  //     i0005.i0005_type,
  //     i0005.i0005_value
  // from i0005
  // where i0005.i0005_modulo = ".$md."
  // ";
  // $tb = scmDbExe($sql,'rows');
  // $iii = 0;
  // $ret['filterAdd'] = $tb['rows'];
  // $achou = 0;
  // for ($i=0;$i<$tb['r'];$i++){
  //   $field  = $tb['rows'][$i]['i0005_field'];
  //   $type   = $tb['rows'][$i]['i0005_type'];
  //   $value  = $tb['rows'][$i]['i0005_value'];
  //   if($type=='string')  $value = "'".$value."'";
  //   $campo[$c]['name'] = $field;
  //   $campo[$c]['value'] = $value;
  //   $campo[$c]['type'] = $type;
  //   $c++;
  // }
  // $result_insert = array();
  // //===== fielter add - end =================
  // $modulo_conf = scmGetModuloConf($md);
  // $grupo = $modulo_conf['grupo'];
  // $user = $modulo_conf['user'];
  // if(!$grupo) $grupo = get_grupo_id($md);
  // if(!$user) $user = get_membro_codigo($md);

  $de_sistema = ($modulo_conf['de_sistema']=='s') ? true : false;
  $tabela_cliente = scmPrefix($de_sistema).$modulo_conf['tabela'];
  // if($md==8126){
  //   $tabela_cliente = scmPrefix($de_sistema).'i'.$md;
  // }


  // echo "--insert_add: $insert_add--";
  // die('---999---');

    if($insert_add){
      $insadd = explode(",", $insert_add);
      $insadd_key ='';
      $insadd_value = '';
      $insadd_i = 0;
      // echo '<pre>';
      // print_r($insadd);
      // echo '</pre>';
      foreach ($insadd as $key => $value) {
        $insadditem = explode("=", $value);
        // if($insadd_i){
         $insadd_key .= ',';
         $insadd_value .= ',';
        // }
        $insadd_key .= $insadditem[0];
        $insadd_value .= $insadditem[1];
        $insadd_i++;
      }

      // echo "---insadd_key: $insadd_key---";
      // echo '<br>';
      // echo "---insadd_value: $insadd_value---";

      // echo "--insert_add: $insert_add--";
      // die('---999---');

    }


  $sql = "insert into ".$tabela_cliente." ";
  $sql_insert = '';
  $sql_values = '';
  for ($i=0;$i<count($campo);$i++){
    if($i > 0){
      $sql_insert .= ",";
      $sql_values .= ",";
    }
    $sql_insert .= $campo[$i]['name'];
    $sql_values .= $campo[$i]['value'];
  }
  if(($modulo_conf['grupalizar']) && ($modulo_conf['conexao'] >=2)){
    $sql_insert .= ", ".$modulo_conf['tabela'].'_id_sysempresa ';
    $sql_insert .= ", ".$modulo_conf['tabela'].'_id_sysusuario ';
    $sql_values .= ", ".get_grupo_id($md);
    $sql_values .= ", ".get_membro_codigo($md);
  }

  $sql_insert .= $insadd_key;
  $sql_values .= $insadd_value;


  $sql .= "(".$sql_insert.")";
  $sql .= " values ";
  $sql .= "(".$sql_values.")";
  


  $ret = scmDbData($sql,'insert',$cnn);


  // echo '<pre>';
  // print_r($sql);
  // die();

  // if($md==9130){
  //   print('<pre>');
  //   print($sql);
  //   print_r($ret);
  //   print('</pre>');
  //   die();
  // }


}
endif;


if (!function_exists('scmMdUpdate')) :
function scmMdUpdate($md,$cod,$cnn){
  global $wpdb;
  $sql = "
    select
      md000001_codigo,
      md000001_ctr_edit,
      md000001_campo,
      md000001_largura,
      md000001_tipo
    from
      ".scmPrefix(true)."md000001
    where
    (
      (
        md000001_modulo = ".$md."
      )
      and
      (
        md000001_ativo = 's'
      )
    )
    order by md000001_ordem
  ";
  // echo "<pre>";
  // echo $sql;
  // echo "</pre>";
  
  $tb = scmDbExe($sql,'rows');
  $return_update = array();
  $i=0;
  $campo = array();
  $rows = $tb['rows'];
  // print_r($rows);
  // die();
  foreach ($rows as $row){
    $vai = scmSelectVai($row['md000001_ctr_edit'],'edit');
    if($vai){
      $name = $row['md000001_campo'];
      $nameU = strtoupper($name);
      $nameL = strtolower($name);
      $vai2 = false;
      // $vai2 = isset($_POST[$nameL]) ? true : false;
      $vai2 = isset($_REQUEST[$nameL]) ? true : false;
      if($vai2){
        $campo[$i]['name'] = $row['md000001_campo'];
        $campo[$i]['type']    = $row['md000001_tipo'];
        // $campo[$i]['value']   = $_POST[$name];
        $campo[$i]['value']   = $_REQUEST[$name];
        $i++;
      }
    }
  }
  $modulo_conf = scmGetModuloConf($md);
  $tabela = $modulo_conf['tabela'];
  $tabela_name = scmPrefix(true).$tabela;
  $tabela_cliente = scmPrefix(false).$tabela;
  $tabela_campo = $tabela;

  $i_old = $i;
  for ($i=0;$i<$i_old;$i++){
    if($campo[$i]['type']=='date'){
        $campo[$i]['value'] = mscDateBrPhp($campo[$i]['value']);
        $campo[$i]['value'] = "'".$campo[$i]['value']."'";
    }

    if($campo[$i]['type']=='blob')    $campo[$i]['value'] = "'".($campo[$i]['value'])."'";
    if(($campo[$i]['type']=='string') || ($campo[$i]['type']=='varchar')){
      $campo[$i]['value'] = scmRetiraAcentoUtf($campo[$i]['value']);
      $de_sistema = $modulo_conf['de_sistema'];
      $campo[$i]['value'] = "'".($campo[$i]['value'])."'";
    }
    if($campo[$i]['type']=='int')   {if(!$campo[$i]['value']) $campo[$i]['value'] = 0;}
    if($campo[$i]['type']=='float')   {
      if(!$campo[$i]['value']) $campo[$i]['value'] = 0;
      $campo[$i]['value'] = scmMoedaBrUs($campo[$i]['value']);
    }
    if($campo[$i]['type']=='float')   {$campo[$i]['value'] =  scmMoedaBrUs($campo[$i]['value']);}
  }
  $return_update['campo'] = $campo;

  $de_sistema = ($modulo_conf['de_sistema']=='s') ? true : false;
  $tabela_cliente = scmPrefix($de_sistema).$modulo_conf['tabela'];

  $sql = "update ".$tabela_cliente." set ";
  for ($i=0;$i<count($campo);$i++){
    if($i>0) $sql .=", ";
    $sql .= $campo[$i]['name'].' = '.$campo[$i]['value'];
  }
  $sql .= " where ".$tabela_campo."_codigo = ".$cod." ";
  // echo $sql;

  // echo "<pre>";
  // echo $sql;
  // echo '---';
  // echo "</pre>";
  // die();

  //$return_update['sql'] = $sql;
  // $ret = scmDbExe($sql,'insert',$modulo_conf['conexao']);
  //$rret = scmDbExe($sql,'insert',$modulo_conf['conexao']);
  // return 1;

  // $ret = $wpdb->query($sql);
  //scmDbData
  $ret = scmDbData($sql,'update',$cnn);

  // echo "<pre>";
  // print_r($ret);
  // echo "</pre>";
  // die();

  return  $ret;


}
endif;


if (!function_exists('scmGetMdNovo')) :
function scmGetMdNovo($md){
  $ret_md_novo = array();
  $campo = array();
  $rules = array();
  $ret_md_novo['campo'] = $campo;
  $ret_md_novo['rules'] = $rules;
  // $sql =  "select * from md000001 where md000001_modulo = ".$md." and md000001_ativo = 's' order by md000001_ordem";
  $rows = scmGetFields($md);
  // $tb = scmDbExe($sql,'rows');
  // $rows = $tb['rows'];

  // echo '<pre>';
  // print_r($rows);
  // die('scmGetMdNovo');

  $i=0;
  $r=0;
  if(count($rows)){
    foreach ($rows as $row){
      $vai = scmSelectVai($row['ctr_new'],'novo');
      if($vai){
        $campo[$i]["inputId"] = strtolower($row['name']);
        $campo[$i]["type"] = $row['tipo'];
        $campo[$i]["name"] = $row['name'];
        // $campo[$i]["ctr_new"] = $row['md000001_ctr_new'];
        $campo[$i]["value"] = '';
        $campo[$i]["cls"] = $row['cls'];
        $campo[$i]["xtype"] = strtolower($row['ctr_new']);
        $campo[$i]["fieldLabel"]  = strtoupper(scmWin1252ToUtf8($row['label']));
        $campo[$i]['width'] = 550;
        $campo[$i]['black'] = ($row['black']) ? true : false;
        $campo[$i]['placeholde'] = '';//novo
        if($campo[$i]["xtype"]=='textfield') $campo[$i]["type"] = 'text';
        if($campo[$i]["xtype"]=='combobox'){
          $campo[$i]["type"] = 'select';
          $sql2  = "select  ".$row['cmb_codigo'].", ".$row['cmb_descri']." ";
          $sql2 .= "from ".$row['cmb_source']." ";
          $sql2 .= "order by ".$row['cmb_descri']." ";
          $sql2 .= " ;";

          $campo[$i]["sql_combo"] = $sql2;

          $tb2 = scmDbData($sql2,'rows');
          $rows2 = $tb2['rows'];

          $j = 1;
          $cmb_store = "";
          $c1 = ($row['cmb_codigo']);
          $c2 = ($row['cmb_descri']);
          $campo[$i]['store'][0]['cod'] = '';
          $campo[$i]['store'][0]['value'] = '';
          $campo[$i]['store'][0]['selected'] = 'selected';
          foreach ($rows2 as $row2){
            $campo[$i]['store'][$j]['cod'] = $row2[$c1];
            $campo[$i]['store'][$j]['value'] = ($row2[$c2]);
            $campo[$i]['store'][$j]['selected'] = '';
            $j++;
          }
        }
        if(!$campo[$i]['black']){
          $name = $campo[$i]["name"];
          $rules[$name]['required'] = true;
          $r++;
        }
        $i++;
      }
      // print_r($campo);
      // die();
    }
    $ret_md_novo['campo'] = $campo;
    $ret_md_novo['rules'] = $rules;

  }
  return $ret_md_novo;
}
endif;



if (!function_exists('scmMdDelete')) :
function scmMdDelete($md,$cod){
  global $wpdb;
  $modulo_conf = scmGetModuloConf($md);


  $tabela = $modulo_conf['tabela'];
  $tabela_name = scmPrefix(true).$tabela;
  $tabela_cliente = scmPrefix(false).$tabela;
  $tabela_campo = $tabela;

  $sql = "delete from ".$tabela_cliente." where ".$tabela_campo."_codigo = ".$cod.";";
  return scmDbData($sql,'delete');
}
endif;


if (!function_exists('mscGetList')) :
function mscGetList($mds){
  $mds = scmGetMdCol($mds);
  $mds = scmGetMdRows($mds);
  return $mds;
}
endif;


if (!function_exists('scmMdDuplique')) :
function scmMdDuplique($md,$cod,$cnn){
  $md_edit = scmGetMdEdit($md,$cod,$cnn);
  $campos = $md_edit['campo'];

  $values = array();
  for ($i=0; $i < count($campos); $i++) {
    $campo = $campos[$i]['name'];
    $value = $campos[$i]['value'];
    $values[$campo] = $value;
  }
  scmMdInsert($md,$values,$cnn);
  return $values;

}
endif;


if (!function_exists('scmGetMdCol')) :
function scmGetMdCol($md,$cnn='',$df=array()){
  $sql = "
  select
    md000001_codigo,
    md000001_ctr_list,
    md000001_campo,
    md000001_tipo,
    md000001_label,
    md000001_largura,
    md000001_tabela,
    md000001_cmb_source,
    md000001_cmb_descri,
    md000001_hidden
  from ".scmPrefix(true)."md000001
  where
    (
      (md000001_modulo = ".$md.")
     and
      (md000001_ativo = 's')
    )
  order by md000001_ordem
  ";
  $tb = scmDbExe($sql,'rows');
  $rows = $tb['rows'];
  // echo $sql;
  // echo '<pre>';
  // print_r($rows);
  // echo '</pre>';
  // die();
  // $tabela = $rows[0]['md000001_tabela'];

  $modulo_conf = scmGetModuloConf($md);
  // print('<pre>');
  // print($md);
  // echo 'modulo_conf'."\n";
  // print_r($modulo_conf);
  // die();
  $tabela = $modulo_conf['tabela'];
  // $tabela = 'i'.$md;

  $tabela_name = scmPrefix(true).$tabela;
  $tabela_cliente = scmPrefix(false).$tabela;
  $tabela_campo = $tabela;

  // print('<pre>');
  // print($sql);
  // print('<hr>');
  // print_r($tb);
  // print('</pre>');
  $col = array();
  $c=0;
  for ($i=0;$i<$tb['r'];$i++) {
    $vai = scmSelectVai($rows[$i]['md000001_ctr_list'],'list');
    if($vai){
      $col[$c]["cd"]=$rows[$i]['md000001_codigo'];
      $col[$c]["codigo_name"]=$tabela.'_codigo';
      $col[$c]["text"]=scmWin1252ToUtf8($rows[$i]['md000001_label']);
      $col[$c]["text"] = strtoupper($col[$c]["text"]);
      $col[$c]["dataIndex"]=$rows[$i]['md000001_campo'];
      $col[$c]["width"]=$rows[$i]['md000001_largura'];
      $col[$c]["hidden"]= ($rows[$i]['md000001_hidden']==1) ? true : false;
      if($col[$c]["width"]) $col[$c]["width"] = $col[$c]["width"] * 1.5;
      $col[$c]["filter_type"]=$rows[$i]['md000001_tipo'];
      $col[$c]["filter"]['type']=$rows[$i]['md000001_tipo'];
      if($rows[$i]['md000001_tipo']=='int') {
        $col[$c]["filter_type"] = 'numeric';
        $col[$c]["filter"]['type']  = 'numeric';
      }
      $col[$c]["inner"] = '';
      $col[$c]["ctr_list"] = $rows[$i]['md000001_ctr_list'];
      if($rows[$i]['md000001_ctr_list']=='combobox'){
        $col[$c]["dataIndex"] = $rows[$i]['md000001_cmb_descri'];
        $col[$c]["type"] = 'string';
        $col[$c]["align"] = 'left';
        $col[$c]["filter_type"] = 'string';
        $col[$c]["filter"]['type']  = 'string';
        $len2 = strlen($rows[$i]['md000001_tabela']);$len2++;
        $cp_fk2 = substr($rows[$i]['md000001_campo'],$len2);
        $inner0 = $rows[$i]['md000001_cmb_source'];
        $inner1 = $rows[$i]['md000001_tabela'].".".$rows[$i]['md000001_tabela']."_".$cp_fk2;
        $inner2 = $rows[$i]['md000001_cmb_source'].".".$rows[$i]['md000001_cmb_source']."_codigo";
        $inner = "inner join ".$inner0." on (".$inner1." = ".$inner2.") ";
        $col[$c]["inner"] = $inner;
      }
      $c++;
    }
  }

if($df['msc_col_add']){
  $msc_col_add_a=explode(",", $df['msc_col_add']);
  foreach ($msc_col_add_a as $key => $value) {
    $tmp_field_config = explode(":", $value);

    $col[$c]['codigo_name'] = $tmp_field_config[0];// $value;
    $col[$c]['text'] = $tmp_field_config[1];
    $col[$c]['dataIndex'] = $tmp_field_config[0];
    $col[$c]['width'] = '';
    $col[$c]['hidden'] = '';
    $col[$c]['filter_type'] = $tmp_field_config[3];//'numeric';
    $col[$c]['filter']['type'] = $tmp_field_config[3];//'numeric';
    $col[$c]['inner'] = '';
    $col[$c]['ctr_list'] = 'label';
    // $col[$c]["text"] = strtoupper('qtd');
    $c++;
  }
  // $col[$c]['codigo_name'] = $df['msc_col_add'];
  // $col[$c]['text'] = $df['msc_col_add'];
  // $col[$c]['dataIndex'] = $df['msc_col_add'];
  // $col[$c]['width'] = '';
  // $col[$c]['hidden'] = '';
  // $col[$c]['filter_type'] = 'numeric';
  // $col[$c]['filter']['type'] = 'numeric';
  // $col[$c]['inner'] = '';
  // $col[$c]['ctr_list'] = 'label';
  // $col[$c]["text"] = strtoupper('qtd');

}
// echo '<pre>';
// print_r($df);
// echo 'df';
  return $col;
}
endif;


if (!function_exists('scmGetFields')) :
function scmGetFields($md, $cnn='', $df=array()){
  $fields = array();
  $sql = "
  select
    md000001_codigo,
    md000001_ctr_new,
    md000001_ctr_list,
    md000001_campo,
    md000001_tipo,
    md000001_formato,
    md000001_cmb_descri,
    md000001_tabela,
    md000001_url,
    md000001_url_md,
    md000001_url_op,
    md000001_label,
    md000001_black,
    md000001_url_painel,
    md000001_cls

  from ".scmPrefix(true)."md000001 where md000001_modulo = ".$md."  and md000001_ativo = 'S' order by md000001_ordem
  ";
  $tb = scmDbExe($sql,'rows');
    // echo '<pre>';
    // print_r($tb);
    // echo $sql;
    // die('scmGetFields');

  $rows = $tb['rows'];
  $c = 0;
  for ($i=0;$i<$tb['r'];$i++){
    $vai = scmSelectVai($rows[$i]['md000001_ctr_list'],'list');
    if($vai){
      $fields[$c]['name'] = $tb['rows'][$i]['md000001_campo'];
      $fields[$c]['type'] = $tb['rows'][$i]['md000001_tipo'];
      $fields[$c]['ctr_new'] = $tb['rows'][$i]['md000001_ctr_new'];

      $fields[$c]['url_painel'] = $tb['rows'][$i]['md000001_url_painel'];





      if($fields[$c]['type']=='date'){
        $fields[$c]['dateFormat'] = 'Y-m-d';
      }
      $fields[$c]['formato']  = $tb['rows'][$i]['md000001_formato'];
      if($rows[$i]['md000001_ctr_list']=='combobox'){
        $fields[$c]['type'] = 'string';
        $fields[$c]["name"] = $rows[$i]['md000001_cmb_descri'];
        $fields[$c]["filter"]['type']='string';
        $len2 = strlen($rows[$i]['md000001_tabela']);$len2++;
        $cp_fk2 = substr($rows[$i]['md000001_campo'],$len2);
      }
      $fields[$c]["url"]      = $rows[$i]['md000001_url'];
      $fields[$c]["url_md"]   = $rows[$i]['md000001_url_md'];
      $fields[$c]["url_op"]   = $rows[$i]['md000001_url_op'];
      $fields[$c]["cls"]   = $rows[$i]['md000001_cls'];
      $fields[$c]["tipo"]   = $rows[$i]['md000001_tipo'];
      $fields[$c]["label"]   = $rows[$i]['md000001_label'];
      $fields[$c]["black"]   = $rows[$i]['md000001_black'];
      $fields[$c]["url_vai"]    = false;
      if($fields[$c]['url_md']){
        if($fields[$c]['url_op']){
          $fields[$c]["type"]     = 'string';
          $url_op = $fields[$c]['url_op'];
          $url_access = get_access($fields[$c]['url_md']);
          $url_access_op = isset($url_access[$url_op]) ? $url_access[$url_op] : false;
          if($url_access_op) $url_vai = $fields[$c]["url_vai"] = true;
        }
      }
      $c++;
    }


  // echo '<pre>';
  // print_r($fields);
  // echo '</pre>';
  // return $fields;



  }



  if($df['msc_col_add']){
    $msc_col_add_a=explode(",", $df['msc_col_add']);
    foreach ($msc_col_add_a as $key => $value) {
      // $value = $df['msc_col_add'];
      $tmp_field_config = explode(":", $value);
      $fields[$c]["name"] = $tmp_field_config[0];//$value;
      $fields[$c]["type"] = $tmp_field_config[4];//"int";
      $fields[$c]["ctr_new"] = "numberfield";
      $fields[$c]["url_painel"] = '';
      $fields[$c]["formato"] = '';
      $fields[$c]["url"] = '';
      $fields[$c]["url_md"] = '';
      $fields[$c]["url_op"] = '';
      $fields[$c]["cls"] = '';
      $fields[$c]["tipo"] = $tmp_field_config[4];//"int";
      $fields[$c]["label"] = "qtrrrd";
      $fields[$c]["black"] = 1;
      $fields[$c]["url_vai"] = '';
      $c++;
    }
  }




  // echo '<pre>';
  // print_r($fields);
  // echo '</pre>';
  return $fields;
}
endif;

if (!function_exists('scmConverteIsoToUtf8')) :
function scmConverteIsoToUtf8( $strContent ){return mb_convert_encoding( $strContent, 'UTF-8', mb_detect_encoding( $strContent, 'UTF-8, ISO-8859-1', true ) );}
endif;


if (!function_exists('scmWin1252ToUtf8')) :
function scmWin1252ToUtf8($string){return  iconv("windows-1252","UTF-8",$string);}
endif;


if (!function_exists('scmUtf8ToWin1252')) :
function scmUtf8ToWin1252($string){return  iconv("UTF-8","ASCII//TRANSLIT",$string);}
endif;


if (!function_exists('scmGetLogado')) :
function scmGetLogado(){$logado = false;if(isset($_SESSION["logado"])){if($_SESSION["logado"]) $logado = true;};return $logado;}
endif;


if (!function_exists('scmGetMembroName')) :
function scmGetMembroName(){if(isset($_SESSION["membro_name"])) return $_SESSION["membro_name"];return '';}
endif;


if (!function_exists('scmGetGrupoName')) :
function scmGetGrupoName(){if(isset($_SESSION["grupo_name"])) return $_SESSION["grupo_name"];return '';}
endif;


if (!function_exists('scmSelectVai')) :
function scmSelectVai($ct,$op){
  if($op=="list"){
    if($ct=='combobox')   return true;
    if($ct=='label')    return true;
    if($ct=='label_user')   return true;
    if($ct=='hidden')   return true;
    return false;
  }
  if($op=="view"){
    if($ct=='label')    return true;
    if($ct=='hidden')   return true;
    return false;
  }
  if($op=="novo"){
    if($ct=='textfield')  return true;
    if($ct=='numberfield')  return true;
    if($ct=='datefield')  return true;
    if($ct=='combobox')   return true;
    if($ct=='textarea')   return true;
    if($ct=='htmleditor')   return true;
    if($ct=='ckeditor')   return true;
    if($ct=='radio')    return true;
    if($ct=='multcheckbox') return true;
    if($ct=='checkbox')   return true;
    if($ct=='checkbox')   return true;
    if($ct=='hidden')     return true;
    return false;
  }
  if($op=="edit"){
    // if($ct=='Label')     return true;
    if($ct=='textfield')  return true;
    if($ct=='numberfield')  return true;
    if($ct=='datefield')  return true;
    if($ct=='combobox')   return true;
    if($ct=='textarea')   return true;
    if($ct=='checkbox')   return true;
    return false;
  }
  if($op=="editu"){
    if($ct=='textfield')  return true;
    if($ct=='numberfield')  return true;
    if($ct=='datefield')  return true;
    if($ct=='combobox')   return true;
    if($ct=='textarea')   return true;
    if($ct=='checkbox')   return true;
    if($ct=='htmleditor')   return true;
    if($ct=='ckeditor')   return true;
    return false;
  }
  return false;
}
endif;


if (!function_exists('scmDateFbBr')) :
function scmDateFbBr($data_fb){
  if($data_fb){
    if(is_array($data_fb)){
      $ano = $data_fb[1];
      $mes = $data_fb[2];
      $dia = $data_fb[3];
    }else{
      list($ano,$mes,$dia) = explode("-", $data_fb);
    }
    return $dia."/".$mes."/".$ano;
  }else{
    return 'null';
  }
}
endif;


if (!function_exists('scmMoedaBr')) :
function scmMoedaBr($valor){
  if(!$valor) $valor = 0;
  return number_format($valor, 2, ',', '.');
  return $valor;
}
endif;





// ---shortcode


function scmPrefix($de_sistema=false){
  return $GLOBALS["wpdb"]->prefix;
}

function scmRecent($atts, $content = null){
  global $wpdb;

  //if ( !is_user_logged_in() ) exit;
  extract(shortcode_atts(array(
    "target" => "",
    "md" => "0",
    "on_op" => ''
  ), $atts));

  $get_url_if_op = isset($_GET['op']) ? $_GET['op'] : '';
  if($on_op) {
    if($on_op=="empty"){
      if($get_url_if_op) return '';
    }else{

    }
    if(!$get_url_if_op)  return '';
    if($get_url_if_op<>$on_op) return '';
  }

  $modulo_conf  = scmGetModuloConf($md);

  $tabela_name = SCM_WPDB_PREFIX.$modulo_conf['tabela'];
  $tabela_campo = $modulo_conf['tabela'];
  $campo_codigo  = $tabela_campo."_codigo";
  $sql = "select $campo_codigo from $tabela_name order by $campo_codigo desc limit 0, 1";
  $tb = scmDbExe($sql,'rows');
  if(!$tb['r']) exit;

  if (!$target) {
    return 'target';
  }
  
  if($tb['r']){
    $cod = $tb['rows'][0][$campo_codigo];
    $target = preg_replace("/__cod__/", $cod , $target);
    echo '<script type="text/javascript">';
    echo  'window.location.href = "'.$target.'"';
    echo '</script>';
    exit;
  }

}
add_shortcode("scmRecent", "scmRecent");

function scm_detalhe($atts, $content = null) {
  //if ( !is_user_logged_in() ) exit;
  extract(shortcode_atts(array(
    "md" => '0',
    "cod" => '0',
  ), $atts));

  $df['md'] =$md;
  $cod = preg_replace("/__cod__/", (isset($_GET['cod']) ? $_GET['cod'] : 0) , $cod);
  $cod = preg_replace("/__pai__/", (isset($_GET['pai']) ? $_GET['pai'] : 0) , $cod);
  $md = preg_replace("/__md__/", (isset($_GET['md']) ? $_GET['md'] : 0) , $md);

  $ret ="";
  if(!$md) {$ret = "wpmsc view - md não especificado";}
  if(!$cod) {$ret = "wpmsc view - cod não especificado";}
  if($ret) {return $ret;exit;}
  $view = scmGetMdView($md,$cod);

  $ret = "";
  $ret .= '';
  $ret .= ' <dl class="dl-horizontal">';
  for ($i=0; $i < count($view['campo']); $i++) {
    $ret .= '<dt>'.$view['campo'][$i]['fieldLabel'].'</dt><dd>'.$view['campo'][$i]['value'].'</dd>';
  }
  $ret .= ' </dl>';

  return $ret;
}
add_shortcode("scm_detalhe", "scm_detalhe");


function scm_view($atts, $content = null) {
  //if ( !is_user_logged_in() ) exit;
  extract(shortcode_atts(array(
    "cnn" => '',
    "md" => '0',
    "cod" => '0',
    "style" => '',
    "un_show" => '',
    "access" => '',
    "role" => '',
    "on_op" => '',
    "inner" => '',
    "col_replace" => ''
  ), $atts));

  if($access){if(!scmIsAccess($access)) return '';}
  if($role){if(!scmIsRole($role)) return '';}

  $df=array();
  $df['inner'] = $inner;
  $df['col_replace'] = $col_replace;

  $get_url_if_op = isset($_GET['op']) ? $_GET['op'] : '';
  if($on_op) {
    if($on_op=="empty"){
      if($get_url_if_op) return '';
    }else{

    }
    if(!$get_url_if_op)  return '';
    if($get_url_if_op<>$on_op) return '';
  }

  $df['md'] =$md;
  $cod = preg_replace("/__cod__/", (isset($_GET['cod']) ? $_GET['cod'] : 0) , $cod);
  $cod = preg_replace("/__pai__/", (isset($_GET['pai']) ? $_GET['pai'] : 0) , $cod);
  $md = preg_replace("/__md__/", (isset($_GET['md']) ? $_GET['md'] : 0) , $md);
  $cod = preg_replace("/__pessoa_by_user__/", get_user_meta( get_current_user_id(), "pessoa_by_user", true ) , $cod);
  
  $ret ="";
  if(!$md) {$ret = "wpmsc view - md não especificado";}
  if(!$cod) {$ret = "wpmsc view - cod não especificado";}
  if($ret) {return $ret;exit;}
  $view = scmGetMdView($md,$cod,$cnn,$df);

  // echo "<pre>";
  // print_r($view) ;
  // echo "</pre>";

  $ret = "";
  $ret .= '';
  $ret .= ' <form class="form-horizontal" action="" method="POST" style="'.$style.'">';
  for ($i=0; $i < count($view['campo']); $i++) {
    //xxx_xxx
    if(($un_show) && (preg_match("/".$view['campo'][$i]['name']."/i", $un_show))){

    } else{

      $ret .= ' <div class="form-group pd0" style="margin-bottom:2px;padding-right:10px;" >';
      $ret .= '   <label class="col-sm-3 control-label" style="font-style: italic;font-size: 12px;padding-right: 15px;">'.$view['campo'][$i]['fieldLabel'].': </label>';
      $ret .= '   <div class="col-sm-9 bgw_ colorb_" style="min-height:30px;font-weight: bolder;">';
      // $ret .= '     <p class="form-control-static" style="font-size: 14px;">'.$view['campo'][$i]['value'].'</p>';
      $ret .= '     '.$view['campo'][$i]['value'].' ';

  
      $ret .= '   </div>';
      $ret .= ' </div>';
    }
  }
  $ret .= ' </form>';

  return $ret;
}
add_shortcode("scm_view", "scm_view");

function scm_det($atts, $content = null) {
  //if ( !is_user_logged_in() ) exit;
  extract(shortcode_atts(array(
    "md" => '0',
    "cod" => '0',
  ), $atts));

  $df['md'] =$md;
  $cod = preg_replace("/__cod__/", (isset($_GET['cod']) ? $_GET['cod'] : 0) , $cod);
  $cod = preg_replace("/__pai__/", (isset($_GET['pai']) ? $_GET['pai'] : 0) , $cod);
  $md = preg_replace("/__md__/", (isset($_GET['md']) ? $_GET['md'] : 0) , $md);

  $ret = '';
  if(!$md) {$ret = "wpmsc det - md não especificado";}
  if(!$cod) {$ret = "wpmsc det - cod não especificado";}
  if($ret) {return $ret;exit;}
  $view = scmGetMdView($md,$cod);

  $ret = "";
  $ret .= '';
  $ret .= ' <form class="form-horizontal" action="" method="POST">';
  for ($i=0; $i < count($view['campo']); $i++) {
    $ret .= ' <div class="form-group">';
    // $ret .= '    <div class="italico f8 ">'.$view['campo'][$i]['fieldLabel'].'</div>';
    // $ret .= '   <label for="exampleInputEmail1">Email address</label></div>';
    $ret .= '   <div class="gray"><em>'.$view['campo'][$i]['fieldLabel'].'</em></div>';

    // $ret .= '    <div>';
    // $ret .= '      <p>'.$view['campo'][$i]['value'].'</p>';
    // $ret .= '    </div>';
    // $ret .= '    <div class="clear h10"></div>';
    // $ret .= '    <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email">';
    $ret .= '    <p class="lead text-uppercase"><strong>'.$view['campo'][$i]['value'].'</strong></p>';



    $ret .= ' </div>';
  }
  $ret .= ' </form>';

  return $ret;
}
add_shortcode("scm_det", "scm_det");

function scm_paginacao($atts, $content = null) {
  //if ( !is_user_logged_in() ) exit;
  extract(shortcode_atts(array(
    "md" => '0'
  ), $atts));

  if(!$md) {echo "paginação - $md nao especificado";exit;}

  $total = isset($_SESSION['md'.$md.'_total']) ? $_SESSION['md'.$md.'_total'] : 0;

  $start = isset($_GET['start']) ? $_GET['start'] : 0;
  $limit = isset($_GET['limit']) ? $_GET['limit'] : 20;

  $start_preview  = $start - $limit;
  $start_next   = $start + $limit;

  if($start_preview < 0 ) $start_preview = 0;
  if($start_next > $total ) $start_next = $start_next;

  $paginas = ceil($total / $limit);
  $pagina = 1;
  if(($start+1) > $limit){
    $pagina = ceil(($start+1) / $limit) ;
  }

  $tt = $start+$limit;
  $pagina_last = $paginas * $limit;

  $limit_end = $start + $limit;
  if($limit_end > $total) $limit_end = $total;

//----ini



  $cls = "";
  $csl_last = "";
  $csl_preview = "";


  if (($pagina_last+$limit) > $total) {
    $tt = $total;

    if(($start+$limit) >= $total) {
      $csl = "disabled";
      $csl_last = "disabled";
    }
    if(!$start){
      $csl_preview = "disabled";
    }
  }
  //----end


  // $qs = $_SERVER["QUERY_STRING"];
  $qs = $_SERVER["REQUEST_URI"];

  // $link       = $url.$_SERVER["QUERY_STRING"];
  //REQUEST_URI
  $link       = $url.$_SERVER["REQUEST_URI"];



  $ret = '';
  // $ret .= '<h4>Paginação</h4>';
  // $ret .= '<div></div>';
  $ret .= '<div class="pd10">';
  $ret .= '  <a class="btn btn-primary fleft '.$csl_preview.'" href="?start=0&limit='.$limit.'>"><span class="glyphicon glyphicon-fast-backward"></span></a>';
  $ret .= '  <a class="btn btn-primary fleft '.$csl_preview.'" href="?start='.$start_preview.'&limit='.$limit.'"><span class="glyphicon glyphicon-backward"></span></a>';
  $ret .= '  <a class="btn btn-primary fleft '.$csl_last.'" href="?start='.$start_next.'&limit='.$limit.'"><span class="glyphicon glyphicon-forward"></span></a>';
  $ret .= '  <a class="btn btn-primary fleft '.$csl_last.'" href="?start='.$pagina_last.'&limit='.$limit.'"><span class="glyphicon glyphicon-fast-forward"></span></a>';
  $ret .= '  <div class="w20 h30 fleft">  </div>';
  $ret .= '  <a class="btn btn-primary  fleft'.$csl_last.'" href=""><span class=" glyphicon glyphicon-refresh"></span></a>';

  $ret .= '';




  // $ret .= '  <div class="clear"></div>';
  // $ret .= '  <div class="hide_">';
  $ret .= '   <div class="fleft pd10">';
  $ret .= '     Total de registros: '.$total.' ';
  $ret .= '   </div>';

  $ret .= '   <div class="fleft pd10 ">';
  $ret .= '     Páginas : '.$paginas.' ';
  $ret .= '   </div>';
  $ret .= '   <div class="fleft pd10">';
  $ret .= '     Página atual: '.$pagina.' ';
  $ret .= '   </div>';
  $ret .= '   <div class="fleft pd10"> ';
  $ret .= '     Mostrando de: '.$start.' a '.($start + $limit).' ';
  $ret .= '   </div>';
  $ret .= '   <div class="fleft pd10"> ';
  $ret .= '     (registros por páginas: '.$limit.') ';
  $ret .= '   </div>';
  // $ret .= '  </div>';

  $ret .= '</div>';

  return $ret;

/**/

}
add_shortcode("scm_paginacao", "scm_paginacao");




function scm_update($atts, $content = null) {
  //if ( !is_user_logged_in() ) exit;
  extract(shortcode_atts(array(
    "cnn" => '',
    "md" => '0',
    "cod" => '0',
    "on_op" => '',
    "target_pos_update" => '?op=view&cod=__cod__',
    // "target_pos_update" => '?op=insert&pai=__pai__',
  ), $atts));

  $get_url_if_op = isset($_GET['op']) ? $_GET['op'] : '';
  if($on_op) {
    if($on_op=="empty"){
      if($get_url_if_op) return '';
    }else{
     if(!$get_url_if_op)  return '';
     if($get_url_if_op<>$on_op) return '';

    }
  }

  if($access){if(!scmIsAccess($access)) return '';}
  if($role){if(!scmIsRole($role)) return '';}
  

  $md = preg_replace("/__md__/", (isset($_GET['md']) ? $_GET['md'] : 0) , $md);
  $cod = preg_replace("/__cod__/", (isset($_GET['cod']) ? $_GET['cod'] : 0) , $cod);
  $target_pos_update = preg_replace("/__cod__/", (isset($_GET['cod']) ? $_GET['cod'] : 0) , $target_pos_update);
  $target_pos_update = preg_replace("/__md__/", (isset($_GET['md']) ? $_GET['md'] : 0) , $target_pos_update);
  $target_pos_update = preg_replace("/__pai__/", (isset($_GET['pai']) ? $_GET['pai'] : 0) , $target_pos_update);
  if(!$md) {$ret = "wpmsc view - md não especificado";}
  if(!$cod) {$ret = "wpmsc view - cod não especificado";}

  if(isset($_POST['duplique'])) {
    // echo 'duplique';
    if(!scmMdInsert($md, $_REQUEST )) {echo "ERRO AO INSERIR";exit;}
      echo '<script type="text/javascript">';
      echo '    window.location.href = "?" ;';
      echo '</script>';
    exit;
  }


  $ret = scmMdUpdate($md,$cod,$cnn);
  //return '--';

  if($target_pos_update){

    // echo '----:'.$target_pos_update.'---';
    // die('---x---');

    $ret = "";
    $url = $_SERVER["REDIRECT_URL"];
    //$url.$target_update
    $add_class = "wpmsc";
    if(substr($url,1,6)=='xxxwpmsc') {
      $add_class = "i".$md."update";
      $ret .= '
      <script type="text/javascript">
        // jQuery(function(){
        // alert("'.$url.$target_pos_update.'");
        //   jQuery(".i'.$md.'update").submit(function(e){
        //     e.preventDefault();
        //     url = jQuery(this).attr("action");
        //     alert(url);
        //     jQuery.ajax({
        //       method: "POST",
        //       url: url,
        //       data: jQuery(this).serialize()
        //     })
        //   // // alert(jQuery(this).serialize());
        //     // .done(function( html ) {
        //     //   // jQuery( "#aba_ctu" ).append( html );
        //     //   jQuery( "#aba_ctu div" ).remove();
        //     //   jQuery( "#aba_ctu" ).html("ok");
        //     // });
        //     return false;
        //   })
        // });
      </script>

      ';
    }else{
      echo '<script type="text/javascript">';
      echo '    window.location.href = "'.html_entity_decode($url.$target_pos_update).'";';
      echo '</script>';
    }
  }

  return '';
}
add_shortcode("scm_update", "scm_update");

function scm_nnew($atts, $content = null) {
  //if ( !is_user_logged_in() ) return "";
  extract(shortcode_atts(array(
    "md" => '0',
    "cod" => '0',
    "restrito" => 's',
    "target_insert" => '?op=insert&pai=__pai__',

    "label_submit" => 'Salvar',
    "title" => '',
    "access" => '',
    "role" => '',
    "on_op" => '',
    "un_show" => '',
    "class" => '',
    "access_manager" => ''
  ), $atts));

  if($access){if(!scmIsAccess($access)) return '';}
  if($role){if(!scmIsRole($role)) return '';}

  $get_url_if_op = isset($_GET['op']) ? $_GET['op'] : '';
  if($on_op) {
    if($on_op=="empty"){
      if($get_url_if_op) return '';
    }else{
      if(!$get_url_if_op)  return '';
      if($get_url_if_op<>$on_op) return '';
    }
  }

  $df['md'] =$md;
  $md = preg_replace("/__md__/", (isset($_GET['md']) ? $_GET['md'] : 0) , $md);

  // $cod = preg_replace("/__cod__/", $cod , $cod);
  $target_insert = preg_replace("/__md__/", (isset($_GET['md']) ? $_GET['md'] : 0) , $target_insert);
  $target_insert = preg_replace("/__cod__/", (isset($_GET['cod']) ? $_GET['cod'] : 0) , $target_insert);
  $target_insert = preg_replace("/__pai__/", (isset($_GET['pai']) ? $_GET['pai'] : 0) , $target_insert);

  $ret = '';
  if(!$md) {$ret = "wpmsc nnew - md não especificado";}
  // if(!$cod) {$ret = "wpmsc view - cod não especificado";}
  if($ret) {return $ret;exit;}

  // $nnew = scmGetMdEdit($md,$cod);
  $nnew = scmGetMdNovo($md);


  $ret = "";
  $url = $_SERVER["REDIRECT_URL"];
  $add_class = "wpmsc";
  // if(substr($url,1,6)=='xxxwpmsc') {
  //   $add_class = "wpmsc_form_ajax";
  //   $ret .= '
  //   <script type="text/javascript">
  //     jQuery(function(){
  //       //jQuery(".wpmsc_form_ajax").submit(e){
  //       jQuery(".wpmsc_form_ajax").submit(function(e){
  //         // alert(jQuery(this).attr("action"));
  //         jQuery.ajax({
  //           method: "POST",
  //           url: jQuery(this).attr("action"),
  //           data: jQuery(this).serialize()
  //         })
  //         .done(function( html ) {
  //           // jQuery( "#aba_ctu" ).append( html );
  //           jQuery( "#aba_ctu div" ).remove();
  //           jQuery( "#aba_ctu" ).html("ok");

  //         });
  //         return false;
  //       })
  //     });
  //   </script>
  //   ';
  // };

  // $ret .= '<div class="'.$class.'">';
  $ret .= $title;
  $ret .= '<form class="form-horizontal '.$add_class.' " action="'.$url.$target_insert.'" method="POST">';
  for ($i=0; $i < count($nnew['campo']); $i++) {
    $campo = $nnew['campo'][$i]['name'];
    $value = isset($_REQUEST[$campo]) ? $_REQUEST[$campo] : '';
    if(!$nnew['campo'][$i]['value']) $nnew['campo'][$i]['value'] = $value;
    if(($un_show) && (preg_match("/".$campo."/i", $un_show))){ }else{
      $ret .= ' <div class="dv_nnew_group" >';
      $ret .= '   <div class="col1">'.$nnew['campo'][$i]['fieldLabel'].'</div>';
      $ret .= '   <div class="col2" style="margin:0px;padding:0px;border: 0px solid gray;">';
      if($nnew['campo'][$i]['type']=='blob'){
        $ret .= ' <textarea class="form-control" autocomplete="off" id="'.$nnew['campo'][$i]['name'].'" name="'.$nnew['campo'][$i]['name'].'" >'.$nnew['campo'][$i]['value'].'</textarea>';  
      }else{
        $ret .= ' <input type="text" style="" name="'.$nnew['campo'][$i]['name'].'" id="'.$nnew['campo'][$i]['name'].'" class="form-control" value="'.$nnew['campo'][$i]['value'].'" title="" autocomplete="off">';  
      }
      $ret .= '   </div>';
      $ret .= ' </div>';
    }
  }
  


  $ret .= ' <div class="dv_nnew_group">';
  $ret .= '   <div class="col1"></div>';
  $ret .= '   <div class="col2">';
  $ret .= '     <button type="submit" class="btn btn-primary" style="padding: 10px 60px;">'.$label_submit.'</button>';
  $ret .= '   </div>';
  $ret .= ' </div>';

  $ret .= '</form>';
  $ret .= '</div>';

  return $ret;
}
add_shortcode("scm_nnew", "scm_nnew");

// include("assets/wpmsc_functions.php");
// include("assets/scm_crud.php");

function scm_list($atts, $content = null) {
  //if ( !is_user_logged_in() ) return "";
  // return '';
  extract(shortcode_atts(array(
    "md" => '0',
    "manut" => '0',
    "criterio" => '',
    "criterio2" => '',
    "style" => '',
    "class" => '',
    "on_op" => '',
    "title" => '',
    "access" => '',
    "role" => '',
    "un_show" => '',
    "config" => '',
    "join" => '',
    "inner" => '',
    "cnn" => '',
    "die_col" => '',
    "col_replace" => '',
    "die_sql" => '' ,
    "col_url" => '',
    "col_x0" => '',
    "msc_col_add" => ''
  ), $atts));
//msc_col_add='depois_de|antes_de,coluna_name,label'
  if($access){if(!scmIsAccess($access)) return '';}
  if($role){ if(!scmIsRole($role)) return '';}

  $get_url_if_op = isset($_GET['op']) ? $_GET['op'] : '';
  if($on_op) {
    if($on_op=="empty"){
      if($get_url_if_op) return '';
    }else{
     if(!$get_url_if_op)  return '';
     if($get_url_if_op<>$on_op) return '';

    }
  }

  $cfg = array();

  $busca = isset($_GET['busca']) ? $_GET['busca'] : '';
  if($busca){
    if(is_numeric($busca)){
      do_shortcode('[scm_buscando]');
      exit;
    }

  }
//site_url()
// $col_url = preg_replace("/__urldosite__/",'xxxxxx' , $col_url);
// $col_url = preg_replace("/__md__/", (isset($_GET['md']) ? $_GET['md'] : 0) , $md);


// ---'.bloginfo('url').'---



  $get_url_if_op = isset($_GET['op']) ? $_GET['op'] : '';

  if($on_op) {
    if($on_op=="empty"){
      if($get_url_if_op) return '';
    }else{

    }
  //   if(!$get_url_if_op)  return '';
  //   if($get_url_if_op<>$on_op) return '';
  }
  $df = array();
  //msc_col_add='depois_de|antes_de,coluna_name,label'
  $df['msc_col_add'] = $msc_col_add;

  $df['md'] = $md;
  $md = preg_replace("/__md__/", (isset($_GET['md']) ? $_GET['md'] : 0) , $md);

  $col  = scmGetMdCol($md,$cnn,$df);
  // print_r($col);
  if($col_replace){
    $resplace = explode(",", $col_replace);
    foreach ($resplace as $keyc => $valuec) {
      $arrray = explode(":", $valuec);
      foreach ($col as $key => $value) {
        if ($value['dataIndex']==$arrray[0]) {
          $col[$key]['dataIndex'] = $arrray[1];
          $col[$key]['filter_type'] = 'string';
        }
      }
    }
    // $arrray = explode(":", $col_replace);
    // foreach ($col as $key => $value) {
      // if ($value['dataIndex']==$arrray[0]) {
        // echo $col[$key] = "--".$value['dataIndex']."--";
        // $col[$key]['dataIndex'] = $arrray[1];
        // $col[$key]['filter_type'] = 'string';
      // }
    // }
  }


  if($die_col){
    echo "<pre>";
    print_r($col);
    echo "<pre>";
    return '';
  }
// echo '<pre>';
// echo get_bloginfo('url');
// echo '</pre>';
// die();

// $site_url = site_url();
// $site_url = explode(":", $site_url);
// $site_url = $site_url[1];

  // $col_url = preg_replace("/__site_url__/",$site_url , $col_url);

  if(!count($col)) return '';
  $modulo_conf  = scmGetModuloConf($md, $cnn);

  $tabela         = $modulo_conf['tabela'];
  // echo $tabela;
  $campo_codigo   = $tabela."_codigo";
  $fields         = scmGetFields($md, $cnn,$df);


  $df['join'] = $join;
  $df['die_col'] = $die_col;
  $df['col_replace'] = $col_replace;
  $df['die_sql'] = $die_sql;
  $df['inner'] = $inner;

  $criterio = preg_replace("/__cod__/", (isset($_GET['cod']) ? $_GET['cod'] : 0) , $criterio);
  $criterio = preg_replace("/__pai__/", (isset($_GET['pai']) ? $_GET['pai'] : 0) , $criterio);
  $criterio = preg_replace("/__prefix__/", scmPrefix(false) , $criterio);
  $criterio = preg_replace("/__pessoa_by_user__/", get_user_meta( get_current_user_id(), "pessoa_by_user", true ) , $criterio);
 
  // echo "<!--criterio: ".$criterio."-->";

  $df['criterio'] = base64_encode($criterio);
  $data = scmGetMdRows($md, $fields, $col, $df, $cnn);
  // echo '<pre>';
  // print_r($data);
  // echo '<pre>';
  // dir();
  // echo '<br><br><br><br>---';
  // echo $cnn;
  // die();

  // echo '--<h1>--'.$data['db_host'].'--</h1>--';

  if(isset($data['msg'])){
    if($data['msg']) return $data['msg'];
  }

  $_SESSION['md'.$md.'_total'] = $data['total'];

  $manut = $modulo_conf['show_cp_option'];
  if( $on_op) $manut = false;

  //paginacai -ini

  $ret = "";
  $url = $_SERVER["REDIRECT_URL"].'?';
  $add_class = "wpmsc";
  if(substr($url,1,6)=='xxxwpmsc') {
    $add_class = "wpmsc_link_ajax";
  };

//gambiarra pra consertar  a paginação quando nginxs
  $q = isset($_GET["q"]) ? $_GET["q"] : '';
  if($q){
    $link       = $q.'?';//$url.$_SERVER["REQUEST_URI"];
  } else {
    $link       = $url.$_SERVER["QUERY_STRING"];
  }
  // $q = isset($_REQUEST["q"]) ? $_REQUEST["q"] : '';
  // if($q){
  //   $link = preg_replace("/".$q."/", '?', $link);
  // }

  // echo '---'.$link.'---';
  // echo '<br>';
  // if (is_user_logged_in()){
  //   phpinfo();
  //   exit;
  // }

  $start      = isset($_GET['start']) ? $_GET['start'] : 0;//0; //isset($_GET['start']) ? $_GET['start'] : 10//
  // $limit      = $modulo_conf['limit'];//20; //por paginas ou limit
  $limit      = isset($_GET['limit']) ? $_GET['limit'] : $modulo_conf['limit'];//20; //por paginas ou limit
  $total      = $data['total'];//149;//$data['total']
  $supertotal = 0;
  $total2 = $total - $limit;
  // $supertotal = (ceil($total / $limit) * $limit) ;//100; // ceil($total / $limit)
  // die($supertotal);/

  $rfirst     = scmAddParam($link,'start',"0");//0;//scmRemoveParam($link, 'start');//
  $rprevious  = scmAddParam($link,'start',($start-$limit < 0 ? 0 : $start-$limit));//0;//scmAddParam($link,'start',10)
  // $rnext      = scmAddParam($link,'start',($start+$limit > $supertotal ? ($supertotal - $limit) : ($start+$limit)));//10;//scmAddParam($link,'start',($start+10));//
  $rnext      = scmAddParam($link,'start',$start+$limit) ;
  // $rlast      = scmAddParam($link,'start',($supertotal - $limit));// $supertotal - $limit;//90;//scmAddParam($link,'start',($supertotal-10))
  $rlast      = scmAddParam($link,'start',($total2));// $supertotal - $limit;//90;//scmAddParam($link,'start',($supertotal-10))

  $limit_10   = scmAddParam($link,'limit',"10");
  $limit_25   = scmAddParam($link,'limit',"25");
  $limit_50   = scmAddParam($link,'limit',"50");
  $limit_100  = scmAddParam($link,'limit',"100");
  echo '<hr>';
  echo '<div style="clear:toth;"></div>';
  $ret = '<div style="clear:toth;"></div>';
  
  $ret .= $title;

  $ret .= '  <div class="" style="overflow-y:auto;border:solid 0px gray;">';
  $ret .= '<table style="'.$style.'" class="table table-condensed" >';

  if(($config) && (preg_match("/no_col_title/i", $config))){
  } else{

    $ret .= '<thead>';
    $ret .= '<tr>';
    $ret .= '<th style=""></th>';
    for ($i=0; $i < count($col); $i++){
      if($col[$i]['ctr_list'] == 'label'){
        //if ($col[$i]['dataIndex']=="i8200_data"){
        if(($un_show) && (preg_match("/".$col[$i]['dataIndex']."/i", $un_show))){
          //if(($un_show) && (preg_match("/i".$campo."/i", $un_show))){
          //$ret .= '<th></th>';
        } else {
          $ret .= '<th style="">'.$col[$i]['text'].'</th>';
        }
      }
    }
    $ret .= '</tr>';
    $ret .= '</thead>';
  }





  $ret .= '<tbody>';
  for ($i=0; $i < count($data['row']); $i++){
    $ret .= '<tr class="wpmsc_tr">';
    




///---col_url

    if ($col_url) {
      // echo '<br>---'.$col_url.'---<br>';
      $t566_codigo_name = $col[$i]['codigo_name'];//]$data['row'][$i]['dataIndex'];
      $t566_v_codigo_name = $data['row'][$i][$t566_codigo_name];
      $col_url = preg_replace("/__tcod__/i", $t566_v_codigo_name, $col_url);
      $col_url = preg_replace("/__pai__/i", (isset($_GET['pai']) ? $_GET['pai'] : 0), $col_url);
      $col_url = preg_replace("/__cod__/i", (isset($_GET['cod']) ? $_GET['cod'] : 0), $col_url);
      // echo '---'.$col_url.'---';
      $col_url_arr = explode(",", $col_url);


      foreach ($col_url_arr as $ckey => $cvalue) {
        $col_url_arr_item = explode(":", $cvalue);
        foreach ($col as $key => $value) {
          // echo '<pre>';
          // print_r($value);
          // print_r($col_url_arr_item);
          // echo '</pre>';
          // echo $col_url_arr_item[0];


          if ($value['dataIndex']==$col_url_arr_item[0]) {
            // echo "<br>----------<br>";
            $tcampo = $value['dataIndex'];
            $tvalue = $col_url_arr_item[1];
            $tvalue = preg_replace("/__this__/i", $data['row'][$i][$tcampo], $tvalue);
            foreach ($col as $ttkey => $ttvalue) {
              // echo "<br>---".$ttvalue['name']."---<br>";
              // echo '<pre>';
              // print_r($ttvalue);
              // echo '</pre>';

              $tttcampo = $ttvalue['dataIndex'];
              $tttvalue = $data['row'][$i][$tttcampo];
              if (preg_match("/__".$tttcampo."__/", $tvalue)) {
                $tvalue = preg_replace("/__".$tttcampo."__/", $data['row'][$i][$tttcampo],$tvalue);
              }
            }
            $data['row'][$i][$tcampo] = $tvalue;
          }
        }
        foreach ($col as $key => $value) {
          $t567 = $col_url_arr_item[1];
          $t566_c = $col[$i]['dataIndex'];//]$data['row'][$i]['dataIndex'];
          $t566_v = $data['row'][$i][$t566_c];
          $t566_codigo_name = $col[$i]['codigo_name'];//]$data['row'][$i]['dataIndex'];
          $t566_v_codigo_name = $data['row'][$i][$t566_codigo_name];
          foreach ($col as $key => $value) {
            if (preg_match("/__".$value['codigo_name']."__/", $t567)) {
              $tvalue = isset($data['row'][$i][$t567]) ? $data['row'][$i][$t567] : '';//Notice: Undefined index: __this__ in
              $t567_c = $value['codigo_name'];
              $t567_v = strip_tags($data['row'][$i][$t567_c]);
              $t567 = preg_replace("/__".$value['codigo_name']."__/", $t567_v,$t567);
              $t567 = preg_replace("/__this__/i", $data['row'][$i][$tcampo], $t567);
              $t567 = preg_replace("/__pai__/i", (isset($_GET['pai']) ? $_GET['pai'] : 0), $t567);
              $data['row'][$i][$tcampo] = $t567;
            }
          }
        }
      }
    }
/*
*/




    if ($col_x0) {
      $t566_codigo_name = $col[$i]['codigo_name'];//]$data['row'][$i]['dataIndex'];
      $t566_v_codigo_name = $data['row'][$i][$t566_codigo_name];
      $col_x0_arr = explode(",", $col_x0);
      $ret .="\n<!--".$col_x0."-->";
      foreach ($col as $key => $value) {
        $t566_campo = $value['dataIndex'];
        $t566_value = $data['row'][$i][$t566_campo];
        $ret .="\n<!--".$value['dataIndex']."-->";
        $col_x0 = preg_replace("/__".$value['dataIndex']."__/", $t566_value, $col_x0);

// echo "<!--";
// echo ($t566_campo);
// echo '--';
// echo ($t566_value);
// echo "-->";
      }
    }

    $ret .= '<td style="white-space: nowrap;">';
    if($col_x0){
      $col_x0a[$i] = $col_x0;
      $t566_codigo_name = $col[0]['codigo_name'];//]$data['row'][$i]['dataIndex'];
      $t566_v_codigo_name = $data['row'][$i][$t566_codigo_name];

      $t565 = $col_x0a[$i];
      $t565 = preg_replace("/__tcod__/i", $t566_v_codigo_name, $t565);
      $t565 = preg_replace("/__pai__/i", (isset($_GET['pai']) ? $_GET['pai'] : 0), $t565);
      $t565 = preg_replace("/__cod__/i", (isset($_GET['cod']) ? $_GET['cod'] : 0), $t565);
      $col_x0a[$i] = $t565;
    }
    $ret .= isset($col_x0a[$i]) ? $col_x0a[$i] : '';//col_x0a
    $ret .= '</td>';
    for ($c=0; $c < count($col); $c++) {  $campo = $col[$c]['dataIndex'];
     
      if($col[$c]['ctr_list'] == 'label'){
        if(($un_show) && (preg_match("/".$campo."/i", $un_show))){
          //$ret .= '<td style="border:1px solid;"></td>';
        }else{
          if(($config) && (preg_match("/no_cel_url/i", $config))){
            $data['row'][$i][$campo] = strip_tags($data['row'][$i][$campo]);//'--=--';
          }
          $ret .= '<td class="irow-sit-" style="white-space: nowrap;">'.$data['row'][$i][$campo].'</td>';
        }
      }
    }
    $ret .= '</tr>';
  }
  $ret .= '</tbody>';
  $ret .= '</table>';
  // $ret .= '</div>---=---';
  $ret .= '</div>';
  //show paginacao - ini
  if(($config) && (preg_match("/no_count_reg/i", $config))){
  } else {
    $ret .= '<div style="text-align:center"> ';
    // $ret .= '<small>'.$total.' registro(s).</small>';
    $ret .= '<big>'.$total.' registro(s).</big>';
    // $ret .= ' '.$total.' registro(s).';
    $ret .= '</div>';
  }
  //show paginacao - end

  // return $ret;
  //show total  - ini
  if(($config) && (preg_match("/no_sum_col/i", $config))){
  } else {
    if($total > $limit){
      $ret .= '<div style="text-align:center"> ';
      $ret .= '<a href="'.$rfirst.'" class="btn btn-link '.$add_class.'">&nbsp;&lt;&lt;&nbsp;</a>';
      $ret .= '<a href="'.$rprevious.'" class="btn btn-link '.$add_class.'">&nbsp;&lt;&nbsp;</a>';
      $ret .= '<small>&nbsp;'.$start.' a '.((($start + $limit) > $total) ? $total : ($start + $limit)).'&nbsp;</small>';
      $ret .= '<a href="'.$rnext.'" class="btn btn-link '.$add_class.'">&nbsp;&gt;&nbsp;</a>';
      $ret .= '<a href="'.$rlast.'" class="btn btn-link '.$add_class.'">&nbsp;&gt;&gt;&nbsp;</a>';
      $ret .= '</div>';
    }
  }
  //show total  - end

  //return $ret;

  if(($config) && (preg_match("/no_paging/i", $config))){
    } else {
  if($total > $limit){
    $ret .= '<div style="text-align:center"> ';
    $ret .= 'limite ';
    $ret .= '<a href="'.$limit_10.'" class="btn btn-link '.$add_class.'">&nbsp;10&nbsp;</a>';
    $ret .= '<a href="'.$limit_25.'" class="btn btn-link '.$add_class.'">&nbsp;25&nbsp;</a>';
    $ret .= '<a href="'.$limit_50.'" class="btn btn-link '.$add_class.'">&nbsp;50&nbsp;</a>';
    $ret .= '<a href="'.$limit_100.'" class="btn btn-link '.$add_class.'">&nbsp;100&nbsp;</a>';
    $ret .= ' por pagina ';
    $ret .= '</div>';
  }
}
  // $ret .= '<small>'.$data['total'].' registro(s). mostrando 10 por pagina. </small>';
  // $ret .= '<div style="text-align:center;"><small>primeira - anterior - proxima - última </small></div>';

  // $ret .= '<small>'.$data['total'].' registro(s). mostrando 10 por pagina. primeira - anterior - proxima - última</small>';

  $ret .= '
    <script type="text/javascript">
      jQuery(function(){
        jQuery(".wpmsc_link_ajax").on("click",function(e){
          var url = jQuery(this).attr("href");
          // alert(url);
          jQuery( "#aba_ctu" ).load(url);
          return false;
        })
      });
    </script>
  ';
  return $ret;
}
add_shortcode("scm_list", "scm_list");





function scm_list_single($atts, $content = null) {
# necessita add na function ára ativar shortcode nos widget add_filter('widget_text', 'do_shortcode');
  //if ( !is_user_logged_in() ) return "";
  //[scm_list md="1030"]
  extract(shortcode_atts(array(
    "md" => '0'
  ), $atts));


  // echo '<pre>';
  // print_r($md);
  // echo '</pre>';

  $df['md'] =$md;
  $md = preg_replace("/__md__/", (isset($_GET['md']) ? $_GET['md'] : 0) , $md);

  $col      = scmGetMdCol($md);
  $modulo_conf  = scmGetModuloConf($md);
  $tabela     = $modulo_conf['tabela'];
  $campo_codigo   = $tabela."_codigo";
  // print_r($col);
  // if($col['msg']) return $col['msg'];

  // return '--';
  $fields = scmGetFields($md);
  $data = scmGetMdRows($md, $fields,$col);
  if($data['msg']) return $data['msg'];
  // echo "<pre>";
  // print_r($modulo_conf);
  // echo $campo_codigo;
  // echo "</pre>";

  // die();

  // die($data['total']);
  // print_r($col);
  $_SESSION['md'.$md.'_total'] = $data['total'];
  $ret = '';
  $ret .= '<div id="md'.$md.'ilist" class="pd10" style="width:100%">';
  $ret .= '  <div class="" style="overflow-y:auto">';
  $ret .= '    <table class="table table-condensed" data-total="'.$data['total'].'">';
  $ret .= '    <tbody>';
  for ($i=0; $i < count($data['row']); $i++){
    $ret .= '      <tr class="wpmsc_tr">';
    for ($c=0; $c < count($col); $c++) {  $campo = $col[$c]['dataIndex'];
      $cls = "";
      // if(!$c) $cls = "hide";
      $ret .= '        <td class="'.$cls.'">'.$data['row'][$i][$campo].'</td>';
    }
    $ret .= '      </tr>';
  }
  $ret .= '    </tbody>';
  $ret .= '  </table>';
  $ret .= '</div>';
  return $ret;
}
add_shortcode("scm_list_single", "scm_list_single");




function scm_edit($atts, $content = null) {
  // return "--=--";
  //if ( !is_user_logged_in() ) exit;
  extract(shortcode_atts(array(
    "md" => '0',
    "cnn" => '',
    "cod" => '0',
    "target_update" => '?op=update&cod=__cod__&pai=__pai__',
    "on_op" => '',
    "access" => '',
    "role" => '',
    "un_show" => ''
  ), $atts));

  if($access){if(!scmIsAccess($access)) return '';}
  if($role){ if(!scmIsRole($role)) return '';}

  $get_url_if_op = isset($_GET['op']) ? $_GET['op'] : '';
  if($on_op) {
    if($on_op=="empty"){
      if($get_url_if_op) return '';
    }else{
      if(!$get_url_if_op)  return '';
      if($get_url_if_op<>$on_op) return '';
    }
  }

  $md = preg_replace("/__md__/", (isset($_GET['md']) ? $_GET['md'] : 0) , $md);
  $cod = preg_replace("/__cod__/", (isset($_GET['cod']) ? $_GET['cod'] : 0) , $cod);
  $target_update = preg_replace("/__cod__/", (isset($_GET['cod']) ? $_GET['cod'] : 0) , $target_update);
  $target_update = preg_replace("/__md__/", (isset($_GET['md']) ? $_GET['md'] : 0) , $target_update);
  $target_update = preg_replace("/__pai__/", (isset($_GET['pai']) ? $_GET['pai'] : 0) , $target_update);
  $ret = '';
  if(!$md) {$ret = "wpmsc view - md não especificado";}
  if(!$cod) {$ret = "wpmsc view - cod não especificado";}
  if($ret) {return $ret;exit;}
  $edit = scmGetMdEdit($md,$cod,$cnn);
  $ret = "";
  $url = $_SERVER["REDIRECT_URL"];
  $add_class = "wpmsc";
  if(substr($url,1,6)=='xxxwpmsc') {
    $add_class = "i".$md."update";
    // $ret .= '
    // <script type="text/javascript">
    //   jQuery(function(){
    //     jQuery(".i'.$md.'update").submit(function(e){
    //       e.preventDefault();
    //       url = jQuery(this).attr("action");
    //       // alert(url);
    //       jQuery.ajax({
    //         method: "POST",
    //         url: url,
    //         data: jQuery(this).serialize()
    //       })

    //       .done(function( html ) {
    //         jQuery("#aba_ctu").load("'.$url.'?op=view&cod='.$cod.'");
    //       });
    //       return false;
    //     })
    //   });
    // </script>
    // ';
  };

  $ttop = isset($_REQUEST['op']) ? $_REQUEST['op'] : '';

  if($ttop=='duplicar'){
    $ret .= '
    <script type="text/javascript">
    jQuery(function(){
      jQuery("#fmdsubmit").css("visibility","hidden");
      jQuery("#fmdsubmit").remove();
      jQuery("#fmdduplique").css("visibility","visible");
      // alert(333);
    });
    </script>
    ';
  }


/*
  for ($i=0; $i < count($nnew['campo']); $i++) {
    $campo = $nnew['campo'][$i]['name'];
    $value = isset($_REQUEST[$campo]) ? $_REQUEST[$campo] : '';
    $ret .= ' <div class="form-group" style="margin:0px;padding:0px;" >';
    $ret .= '   <label class="span3 col-sm-3 col-md-3 control-label ">'.$nnew['campo'][$i]['fieldLabel'].'</label>';
    $ret .= '   <div class="span9 col-sm-9 col-md-9" style="margin:0px;padding:0px;">';
    $ret .= '     <input type="text" style="text-transform:uppercase;margin:0px;padding:0px;height:18px;padding:10px;width:90%;" name="'.$nnew['campo'][$i]['name'].'" id="'.$nnew['campo'][$i]['name'].'" class="form-control" value="'.$value.'" title="" autocomplete="off">';
    $ret .= '   </div>';
    $ret .= ' </div>';

  }

*/
// echo '<pre>';
// print_r($edit);
// echo '</pre>';
  $ret .= '';
  $ret .= ' <form class="form-horizontal '.$add_class.'" action="'.$url.$target_update.'" method="POST">';
  for ($i=0; $i < count($edit['campo']); $i++) {
    // xxx_xxx
    if(($un_show) && (preg_match("/".$edit['campo'][$i]['name']."/i", $un_show))){

    } else {
      $ret .= ' <div class="form-group pd0" style="margin-bottom:2px;padding-right:10px;" >';
      $ret .= '   <label class="span3 col-sm-3 col-md-3 control-label ">'.$edit['campo'][$i]['fieldLabel'].'</label>';
      $ret .= '   <div class="span9 col-sm-9 col-md-9" style="min-height:30px">';
      // $ret .= '     <input type="text" style="text-transform:uppercase;margin:0px;padding:0px;height:18px;padding:10px;width:90%;" name="'.$edit['campo'][$i]['name'].'" id="'.$edit['campo'][$i]['name'].'" class="form-control" value="'.$edit['campo'][$i]['value'].'" title="" autocomplete="off">';

      
      if($edit['campo'][$i]['type']=='blob'){
        $ret .= ' <textarea class="form-control" autocomplete="off" id="'.$edit['campo'][$i]['name'].'" name="'.$edit['campo'][$i]['name'].'" >'.$edit['campo'][$i]['value'].'</textarea>';  
      }else{
        $ret .= '     <input type="text" style="" name="'.$edit['campo'][$i]['name'].'" id="'.$edit['campo'][$i]['name'].'" class="form-control" value="'.$edit['campo'][$i]['value'].'" title="" autocomplete="off">';  
      }
      


      $ret .= '   </div>';
      $ret .= '   <div style="clear:both;"></div>';
      $ret .= ' </div>';
    }
  }
  
  $ret .= ' <div style="height:15px;"></div>';

  $ret .= ' <div class="form-groupx pd0x" style="margin-bottom:2px;padding-right:10px;" >';
  // $ret .= '    <div class="col-sm-3">'.$edit['campo'][$i]['fieldLabel'].'</div>';
  $ret .= '   <div class="span3 col-sm-3"> </div>';
  $ret .= '   <div class="span9 col-sm-9">';
  $ret .= '     <button id="fmdsubmit" type="submit" class="btn btn-primary">Atualizar</button> ';
  $ret .= '     <button id="fmdduplique" type="submit" name="duplique" class="btn btn-primary" style="visibility: hidden;">Duplicar</button> ';
  $ret .= '   </div>';
  $ret .= '   <div style="clear:both;"></div>';
  $ret .= ' </div>';
  // $ret .= '  <input type="hidden" name="md" value="'.$md.'" >';
  // $ret .= '  <input type="hidden" name="op" value="edit" >';
  $ret .= ' </form>';

  return $ret;
}
add_shortcode("scm_edit", "scm_edit");




function scm_duplique($atts, $content = null) {
  // return "--=--";
  //if ( !is_user_logged_in() ) exit;
  extract(shortcode_atts(array(
    "cnn" => '',
    "md" => '0',
    "cod" => '0',
    "target_update" => '',
    "target_insert" => '?op=insert',
    "access" => '',
    "role" => ''
  ), $atts));

  if($access){if(!scmIsAccess($access)) return '';}
  if($role){if(!scmIsRole($role)) return '';}

  $md = preg_replace("/__md__/", (isset($_GET['md']) ? $_GET['md'] : 0) , $md);
  $cod = preg_replace("/__cod__/", (isset($_GET['cod']) ? $_GET['cod'] : 0) , $cod);
  $target_update = preg_replace("/__cod__/", (isset($_GET['cod']) ? $_GET['cod'] : 0) , $target_update);
  $target_update = preg_replace("/__md__/", (isset($_GET['md']) ? $_GET['md'] : 0) , $target_update);
  $target_update = preg_replace("/__pai__/", (isset($_GET['pai']) ? $_GET['pai'] : 0) , $target_update);

  $target_insert = preg_replace("/__cod__/", (isset($_GET['cod']) ? $_GET['cod'] : 0) , $target_insert);
  $target_insert = preg_replace("/__md__/", (isset($_GET['md']) ? $_GET['md'] : 0) , $target_insert);
  $target_insert = preg_replace("/__pai__/", (isset($_GET['pai']) ? $_GET['pai'] : 0) , $target_insert);

  $ret = '';
  if(!$md) {$ret = "wpmsc view - md não especificado";}
  if(!$cod) {$ret = "wpmsc view - cod não especificado";}
  if($ret) {return $ret;exit;}
  $edit = scmGetMdEdit($md,$cod,$cnn);
  $ret = "";
  $url = $_SERVER["REDIRECT_URL"];
  $add_class = "wpmsc";
  if(substr($url,1,6)=='xxxwpmsc') {
    $add_class = "i".$md."update";
    $ret .= '
    <script type="text/javascript">
      jQuery(function(){
        jQuery(".i'.$md.'update").submit(function(e){
          e.preventDefault();
          url = jQuery(this).attr("action");
          // alert(url);
          jQuery.ajax({
            method: "POST",
            url: url,
            data: jQuery(this).serialize()
          })

          .done(function( html ) {
            jQuery("#aba_ctu").load("'.$url.'?op=view&cod='.$cod.'");
          });
          return false;
        })
      });
    </script>
    ';
  };

  $ttop = isset($_REQUEST['op']) ? $_REQUEST['op'] : '';

  if($ttop=='duplicar'){
    $ret .= '
    <script type="text/javascript">
    jQuery(function(){
      jQuery("#fmdsubmit").css("visibility","hidden");
      jQuery("#fmdsubmit").remove();
      jQuery("#fmdduplique").css("visibility","visible");
      // alert(333);
    });
    </script>
    ';
  }





  $ret .= '';
  $ret .= ' <form class="form-horizontal '.$add_class.'" action="'.$url.$target_insert.'" method="POST">';
  for ($i=0; $i < count($edit['campo']); $i++) {
    $ret .= ' <div class="form-group pd0" style="margin-bottom:2px;padding-right:10px;" >';
    $ret .= '   <label class="col-sm-3 control-label italico f12">'.$edit['campo'][$i]['fieldLabel'].'</label>';
    $ret .= '   <div class="col-sm-9 bgw colorb" style="min-height:30px">';
    $ret .= '     <input type="text" style="text-transform:uppercase;" name="'.$edit['campo'][$i]['name'].'" id="'.$edit['campo'][$i]['name'].'" class="form-control" value="'.$edit['campo'][$i]['value'].'" title="" autocomplete="off">';
    $ret .= '   </div>';
    $ret .= ' </div>';

  }
  $ret .= ' <div class="h20" ></div>';
  $ret .= ' <div class="form-group pd0" style="margin-bottom:2px;padding-right:10px;" >';
  // $ret .= '    <div class="col-sm-3">'.$edit['campo'][$i]['fieldLabel'].'</div>';
  $ret .= '   <div class="col-sm-3"></div>';
  //$ret .= '   <button id="fmdsubmit" type="submit" class="btn btn-primary">Atualizar</button> ';
  $ret .= '   <button id="fmdduplique" type="submit" name="duplique" class="btn btn-primary" style="">Duplicar</button> ';

  $ret .= ' </div>';
  // $ret .= '  <input type="hidden" name="md" value="'.$md.'" >';
  // $ret .= '  <input type="hidden" name="op" value="edit" >';
  $ret .= ' </form>';

  return $ret;
}
add_shortcode("scm_duplique", "scm_duplique");


function scm_aba($atts, $content = null) {
  extract(shortcode_atts(array(
    "label" => 'LABEL',
    "md" => '0',
    "op" => 'ilist',
    "cod" => '',
    "on_op" => 'view',
    "criterio" => ''
  ), $atts));

  // $ret = '<hr>';
  // $ret .= $label;
  // $ret .= '<hr>';
  // return $ret;

  $vai = true;
  if($on_op) {
    $vai = false;
    $t_op = isset($_GET['op']) ? $_GET['op'] : 'empty';
    if(($on_op=='empty') && ($t_op=='empty')) $vai = true;
    if(($on_op=='view') && ($t_op=='view')) $vai = true;
    if(($on_op=='edit') && ($t_op=='edit')) $vai = true;
    if(($on_op=='deletar') && ($t_op=='deletar')) $vai = true;
    if(($on_op=='det') && ($t_op=='det')) $vai = true;
  }
  if(!$vai) return '';

  $criterio = preg_replace("/__cod__/", (isset($_GET['cod']) ? $_GET['cod'] : 0) , $criterio);
  $criterio = preg_replace("/__pai__/", (isset($_GET['pai']) ? $_GET['pai'] : 0) , $criterio);


  $labels = explode(',', $label);
  $abas = count($labels);

  $mds  = explode(',', $md);
  $ops  = explode(',', $op);

  $criterios  = explode(',', $criterio);

  $aba = isset($_GET['aba']) ? $_GET['aba'] : 0;
  $qs     = $_SERVER["REQUEST_URI"];

  $ret  = '';
  $ret .= '<ul class="nav nav-tabs" role="tablist">';
  for ($i=0; $i < $abas; $i++) {
    $ret .= '  <li role="presentation" ><a class="scm_aba" aria-controls="home" role="tab" data-toggle="tab" data-md='.$mds[$i].' href="/wpmsc/'.$mds[$i].'/?'.$criterios[$i].'">'.$labels[$i].'</a></li>';
  }
  $ret .= '</ul>';
  $ret .= '<div id="aba_ctu">--aba--</div>';
  $ret .= '
  <script type="text/javascript">
    jQuery(function(){
      jQuery(".scm_aba").on("click",function(e){
        e.preventDefault();
          jQuery( "#aba_ctu div" ).remove() ;
          jQuery( "#aba_ctu" ).load( jQuery(this).attr("href") );
          // jQuery( "#aba_ctu" ).html( jQuery(this).attr("href") );
      });
    });
  </script>
  ';
  return $ret;

}
add_shortcode("scm_aba", "scm_aba");









function scm_busca($atts, $content = null) {
  extract(shortcode_atts(array(
    "md" => 0,
    "op" => '',
    "cod" => 0,
    "target" => '',
    "target_det" => '',
    "on_op" => '',
    "access" => '',
    "role" => '',
    "style" => '',
    "class" => '',
    "placeholder" => 'BUSCA'
  ), $atts));


  if($access){if(!scmIsAccess($access)) return '';}
  if($role){ if(!scmIsRole($role)) return '';}
  
  $vai = true;
  if($on_op) {
    $vai = false;
    $t_op = isset($_GET['op']) ? $_GET['op'] : 'empty';
    if(($on_op=='empty') && ($t_op=='empty')) $vai = true;
  }
  if(!$vai) return '';

  $busca = isset($_GET['busca']) ? $_GET['busca'] : '';


  $ret = "";
  $url = $_SERVER["REDIRECT_URL"];
  $add_class = "wpmsc";
  // if(substr($url,1,6)=='xxxwpmsc') {
  //   $add_class = "wpmsc_form_ajax";
  //   $ret .= '
  //   <script type="text/javascript">
  //     jQuery(function(){
  //       jQuery(".wpmsc_form_ajax").submit(function(e){
  //         // alert(jQuery(this).attr("action"));
  //         var busca = jQuery(this).attr("action")+"?"+jQuery(this).serialize();
  //         // alert(jQuery(this).serialize());
  //         jQuery( "#aba_ctu" ).load(busca);
  //         // alert(busca);

  //         // jQuery.ajax({
  //         //   method: "GET",
  //         //   url: jQuery(this).attr("action"),
  //         //   data: jQuery(this).serialize()
  //         // })
  //         // .done(function( html ) {
  //         //   // jQuery( "#aba_ctu div" ).remove();
  //         //   // jQuery( "#aba_ctu" ).html("ok");
  //         //   // jQuery( "#aba_ctu" ).html(html);
  //         // });
  //         return false;
  //       })
  //     });
  //   </script>
  //   ';
  // };

  // $ret .= '<form action="'.$url.$target.'" method="GET" class=" '.$add_class.'" role="form">';
  $ret .= '<form action="'.$target.'" method="GET" class="'.$class.'" style="'.$style.'">';
  $ret .= '  <input type="text" class="form-control " value="'.$busca.'" name="busca" placeholder="'.$placeholder.'" style="text-align:center">';
  // $ret .= '  <input type="hidden" name="op" value="ilist">';
  $ret .= '</form>';
  return $ret;
}
add_shortcode("scm_busca", "scm_busca");


function scm_busca_redir($atts, $content = null) {
  extract(shortcode_atts(array(
    "tarmscGetList" => '../listagem/',
    "target_det" => '../view/'
  ), $atts));

  $busca = isset($_GET['busca']) ? $_GET['busca'] : '';
  $tarmscGetList = preg_replace("/__site_url__/",site_url() , $tarmscGetList);
  $target_det = preg_replace("/__site_url__/",site_url() , $target_det);

  if(is_numeric($busca)){
    echo '<script type="text/javascript">';
    echo '    window.location.href = "'.$target_det.'?cod='.$busca.'";';
    // echo "alert('".$target_det."')";
    echo '</script>';
    exit;
  }else{
    echo '<script type="text/javascript">';
    echo '    window.location.href = "'.$tarmscGetList.'?busca='.$busca.'";';
    echo '</script>';
    exit;
  }

}
add_shortcode("scm_busca_redir", "scm_busca_redir");

function scm_busca_redir_v2($atts, $content = null) {
  extract(shortcode_atts(array(
    "tarmscGetList" => '../listagem/',
    "target_det" => '../view/'
  ), $atts));

  $busca = isset($_GET['busca']) ? $_GET['busca'] : '';
  $tarmscGetList = preg_replace("/__site_url__/",site_url() , $tarmscGetList);
  $target_det = preg_replace("/__site_url__/",site_url() , $target_det);

  // $target =
  if(is_numeric($busca)){
    $target = $target_det;
    // echo '<script type="text/javascript">';
    // echo '    window.location.href = "'.$target_det.'?cod='.$busca.'";';
    // echo '</script>';
    // exit;
  }else{
    $target = $tarmscGetList;
    // echo '<script type="text/javascript">';
    // echo '    window.location.href = "'.$tarmscGetList.'?busca='.$busca.'";';
    // echo '</script>';
    // exit;
  }
  $target = preg_replace("/__busca__/",$busca , $target);
  $target = html_entity_decode($target);
  // echo $target;
  echo '<script type="text/javascript">';
  echo '    window.location.href = "'.$target.'";';
  echo '</script>';
  exit;

}
add_shortcode("scm_busca_redir_v2", "scm_busca_redir_v2");


function scm_buscando($atts, $content = null) {
  //EH UM CLONE DA FUNCAO DE CIMA (scm_busca_redir) PARA MANTER A COMPATIBILIDADE
  extract(shortcode_atts(array(
    // "tarmscGetList" => '../listagem/',
    // "target_det" => '../view/'
    "tarmscGetList" => './',
    "target_det" => './'

  ), $atts));

  $busca = isset($_GET['busca']) ? $_GET['busca'] : '';

  $tarmscGetList = html_entity_decode($target_det);
  $target_det = html_entity_decode($target_det);

  $target_det = preg_replace("/__cod__/", (isset($_GET['cod']) ? $_GET['cod'] : 0) , $target_det);

  if(is_numeric($busca)){
    echo '<script type="text/javascript">';
    // echo '    window.location.href = "'.$target_det.'?cod='.$busca.'";';
    echo '    window.location.href = "'.$target_det.'?op=view&cod='.$busca.'";';
    // echo '    window.location.href = "'.$target_det.'";';
    echo '</script>';
    exit;
  }else{
    echo '<script type="text/javascript">';
    echo '    window.location.href = "'.$tarmscGetList.'?busca='.$busca.'";';
    echo '</script>';
    exit;
  }

}
add_shortcode("scm_buscando", "scm_buscando");









function scm_delete($atts, $content = null) {
  extract(shortcode_atts(array(
    "md" => '0',
    "cod" => '0',
    "target_pos_delete" => '?',
    "on_op" => '',
    "access" => '',
    "role" => ''
  ), $atts));

  if($access){if(!scmIsAccess($access)) return '';}
  if($role){if(!scmIsRole($role)) return '';}

  $get_url_if_op = isset($_GET['op']) ? $_GET['op'] : '';
  if($on_op) {
    if($on_op=="empty"){
      if($get_url_if_op) return '';
    }else{
     if(!$get_url_if_op)  return '';
     if($get_url_if_op<>$on_op) return '';
    }
  }

  

  // if($access){
  //   $ret = '';
  //   if(is_super_admin()) {
  //     $ret = 'true';
  //   } else {
  //     $ret = get_user_meta( get_current_user_id(), $access, true );
  //   }
  //   if(!$ret) return '';
  // }

 

  $target_pos_delete = preg_replace("/__cod__/", (isset($_GET['cod']) ? $_GET['cod'] : 0) , $target_pos_delete);
  $target_pos_delete = preg_replace("/__pai__/", (isset($_GET['pai']) ? $_GET['pai'] : 0) , $target_pos_delete);


  $cod = preg_replace("/__cod__/", (isset($_GET['cod']) ? $_GET['cod'] : 0) , $cod);
  $md = preg_replace("/__md__/", (isset($_GET['md']) ? $_GET['md'] : 0) , $md);

  $delete = scmMdDelete($md,$cod);


  $ret = '';
  $ret = "";
  $ret .= '';
  if($target_pos_delete){
    echo '<script type="text/javascript">';
    echo '    window.location.href = "'.html_entity_decode($target_pos_delete).'";';
    // echo  'window.location.href = "../md-detalhe/?md=1030&cod=511"';
    echo '</script>';
  }
  return $ret.'---=---';
}
add_shortcode("scm_delete", "scm_delete");




function scm_deletar($atts, $content = null) {
  //if ( !is_user_logged_in() ) exit;
  extract(shortcode_atts(array(
    "md" => '0',
    "cod" => '0',
    "target_delete" => '?op=delete&cod=__cod__',
    "on_op" => '',
    "access" => '',
    "role" => ''
  ), $atts));

  if($access){if(!scmIsAccess($access)) return '';}
  if($role){if(!scmIsRole($role)) return '';}

  $get_url_if_op = isset($_GET['op']) ? $_GET['op'] : '';
  if($on_op) {
    if($on_op=="empty"){
      if($get_url_if_op) return '';
    }else{
     if(!$get_url_if_op)  return '';
     if($get_url_if_op<>$on_op) return '';

    }
  }

  $cod = preg_replace("/__cod__/", (isset($_GET['cod']) ? $_GET['cod'] : 0) , $cod);
  $md = preg_replace("/__md__/", (isset($_GET['md']) ? $_GET['md'] : 0) , $md);

  $target_delete = preg_replace("/__cod__/", (isset($_GET['cod']) ? $_GET['cod'] : 0) , $target_delete);
  $target_delete = preg_replace("/__pai__/", (isset($_GET['pai']) ? $_GET['pai'] : 0) , $target_delete);

  $ret = "";
  // $ret .= "<h1 style='color:red;'>DELETAR</h1>";
  $ret .= "<h2 style='text-align:center;'>EXCLUSÃO DE REGISTRO</h2>";
  $ret .= do_shortcode('[scm_view md='.$md.' cod=__cod__]');
  $ret .= '<div style="text-align:center;">';
  $ret .= do_shortcode('[scm_botao label="CONFIRME A EXCLUSÃO DESTE REGISTRO" target="'.$target_delete.'" class="btn btn-danger"]');
  $ret .= '</div>';

  return $ret;



}
add_shortcode("scm_deletar", "scm_deletar");






function scm_insert($atts, $content = null) {
  //if ( !is_user_logged_in() ) exit;
  extract(shortcode_atts(array(
    "cnn" => '',
    "md" => '0',
    "cod" => '0',
    "target" => '',
    "target_pos_insert" => '?',
    "on_op" => '',
    "access" => '',
    "role" => '',
    "col_fix" => '',
    "insert_add" => '',
    "insert_add_user_meta" => '',
    "insert_add_option" => ''
  ), $atts));

  if($access){if(!scmIsAccess($access)) return '';}
  if($role){ if(!scmIsRole($role)) return '';}

  $get_url_if_op = isset($_GET['op']) ? $_GET['op'] : '';
  if($on_op) {
    if($on_op=="empty"){
      if($get_url_if_op) return '';
    }else{
     if(!$get_url_if_op)  return '';
     if($get_url_if_op<>$on_op) return '';

    }
  }

  $target_pos_insert = html_entity_decode($target_pos_insert);

  $md = preg_replace("/__md__/", (isset($_GET['md']) ? $_GET['md'] : 0) , $md);

  $target_pos_insert = preg_replace("/__cod__/", (isset($_GET['cod']) ? $_GET['cod'] : 0) , $target_pos_insert);
  $target_pos_insert = preg_replace("/__pai__/", (isset($_GET['pai']) ? $_GET['pai'] : 0) , $target_pos_insert);

  $ret = '';
  if(!$md) {$ret = "wpmsc insert - md não especificado";}
  // if(!$cod) {$ret = "wpmsc view - cod não especificado";}
  if($ret) {return $ret;exit;}

  // if(!scmMdInsert($md, $_POST)) {echo "ERRO AO INSERIR";exit;}

  $tmp_request = $_REQUEST;
  // $col_fixx = arra
  $fields = '';
  $values = '';

  if($col_fix){

    $col_fix_arr = explode(',', $col_fix);
    foreach ($col_fix_arr as $key => $value) {
      $t = explode('=', $value);
      $fields .= $t[0];
      $values .= $t[1];
      $values = preg_replace("/__user__/i",  get_current_user_id(), $values);
      $tmp_request[$fields] = $values;

    }
  }


  // print('<pre>');
  // print('col_fix: '.$col_fix);
  // // print_r($tmp_request);
  // echo '<br>';
  // echo '---';

  // echo '<br>';
  // echo 'fields:'.$fields;
  // echo '<br>';

  // print('</pre>');

  // print('<pre>');
  // print_r($tmp_request);
  // print('</pre>');
  // die();

  // echo "--insert_add: $insert_add--";
  // die();
  if(!scmMdInsert($md, $tmp_request, $cnn, $insert_add, $insert_add_user_meta, $insert_add_option )) {
    // echo "ERRO AO INSERIR";exit;
  }

  // echo $target_pos_insert;
  // exit;
  $ret = "";
  $ret .= '';
  if($target_pos_insert){
    echo '<script type="text/javascript">';
    echo '    window.location.href = "'.$target_pos_insert.'";';
    echo '</script>';
    exit;
  }
  return $ret;
}
add_shortcode("scm_insert", "scm_insert");




// pra ir pro wpmsc-crud - ini
function scm_botao($atts, $content = null) {
  extract(shortcode_atts(array(
    "md" => '0',
    "cod" => '0',
    "target" => '',
    "label" => '',
    "janela" => '',//blank
    "class" => '',
    "style" => '',
    "on_op" => '',
    "access" => '',
    "role" => ''

  ), $atts));

  if($access){if(!scmIsAccess($access)) return '';}
  if($role){if(!scmIsRole($role)) return '';}

  $get_url_if_op = isset($_GET['op']) ? $_GET['op'] : '';
  if($on_op) {
    if($on_op=="empty"){
      if($get_url_if_op) return '';
    }else{
      if(!$get_url_if_op)  return '';
      if($get_url_if_op<>$on_op) return '';
    }
  }

  $target = preg_replace("/__md__/", (isset($_GET['md']) ? $_GET['md'] : 0) , $target);
  $target = preg_replace("/__cod__/", (isset($_GET['cod']) ? $_GET['cod'] : 0) , $target);
  $target = preg_replace("/__qs__/",$_SERVER['REQUEST_URI'] , $target);
  $target = preg_replace("/__site_url__/",site_url() , $target);
  $target = preg_replace("/__pai__/", (isset($_GET['pai']) ? $_GET['pai'] : 0) , $target);
  $target = preg_replace("/__hoje__/", date('d/m/Y') , $target);
  

  $to_janela = '';
  if($janela) $to_janela = 'target="'.$janela.'"';
  return '<a style="'.$style.'" class=" '.$class.'" href="'.$target.'" '.$to_janela.' >'.$label.'</a>'.$content;

}
add_shortcode("scm_botao", "scm_botao");



function scm_crud($atts, $content = null) {
  extract(shortcode_atts(array(
    "md" => '',
    "op" => '',
    "cod" => '__cod__',
    "pai" => '__pai__',
    "default_op" => 'ilist',
    'title_nnew' => '',
    "access_nnew" => '',
    "access" => '',
    "role" => '',
    "access_manager" => '',
    "target_insert" => '?op=insert',
    "target_pos_insert" => '?',

    "target_edit" => '?op=edit&cod=__cod__',
    "target_update" => '?op=update&cod=__cod__',
    "target_pos_update" => '?op=view&cod=__cod__',
    "target_pos_delete" => '?',
    "target_pos_duplique" => '?',
    "criterio" => '',
    "bar_top" => 1,
    "busca" => 1
  ), $atts));

  if($op=='') {
    $op = isset($_GET['op']) ? $_GET['op'] : '';
    if($op=='') $op=$default_op;
  }

  if($access){if(!scmIsAccess($access)) return '';}
  if($role){ if(!scmIsRole($role)) return '';}


  // if($op=='__op__') {
  if(!$md) return '--nada--';

  $md   = preg_replace("/__md__/", (isset($_GET['md']) ? $_GET['md'] : 0) , $md);
  $op   = preg_replace("/__op__/", (isset($_GET['op']) ? $_GET['op'] : $default_op) , $op);
  $cod  = preg_replace("/__cod__/", (isset($_GET['cod']) ? $_GET['cod'] : 0) , $cod);
  $cod  = preg_replace("/__ucod__/", (isset($_GET['ucod']) ? $_GET['ucod'] : 0) , $cod);
  $pai  = preg_replace("/__pai__/", (isset($_GET['pai']) ? $_GET['pai'] : 0) , $pai);


  if($access_manager){
    $se = 0;
    if(($op=='edit') || ($op=='update') || ($op=='novo') || ($op=='nnew') || ($op=='insert') || ($op=='delete') || ($op=='deletar')  || ($op=='duplicar')){
      $se = 1;
    }
    if($se){
        if(!scmIsAccess($access_manager)) return '';
    }
  }

  $uur = '';
  if($cod) $uur .= '&cod='.$cod;
  if($pai) $uur .= '&pai='.$pai;

  $ret = "";
  $url = $_SERVER["REDIRECT_URL"];
  $add_class = "wpmsc";
  if(substr($url,1,6)=='xxxwpmsc') {
    $add_class = "wpmsc_ajax";
    $ret .= '
    <script type="text/javascript">
      jQuery(function(){
        jQuery(".wpmsc_ajax").on("click",function(e){
          e.preventDefault();
          // alert(jQuery(this).attr("href"));
          jQuery( "#aba_ctu" ).load( jQuery(this).attr("href"));
        });
      });
    </script>
    ';
  };


  if($bar_top==1){
    $ret .= '<div style="text-align:center">';
    // $ret .= '--'.$_SERVER["REDIRECT_URL"].'-';


    // phpinfo();
    // die();
    $ret .= do_shortcode('[scm_botao class="btn '.$add_class.'" label="RELOAD"      target="" ]');
    $ret .= do_shortcode('[scm_botao class="btn '.$add_class.'" label="LISTAGEM"    target="'.$url.'?" ]');
    $ret .= do_shortcode('[scm_botao class="btn '.$add_class.'" label="NOVO"        target="'.$url.'?op=novo&'.$criterio.'"]');
    $ret .= do_shortcode('[scm_botao class="btn '.$add_class.'" label="EDIT"        target="'.$url.'?op=edit&cod=__cod__&pai=__pai__" on_op="view" ]');
    $ret .= do_shortcode('[scm_botao class="btn '.$add_class.'" label="DELETAR"     target="'.$url.'?op=deletar&cod=__cod__" on_op="view" access=""]');
    $ret .= do_shortcode('[scm_botao class="btn '.$add_class.'" label="DUPLICAR"    target="'.$url.'?op=duplicar&cod=__cod__" on_op="view" access=""]');
    $ret .= '</div>';
  }//class="btn-link '.$add_class.'"

  if($busca==1){
    $ret .= '<div style="text-align:center;">';
    $ret .= do_shortcode( '[scm_busca]' );
    $ret .= '</div>';

    $ret .= '<div style="min-height: 1em;"></div>';
  }

  if($op=='ilist')    $ret .= do_shortcode( '[scm_list md='.$md.' access_manager="'.$access_manager.'" criterio="'.$criterio.'"]' );
  if($op=='novo')     $ret .= do_shortcode( '[scm_nnew md='.$md.' target_insert="'.$target_insert.'" title="'.$title_nnew.'" access="'.$access_nnew.'" access_manager="'.$access_manager.'" target_pos_insert="'.$target_pos_insert.'"] ' );
  if($op=='insert')   $ret .= do_shortcode( '[scm_insert md='.$md.' target_pos_insert="'.$target_pos_insert.$uur.'" access_manager="'.$access_manager.'" ]' );
  if($op=='edit')     $ret .= do_shortcode( '[scm_edit md='.$md.' cod='.$cod.' target_update="'.$target_update.'" access_manager="'.$access_manager.'" ]' );
  if($op=='update')   $ret .= do_shortcode( '[scm_update md='.$md.' cod='.$cod.' target_pos_update="'.$target_pos_update.'" access_manager="'.$access_manager.'" ]' );
  if($op=='view')     $ret .= do_shortcode( '[scm_view md='.$md.' cod='.$cod.' access_manager="'.$access_manager.'" ]' );
  if($op=='det')      $ret .= do_shortcode( '[scm_detalhe md='.$md.' cod='.$cod.' access_manager="'.$access_manager.'" ]' );
  if($op=='delete')   $ret .= do_shortcode( '[scm_delete md='.$md.' cod='.$cod.' target_pos_delete="'.$target_pos_delete.'" access_manager="'.$access_manager.'" ] ' );
  if($op=='duplicar') $ret .= do_shortcode( '[scm_duplique md='.$md.' cod='.$cod.' target_update="'.$target_update.'" access_manager="'.$access_manager.'" ]' );
  // if($op=='duplicar') $ret .= do_shortcode( '[scm_edit md='.$md.' cod='.$cod.' target_pos_duplique="'.$target_pos_duplique.'" access_manager="'.$access_manager.'" ] ' );

  if($op=='deletar') {
    $ret = "<h1 style='color:red;'>DELETAR</h1>";
    $ret .= "<h2 style='color:red;'>SOLICITAÇÃO DE EXCLUSÃO DE REGISTRO</h2>";
    $ret .= do_shortcode('[scm_view md='.$md.' cod=__cod__]');
    $ret .= do_shortcode('[scm_botao label="CONFIRMAR EXCLUSÃO" target="?op=delete&cod=__cod__" class="btn btn-danger"]');


    //return do_shortcode( '[scm_deletar md='.$md.' cod='.$cod.' target_pos_delete="?" ] ' );
  }
  return $ret;
}
add_shortcode("scm_crud", "scm_crud");


function scmIsAccess($grupo){
  if(is_super_admin()) return true;
  $role = get_user_meta( get_current_user_id(), 'role', true );
  if($role){
    $grupos = explode(',', $grupo);
    foreach ($grupos as $key => $value) {
      if($value==$role) return true;
    }
  }
  //if($role==$grupo) return true;
  return false;
}
//add_action( 'wp', 'scmIsAccess' );

function scmIsRole($role){
  if(is_super_admin()) return true;
  $role = trim($role);
  $t = explode(",", $role);
  $ret = 0;
  foreach ($t as $key => $value) {
    if(current_user_can( trim($value) ))  $ret = 1;
  }
  return $ret;

  // if(current_user_can($role)) return true;
  // $user_id = get_current_user_id();
  // $user_meta=get_userdata($user_id); 
  // $user_roles=$user_meta->roles; 
  // if (in_array($role, $user_roles)) return true;
  // return false;

  /*
$os = array("Mac", "NT", "Irix", "Linux"); 
if (in_array("Irix", $os)) { 
    echo "Tem Irix";
}
if (in_array("mac", $os)) { 
    echo "Tem mac";
}
$user_roles=$user_meta->roles; //array of roles the user is part of.

$user_id = get_current_user_id();
$user_meta=get_userdata($user_id); 
$user_roles=$user_meta->roles; 
if (in_array("subscriber", $user_roles)){}
  */
}


add_action( 'wp_enqueue_scripts', 'scm_enqueue_scripts', 999 );
function scm_enqueue_scripts() {
  wp_enqueue_style( 'wpmsc-0000-css', plugins_url('/assets/css/style.css',__FILE__ ), '1.0.0' );
  wp_enqueue_script( 'wpmsc-0000-js', plugins_url( 'assets/js/js.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
  // wp_enqueue_style( 'wp-wpmsc-style', plugins_url( 'assets/css/global.css', __FILE__ ), array(), '1.0.0' );

  // wp_enqueue_style( 'wp-wpmsc-style', plugins_url( 'assets/css/style.css', __FILE__ ), array(), '1.0.0' );

  // wp_enqueue_script( 'wp-wpmsc-ext_all', plugins_url( 'assets/js/ext-all.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
  // wp_enqueue_script( 'wp-wpmsc-script', plugins_url( 'assets/js/function.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
  // wp_enqueue_script( 'wp-wpmsc-ext-lang', 'assets/js/ext-lang-pt_BR.js', array( 'jquery' ), '1.0.0', true );
  // wp_enqueue_script( 'wp-wpmsc-validate', plugins_url('assets/js/jquery.validate.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
}


//[scm_iframe url=”/wpmsc/8201/?pai=__pai__&i8201_pessoa=__pai__”]

function scm_iframe($atts, $content = null){
  extract(shortcode_atts(array(
    "on_op" => '',
    "access" => '',
    "role" => '',
    "url" => '',
    "id" => ''
  ), $atts));

  if($access){if(!scmIsAccess($access)) return '';}
  if($role){ if(!scmIsRole($role)) return '';}

  $ret = '';
  // $ret = '---'.$url.'---';
  $url = preg_replace("/__pai__/", (isset($_GET['pai']) ? $_GET['pai'] : 0) , $url);
  $url = preg_replace("/__qs__/", $_SERVER['REQUEST_URI'] , $url);
  

  $ret .= '';
  //style="width:100%;min-height:500px;border:solid 1px #0000; overflow-x:hidden; overflow-y:auto;"
  $ret .= '<iframe class="iiframe" style="overflow:hidden;width:100%;min-height:500px;" id="'.$id.'" src="'.$url.'"  ></iframe>';
  //scrolling="no"
  // $ret .= '<script>(function($) {var t = jQuery("iframe").contents().width();jQuery("'.$id.'").height(t);});</script>';
  // $ret .= '<script>(function($) { 
  //   var t = $("iframe").contents().width();
  //   alert(t);
  //   // console.log(\'t:\'+t); 
  // });</script>';
  ///wpmsc/8201/?pai=__pai__
  // $ret .= "---".$url."---";


  return $ret;
}
add_shortcode("scm_iframe", "scm_iframe");


//scm_text 

function scm_text($atts, $content = null){
  extract(shortcode_atts(array(
    "on_op" => '',
    "access" => '',
    "role" => '',
    "url" => '',
    "id" => ''
  ), $atts));
  // return $on_op;

  if($access){if(!scmIsAccess($access)) return '';}
  if($role){if(!scmIsRole($role)) return '';}
  

  $get_url_if_op = isset($_GET['op']) ? $_GET['op'] : '';
  if($on_op) {
    if($on_op=="empty"){
      if($get_url_if_op) return '';
    }else{
     if(!$get_url_if_op)  return '';
     if($get_url_if_op<>$on_op) return '';

    }
  }

  $ret = '';
  // $url = preg_replace("/__pai__/", (isset($_GET['pai']) ? $_GET['pai'] : 0) , $url);
  // $url = preg_replace("/__qs__/", $_SERVER['QUERY_STRING'] , $url);

  $ret .= '';
  $ret .= '';
  
  // echo $content;

  return $content;
}
add_shortcode("scm_text", "scm_text");


//---sys000200

function scm_display_posts($atts, $content = null){
  extract(shortcode_atts(array(
    "on_op" => '',
    "access" => '',
    "role" => '',

    "post_type" => '',
    "include_content" => '',
    "include_title" => '',
    "include_link" => '',
    "wrapper" => '',
    "author" => ''
  ), $atts));
  // return $on_op;


  // return do_shortcode('[display-posts post_type="userpanel" include_content="true" include_title="false" include_link="false" wrapper="div" author="__user__"]');
  


  // return;
// echo "---$author---";
  if($author){
    if(!is_user_logged_in()){
      return '';
    }
    $author = preg_replace("/__user__/i",  get_current_user_id(), $author);
    $author_obj = get_user_by('id', $author);
    $author = $author_obj->user_login;
  }

  if($access){if(!scmIsAccess($access)) return '';}
  if($role){if(!scmIsRole($role)) return '';}
  

  $get_url_if_op = isset($_GET['op']) ? $_GET['op'] : '';
  if($on_op) {
    if($on_op=="empty"){
      if($get_url_if_op) return '';
    }else{
     if(!$get_url_if_op)  return '';
     if($get_url_if_op<>$on_op) return '';

    }
  }
  return do_shortcode('[display-posts post_type="'.$post_type.'" include_content="'.$include_content.'" include_title="'.$include_title.'" include_link="'.$include_link.'" wrapper="'.$wrapper.'" author="'.$author.'"]');
}
add_shortcode("scm-display-posts", "scm_display_posts");



function scm_bloginfo_title($atts, $content = null){
  return '<a href="'.get_bloginfo('url').'">'.get_bloginfo('name').'</a>';
}
add_shortcode("scm_bloginfo_title", "scm_bloginfo_title");


function scm_bloginfo_description($atts, $content = null){
  return get_bloginfo('description');
}
add_shortcode("scm_bloginfo_description", "scm_bloginfo_description");


function scm_show_login($atts, $content = null){
    $user_id = get_current_user_id();
    if(!$user_id){
      return '<a href="'.get_bloginfo('url').'/login/">Login</a>';
    }
    $user_id = get_current_user_id();
    $diretorio = '';
    $usuario = get_user_meta( $user_id, "first_name", true ); 


    if(!$usuario) {
      // $usuario = get_user_meta( $user_id, "login", true ); 
      $user_obj = get_user_by('id', $user_id);
      $diretorio = get_user_meta( $user_id, "diretorio", true );
      $usuario = $user_obj->user_nicename;
    }
    // $user_obj = get_user_by('id', $user_id);
    // get_userdata
    // $user_obj = get_userdata('id', $user_id);
    // $user_obj = get_userdata($user_id);

    // echo '<pre>';
    // print_r($user_obj);
    // echo '</pre>';
    // $usuario = $user_obj->user_nicename;
    // return $usuario;
    // echo '<pre>';
    // $all_meta_for_user = get_user_meta( $user_id );
    // print_r( $all_meta_for_user );
    // echo '</pre>';


    return $diretorio.' <a href="/login/">'.$usuario.' </a>';
}
add_shortcode("scm_show_login", "scm_show_login");


function scm_create_fields($md) {
  // if ( !is_user_logged_in() ) return "";
  if(!$md) {echo 'md'; exit;}
  $md = $md;
  $tabela   = 'md'.$md;
  global $wpdb;
  if(!$md) die();
  $sql = "SHOW COLUMNS FROM ".SCM_WPDB_PREFIX.$tabela;
  $tb = scmDbExe($sql,'rows');
  $tabela_len = strlen($tabela);
  $sql_name = "";
  $sql_value = "";
  // echo '<pre>';
  $sql = "delete from ".SCM_WPDB_PREFIX."md000001 where md000001_modulo = ".$md.";\n";
  $campos = array();
  for ($i=0; $i < $tb['r']; $i++) {
    $tb['rows'][$i]['label']  = $tb['rows'][$i]['Field'];
    $tb['rows'][$i]['tam']  = 10;
    $tb['rows'][$i]['tipo']  = 'string';
    $ctr_new = 'textfield';
    $ctr_edit = 'textfield';
    if(substr($tb['rows'][$i]['Type'], 0, 7) == 'varchar'){
      $tb['rows'][$i]['tipo']  = 'string';
      $tb['rows'][$i]['tam']  = 50;
    }
    if(substr($tb['rows'][$i]['Type'], 0, 5) == 'float'){
      $tb['rows'][$i]['tipo']  = 'float';
      $tb['rows'][$i]['tam']  = 20;
    }
    if(substr($tb['rows'][$i]['Type'], 0, 4) == 'date'){
     $tb['rows'][$i]['tipo']  = 'date'; 
     $tb['rows'][$i]['tam']  = 20;
    }
    if(substr($tb['rows'][$i]['Type'], 0, 3) == 'int'){
     $tb['rows'][$i]['tipo']  = 'int';
     $tb['rows'][$i]['tam']  = 20;
    }
    if(substr($tb['rows'][$i]['Type'], 0, 4) == 'text'){
      $tb['rows'][$i]['tipo']  = 'blob';
      $tb['rows'][$i]['tam']  = 50;

      $ctr_new = 'textarea';
      $ctr_edit = 'textarea';

    }
    $sql .= "
      insert into ".SCM_WPDB_PREFIX."md000001 (
        md000001_tipo, 
        md000001_ctr_new,
        md000001_ctr_edit,
        md000001_ctr_view, 
        md000001_ctr_list, 

        md000001_modulo, 
        md000001_ordem, 
        md000001_xtype, 

        md000001_campo, 
        md000001_label, 
        md000001_ativo, 

        md000001_size, 
        md000001_black, 
        md000001_tabela
      
      ) values (

        '".$tb['rows'][$i]['tipo']."', 
        '".$ctr_new."', 
        '".$ctr_edit."', 
        'label', 
        'label', 

        ".$md.", 
        ".$i.", 
        'textfield', 

        '".$tb['rows'][$i]['Field']."',  
        '".substr($tb['rows'][$i]['Field'], 9) ."', 
        's', 

        ".$tb['rows'][$i]['tam'].", 
        1, 
        'md".$md."'
      );
    ";
    
  }
  // echo $sql;
  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  $mysqli->multi_query($sql);

}



  
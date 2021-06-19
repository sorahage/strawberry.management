<?php
require_once(dirname(__FILE__).'/_common_include.php');		// 設定情報読込
require_once(dirname(__FILE__).'/_count_team_score.php');	// チーム集計

// POSTデータセット
$mode1=$_POST['mode1'];//null>input url | 1>confirm | 2>correct | 3>registration
$mode2=$_POST['mode2'];//null>input url | 1>confirm | 2>correct | 3>registration
$url =$_POST['url' ];
$task=$_POST['task'];

// 未開催タスクの検索
$sql  = 'SELECT no,name FROM task ORDER BY no ASC';
$result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
while ( $row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	$sql  = 'SELECT * FROM score WHERE task='.$row['no'];
	$result2 = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
	//echo $row['no'].'='.mysql_num_rows($result2).'<br>';
	if(!mysql_num_rows($result2)){$taskname[$row['no']]=$row['name'];}
}

$league=array('ナショナルリーグ','Ｎ２リーグ','チャレンジリーグ');
$league2=array('nl','n2','cl');
$task2=floor($task/200);

// ------------------------------------------------------------------ [ mode.0 入力 ]
if((!$url or !file_get_contents($url)) and !$mode1 and !$mode2){
	echo '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
	echo 'タスクリザルトの読込<br>';
	echo '読み込みデータのURL<br>';
	echo '<input type="text" name="url" id="url" size="50" maxlength="200" value="'.$url.'">';
	echo '<br><br>';

echo '<b>[[ データの選択 ]]</b><br>';
$root = dirname(__FILE__) . '/files/';
$result = list_files($root);

//echo'<font size="-1">';
foreach($result as $value){
	$dir=dirname(__FILE__).'/files/';
	$a= str_replace($dir,'',$value);
	echo '<a onClick="document.getElementById(\'url\').value =\''.$value.'\'">'.$a.'</a><br>';
}
echo'　クリックで成績データを選択できます</font><br>';
echo'<a href="file_upload.php">ファイルを登録</a><br><br>';

	echo '<br>';
	echo '<input type="checkbox" name="taskstop" value="1">';
	echo 'タスクストップの場合にチェックして下さい<br>';
	echo '<br>';
	echo '<input type="checkbox" name="gp" value="1">';
	echo 'グランプリ成立の場合にチェックして下さい<br>';
	echo '<br>';
	echo '読み込むデータの種類を選んでください';
	echo '';
	echo '<table><tr><td><input type="radio" name="compegps" value="1" checked><th>Rank</th><th>ID</th><th>Name</th><th>Nation</th><th>Glider</th><th>Sponsor</th><th>Start</th><th>Finish</th><th>Time</th><th>Speed</th><th>Distance</th><th>Spd P</th><th><font color=red>LO P</th><th><font color=red>Dst P</th><th>Score</th></tr></table>';
	echo '<table><tr><th><input type="radio" name="compegps" value="2"><th>Rank</th><th>ID</th><th>Name</th><th>Nation</th><th>Glider</th><th>Sponsor</th><th>Start</th><th>Finish</th><th>Time</th><th>Speed</th><th>Distance</th><th>Spd P</th><th><font color=red>Dep P</th><th><font color=red>Arr P</th><th><font color=red>Dst P</th><th>Score</th></tr></table>';
	echo '<table><tr><th><input type="radio" name="compegps" value="3"><th>CompeGPS形式</table>';
	echo '<br>';
	
	// 未開催タスク表示
	echo'登録するタスクを選択してください<br>';
	$league=array(1=>'PNL ',3=>'N2L ',5=>'PCL ');
	echo'<select name="task">';
	foreach($taskname as $key => $value){
		$key2=floor($key/100);
		echo '<option value="'.$key.'">'.$league[$key2].$value.'</option>';
	}
	echo'</select><br><br>';

	echo '<b>[PCL]</b><br>トップ選手のフライト距離が15km未満の場合→';
	echo '<input type="text" size="5" name="distance">km<br>';
	echo 'PCL参加選手数→';
	echo '<input type="text" size="5" name="sankasu">名（未入力の場合自動集計）<br><br>';

	//echo'<input type="hidden" name="mode" value="1" />';
	echo'<input type="reset" name="res" value="取消" />';
	echo'<input type="submit" name="mode1" value="読込" />';
	echo'<br><br><a href="del.php">成績の削除はこちらをクリック</a>';
	die;
}

// ------------------------------------------------------------------ [ mode.1 確認 ]
if($mode1){

	//解析モード
	$debug=0;

	// データ解析
	$a=file_get_contents($url) or die("URLが正しくありません".$url);

	//読み込みデータ表示
	if($debug){echo '[[ 読み込みデータ表示 <b>$a=file_get_contents($url)</b> ]]<br><br>$a='.$a.'<br><br>';}

	//先頭部分の未利用データを削除
	$str = explode( "Score</th></tr>", $a );
	if($_POST['compegps']==3){$str = explode( "Point", $a );}

	//利用部分データ表示
	if($debug){
		echo'[[ 利用部分データ表示 <b>$str = explode( "Score</th></tr>", $a )</b> ]]<br>$str=';
		print_r($str);
		echo '<br><br>';
	}

	//HTMLデータをCSVデータへ変換
	$str2 ='<table>'.$str[1];
	$csv=table_tag2csv($str2);

	//CSVデータ表示
	if($debug){
		echo '[[ <b>データ解析|HTML>>CSV</b> ]]<br>';
		echo $csv.'<br><br>';
	}

	if($_POST['compegps']==3){
		$csv=$a;
		$csv=str_replace(array("\r\n","\r","\n"), ',', $csv);//改行コードをカンマへ変換
		$csv=str_replace(',,', ',', $csv);//カンマの重複を解消
	}

	// 解析データ(CSV形式)をカンマで成形
	$str3 = explode( ",", $csv );//csvデータをカンマで分割


	//形式の読み込み
	if($_POST['compegps']==1){
		$t=array('Rank','ID','Name','Nation','Glider','Sponsor','Start','Finish','Time','Speed','Distance','Spd P','LO P','Dst P','Score');//csvデータの属性をセット
		$tt=14;//項目数セット
	}

/*	if($_POST['compegps']==1){
		$t=array('Rank','ID','Name','Nation','Glider','Sponsor','Start','Finish','Time','Speed','Distance','Spd P','Dst P','Score');//csvデータの属性をセット
		$tt=13;//項目数セット
	}
	*/
	if($_POST['compegps']==2){
		$t=array('Rank','ID','Name','Nation','Glider','Sponsor','Start','Finish','Time','Speed','Distance','Spd P','Dep P','Arr P','Dst P','Score');//csvデータの属性をセット
		$tt=15;//項目数セット
	}
	if($_POST['compegps']==3){
		$t=array('Rank','ID','Name','Region','SS','ES','Time','Km','P Km','P Dep.','P km/h','P Arr.','Score');
		$tt=1;
	}

	//タスクストップだった特別な場合の対応
	if($_POST['taskstop']){
		$t=array('Rank','ID','Name','Nation','Glider','Sponsor','Start','Finish','Time','Speed','Distance','Altitude','Adj Dist','Spd P','LO P','Dst P','Score');
		$tt=16;
	}

	if($debug){
		echo '<b>$str3</b><br>';
		print_r($str3);
		echo '<br><br><br>';
	}

	$f=0;//csvカウント関数リセット
	$g=0;//dataカウント関数リセット
	$pclpoint=array(100,90,82,75,70,65,61,57,53,50,47,44,41,38,35,32,29,26,23,21,19,17,15,13);//PCLポイントセット

	if($debug){
		print_r($str2);
		echo '<br>';
		print_r($title);
		echo 'データ解析|CSV>>データ成形<br>';
	}
	

	foreach($str3 as $value){
		if($debug){echo $t[$f].'='.$value.'<br>';}
		if($t[$f]=='Rank'	){
			if($value=='NYP'){break;}// NYP選手以降は登録しない
			//if($_POST['compegps']==3){ //compegpsデータの場合は順位データを基準にポイントセット
				// 1~24位の場合
				if($value<25){
					$data[$g][3]=$pclpoint[$value-1];
				// 25位以下の場合
				} else {
					$data[$g][3]=11;
				}
				//距離減算
				//if($_POST['distance']<>null and $_POST['distance']<15){
				//	$data[$g][3]=round($data[$g][3]*$_POST['distance']/15,0);
				//}
			//}
		}
		if($t[$f]=='ID'		){if($value>0){$data[$g][0]=$value;}else{break;}}
		if($t[$f]=='Name'	){$data[$g][1]=$value;}
		if($t[$f]=='Start'	){if(strpos($value,':')===false){$f+=3;}}
		if($t[$f]=='Score'	){
			//if($value==''){$g--;break;}//スコアがゼロの選手以降は登録しない
			$data[$g][2]=$value*1;
			// PCLの場合 ポイントに変換する
			//if($task2>1){
			// 1~24位の場合
			//if($g<24){
				//$data[$g][3]=$pclpoint[$g];
				//$data[$g][3]=$pclpoint[$g];
			// 25位以下の場合
			//} else {
				//$data[$g][3]=11;
			//}
			//}
			//$g++;
		}
		if($f<$tt){$f++;}else{$f=0;$g++;} // $f..列  $g..行

	}

	//$g--;

	//echo 'result';
	//print_r($data);
	//echo '<br><br><br>';

   // [PCLの場合]
   // オープン選手を分けてポイント集計する
   if($task>500){

	// 基準ポイントを表示
	echo '基準ポイント<table><tr>';
	for ($a=1; $a<25; $a++) {echo '<td align="center">'.$a.'位';}
	echo '<td align="center">25位以降<tr>';
	foreach($pclpoint as $value){echo '<td align="center">'.$value;}
	echo '<td align="center">11</table>';

	// 基準距離を表示
	$d=$_POST['distance'];
	echo '基準距離：';
	if($d==null){$distance=15;echo '15km以上でポイント減なし';}else{$distance=$d;echo $d.'/15km';}
	echo '<br>';

	// 参加選手数を表示
	echo '参加選手数：';
	$s=$_POST['sankasu'];
	if($s==null){$sankasu=$g;echo $g;}else{$sankasu=$s;echo $s;}echo '名';
	if($sankasu>6               ){echo'(7名以上 100％計上)';$sankaritu=1  ;}
	if($sankasu>3 and $sankasu<7){echo'(4～6名 70％計上)';	$sankaritu=0.7;}
	if($sankasu<4               ){echo'(3名以下 50％計上)';	$sankaritu=0.5;}
	echo '<br><br>';

	//PCLポイント書換
	for ($a=0; $a<$g; $a++){$data[$a][3]=round($data[$a][3]*$distance/15*$sankaritu,0);}

      for ($a=0; $a<$g; $a++) {
         //echo $data[$a][0].'=>'.$data[$a][2].'\n';
      }
      //foreach($data
      //array_multisort($data[0],SORT_NUMERIC,SORT_DESC);
   }

	// formで表示

	echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';

	$h=0;// 表示カウント
	$rank=1;// ランキング

	echo $league[$task2].' '.$taskname[$task].' 登録データ<br>';
	echo 'ゼッケンと得点に間違いがないか確認してください';
	//echo $task;
	
	//タイトル表示
	echo '<table><tr>';
	echo '<td align="center" bgcolor="lightgray"><b>Rank</b></td>';
	echo '<td align="center" bgcolor="lightgray"><b>Zeichen</b></td>';
	echo '<td align="center" bgcolor="lightgray"><b>Name</b></td>';
	echo '<td align="center" bgcolor="lightgray"><b>Score</b></td>';
	//PCLの場合にポイント欄を追加
	if($task>500){echo '<td align="center" bgcolor="lightgray"><b>Point</b></td>';}

	// グランプリの場合にグランプリ欄を追加
	if($_POST['gp']){
		echo '<td align="center" bgcolor="lightgray"><b>GPx1.1</b></td>';	
		echo '<td align="center" bgcolor="lightgray"><b>ScoreTotal</b></td>';
	}

	// データ表示開始
	while($h<$g){
		echo '<tr>';
		//$rank=$h+1;
		//if($h>0){
			if($task<500){
				if($data[$h-1][2]<>$data[$h][2])$rank=$h+1;
			} else {
				if($data[$h-1][3]<>$data[$h][3])$rank=$h+1;
			}
		//}
		echo '<td align="right">'.$rank; // Rank
		echo '<td align="right"><input type="text" name="no'.$h.'"    size="4"  maxlength="3" value="'.$data[$h][0].'" style="text-align: right; ">'; // Zeichen
		echo '<td align="left">'.$data[$h][1]; // Name
		// グランプリの場合
		if($_POST['gp']){
			echo '<td align="right">'.$data[$h][2]; // 読み取り得点を表示

			// PCLの場合
			if($task>500){
				$gpadd=floor($data[$h][3]*0.1); // グランプリポイント計算
				$gpscore=$data[$h][3]+$gpadd; //グランプリポイントを加算した得点を計算
				echo '<td align="right">'.$data[$h][3]; // PCLポイントを表示
				echo '<td align="right">'.$gpadd; // GP加算ポイントを表示
				echo '<td align="right"><input type="text" name="score'.$h.'" size="4"  maxlength="3" value="'.$gpscore.'" style="text-align: right; ">';
	
			// PCL以外
			} else {
				$gpadd=floor($data[$h][2]*0.1); // グランプリポイント計算
				$gpscore=$data[$h][2]+$gpadd; //グランプリポイントを加算した得点を計算
				echo '<td align="right">'.$gpadd;
				echo '<td align="right"><input type="text" name="score'.$h.'" size="4"  maxlength="3" value="'.$gpscore.'" style="text-align: right; ">';
			}
		
		// グラングリ以外の場合
		} else {
		
			// PCLの場合
			if($task>500){
				echo '<td align="right">'.$data[$h][2]; // 読み取り得点を表示
				echo '<td><input type="text" name="score'.$h.'" size="4"  maxlength="3" value="'.$data[$h][3].'" style="text-align: right; ">'; // PCLポイントを表示
			
			// PCL以外
			} else {
				echo '<td><input type="text" name="score'.$h.'" size="4"  maxlength="3" value="'.$data[$h][2].'" style="text-align: right; ">';// 読み取り得点を表示
			}
			
		}
		$h++;
	}
	echo '</table>';

	echo'<input type="hidden" name="url" value="'.$url.'" />';
	echo'<input type="hidden" name="task" value="'.$task,'" />';
	//echo'<input type="reset" name="res" value="取消" />';
	echo'<input type="submit" name="mode2" value="登録" />';
	echo'<br><br><a href="'.$_SERVER['PHP_SELF'].'">やり直す場合はこちらをクリック</a>';
	die;
}

// ------------------------------------------------------------------ [ mode.2 DB登録 ]
// データ登録後の処理
if($mode2){
	//echo'test ok';die;
	$i=0;
	while($_POST['no'.$i]<>""){
		$score[$_POST['no'.$i]]=$_POST['score'.$i];
		$i++;
	}

	foreach($score as $key => $value){
		$sql ='INSERT INTO score';
		$sql.='(no, Zeichen, task, score) ';
		$sql.='VALUES (NULL,'.$key.','.$task.','.$value.')';
		$result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
	}

	// url書込み
	$sql ='UPDATE task SET url="'.$_POST['url'];
	$sql.='" WHERE task.no='.$task.' LIMIT 1';
	$result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
	
	// チームデータ集計
	count_team_score($task);

	// 終了表示
	echo $league[$task2].'の成績が正常にデータベースへ反映されました<br>';
	echo'最新のランキング <a href="rank.php?league='.$league2[$task2];
	echo'">'.$league[$task2].'年間ランキング</a>';

	echo'<br><br>';
	echo'<a href="./rank.php">年間ランキング	</a>｜';
	echo'<a href="./add.php" >成績を追加		</a>｜';
	echo'<a href="./del.php" >成績を削除		</a>　';

	die;
}

// ------------------------------------------------------------------ [ サブルーチン ]

/* ================================
 * table_tag2csv
 *
 * @create  2010/04/09
 * @author  pentan
 * @url     http://pentan.info/
 *
 * Copyright (c) 2009 pentan.info All Rights Reserved.
 * 著作権表示部分の変更削除は禁止です
 * ================================
 */
function table_tag2csv($buff) {
  $buff = preg_replace("/>[\s]+</is","><",$buff);

  $buff = preg_replace("/^.*<table[^>]*>/Uis","",$buff);
  $buff = preg_replace("/<\/table>.*$/is","",$buff);

  $buff = preg_replace("/<([a-z]+) ([^>]+)>/i","<$1>",$buff);
  $buff = preg_replace("/<th>/i","<td>",$buff);
  $buff = preg_replace("/<\/th>/i","</td>",$buff);

  $buff = preg_replace("/<\/?[^(tr|td)<>]+>/i","",$buff);

  $buff = str_replace("\r\n","\n",$buff);
  $buff = preg_replace("/(\r|\n)/","\r\n",$buff);

  $csv = "";
  if(preg_match_all("/<tr>(.*)<\/tr>/iU",$buff,$trmatches)){
    foreach($trmatches[1] as $rows){
      if(preg_match_all("/<td>(.*)<\/td>/iU",$rows,$tdmatches)){
        for($i=0;$i<count($tdmatches[1]);$i++){
          if(strpos($tdmatches[1][$i],",")!==false || strpos($tdmatches[1][$i],'"')!==false || strpos($tdmatches[1][$i],"\r\n")!==false){
            $tdmatches[1][$i] = '"'.str_replace('"','""',$tdmatches[1][$i]).'"';
          }
          $tdmatches[1][$i] = htmlspecialchars_decode($tdmatches[1][$i]);
        }
        $csv .= implode(',',$tdmatches[1]);
      }
      //$csv .= "\r\n";
      $csv .= ",";
    }
  }
  return $csv;
}

function list_files($dir){
  $files = array();
  $list = scandir($dir);
  foreach($list as $file){
    if($file == '.' || $file == '..'){
      continue;
    } else if (is_file($dir . $file)){
      $files[] = $dir . $file;
    } else if( is_dir($dir . $file) ) {
      $files = array_merge($files, list_files($dir . $file . '/'));
    }
  }
  return $files;
}

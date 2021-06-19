<?php

require_once(dirname(__FILE__).'/../_init/_sql_init.php');

//科目読込
$sql  = 'SELECT no,name,bunrui FROM kamoku WHERE 1 ORDER BY no ASC';
$result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
while ( $row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	$kamoku[$row['no']]=$row['name'  ];
	$bunrui[$row['no']]=$row['bunrui'];
}

//部門読込
$sql  = 'SELECT no,name FROM bumon WHERE 1 ORDER BY no ASC';
$result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
while ( $row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	$bumon[$row['no']]=$row['name'  ];
}

/*
print_r($kamoku);
echo '<br><br>';
print_r($bumon);
echo '<br><br>';
*/

//更新期間を指定
//$d1=1501;
//$d2=1501;
$d1=$_GET['d1'];
$d2=$_GET['d2'];
if($d1==0 and $d2==0){$d1=1;echo'更新なし';}else{echo'残データ更新中 '.$d1.'-'.$d2.'<br>';}
//
//[[ 更新開始 ]]
//
//指定期間を繰り返し
$d=$d1;
while($d<=$d2){

	//更新月の残データを一旦削除
	$sql  = 'DELETE FROM zan WHERE date='.$d;
	$result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
	
	echo '<font color="blue">'.$d.'<br>';
	
	//科目順に更新
	foreach ($kamoku as $k_key => $k_value) {
		//echo '<font color="blue">'.$k_key.' ';

		//部門順に更新
		foreach ($bumon as $b_key => $b_value) {
			//echo '<font color="red">'.$b_key.' ';

			//更新月の開始残を読込
			if(($d-floor($d/100)*100)==1){$d0=$d-100+12;}else{$d0=$d-1;}//1月の場合は前年の13月残を使う
			$sql  = 'SELECT kingaku FROM zan WHERE';
			$sql .= '     bumon  ='.$b_key;
			$sql .= ' and date   ='.$d0;
			$sql .= ' and kamoku ='.$k_key;
			$result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			$kisyu=$row['kingaku'];

			//借方期中増減を計算
			$d0=$d;//x翌月にする
			$sql  = 'SELECT sum(kingaku) FROM shiwake WHERE kari ='.$k_key.' and bumon='.$b_key;
			$sql .= ' and date>'.$d0.'00 and date<'.$d0.'99';
			$result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			$kari=$row['sum(kingaku)'];

			//貸方期中増減を計算
			$sql  = 'SELECT sum(kingaku) FROM shiwake WHERE kashi ='.$k_key.' and bumon='.$b_key;
			$sql .= ' and date>'.$d0.'00 and date<'.$d0.'99';
			$result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			$kashi=$row['sum(kingaku)'];

			//残高を計算
			if($bunrui[$k_key]>1 and $bunrui[$k_key]<5){
				$zan=$kisyu-$kari+$kashi;
			}else{
				$zan=$kisyu+$kari-$kashi;
			}

			//残高を書込
			if($zan<>0){
				$sql  = 'INSERT INTO zan(bumon,date,kamoku,kingaku) ';
				$sql .= 'VALUES ('.$b_key.','.$d.','.$k_key.','.$zan.')';
				$result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
			}
		}
	}
	
	//翌月にする
	$d++;
	if(($d-floor($d/100)*100)>13){$d=$d+100-13;}

}
echo '<font color="black">更新完了';

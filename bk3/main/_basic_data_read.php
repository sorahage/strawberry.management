<?
// --------------------------------------------------------| 科目読込 |----------
$sql  = 'SELECT no,name,bunrui FROM kamoku WHERE 1 ORDER BY no ASC';
$prepare =  $dbh -> prepare($sql);
$prepare -> execute();
while ( $row = $prepare -> fetch() ) {
	$kamoku[$row['no']]=$row['name'  ];
	$bunrui[$row['no']]=$row['bunrui'];
}

// --------------------------------------------------------| 部門読込 |----------
$sql  = 'SELECT no,name FROM bumon WHERE 1 ORDER BY no ASC';
$prepare =  $dbh -> prepare($sql);
$prepare -> execute();
while ( $row = $prepare -> fetch() ) {
	$bumon[$row['no']]=$row['name'  ];
}
end($bumon);
$bumon_end_key=key($bumon);
reset($bumon);

// --------------------------------------------------------| 日付最小最大確認 |---
$sql  = 'SELECT MIN(date) as mindate , MAX(date) as maxdate FROM shiwake';
$prepare =  $dbh -> prepare($sql);
$prepare -> execute();
$data = $prepare -> fetch();
$mindate = substr($data[0],0,4);	// 仕訳データ 最初の年月 yymm
$maxdate = substr($data[1],0,4);	// 仕訳データ 最後の年月 yymm

// --------------------------------------------------------| データセット |-------
$m1=$_GET['m1'];	//開始期間 yymmdd
$m2=$_GET['m2'];	//終了期間 yymmdd
$k =$_GET['k' ];	//科目 xxx
$b1=$_GET['b1'];	//開始部門 xxxxx
$b2=$_GET['b2'];	//終了部門 xxxxx

// --------------------------------------------------------| 初期値セット |-------
if($m1==""   				   ){$m1=date('y').date('m').'00';} // Getの開始年月をセット
if($m2==""             ){$m2=date('y').date('m').'99';} // Getの終了年月をセット
if($m1< $mindate*100   ){$m1=$mindate*100;            } // Getの開始月が仕訳最初より小さかったら仕訳開始月とする
if($m2 >$maxdate*100+99){$m2=$maxdate*100+99;         } // Getの開始月が仕訳最後より大きかったら仕訳終了月とする
if($m1 >$maxdate*100   ){$m1=$maxdate*100;            } // イレギュラー制御
if($m2< $mindate*100+99){$m2=$mindate*100+99;         } // イレギュラー制御
if($k ==""             ){$k=0;                        } // 集計分類の初期値をセット
if($b1==""             ){$b1=0;                       } // 開始部門をセット
if($b2==""             ){$b2=$bumon_end_key;          } // 終了部門をセット

?>

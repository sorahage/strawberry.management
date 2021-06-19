<?//================================================================================
//行の書出しサブルーチン
function gyo($m1,$m2,$k,$b1,$b2,$kkey1,$kkey2){

	include'../_init/_sql_init.php';
	global $bumon,$bunrui;

	//部門繰返し
	foreach($bumon as $bkey => $bvalue){

		//部門表示確認
		if($bkey>=$b1 and $bkey<=$b2){

			//大分類選択の場合
			if($k==0){
				if($bkey==(floor($bkey/10000)*10000)){
					$bb1=$bkey;
					$bb2=$bkey+9999;
				}else{
					continue;
				}
			}

			//中分類選択の場合
			if($k==1){
				if($bkey==(floor($bkey/1000)*1000)){
					$bb1=$bkey;
					$bb2=$bkey+999;
				}else{
					continue;
				}
			}

			//小分類選択の場合
			if($k==2){
				if($bkey==(floor($bkey/100)*100)){
					$bb1=$bkey;
					$bb2=$bkey+99;
				}else{
					continue;
				}
			}

			//全分類選択の場合
			if($k==3){
				$bb1=$bkey;
				$bb2=$bkey;
			}

		}else{continue;}

		//借方集計
		$sql  = "SELECT sum(kingaku) FROM shiwake WHERE ";
		if($kkey1<500){
			// BS科目処理
			$sql .= "kari >='$kkey1' and kari <='$kkey2' and                date<'$m2' and bumon>='$bb1' and bumon<='$bb2'";
		} else {
			// PL科目、集計
      $sql .= "kari >='$kkey1' and kari <='$kkey2' and date>'$m1' and date<'$m2' and bumon>='$bb1' and bumon<='$bb2'";
		}
		$prepare =  $dbh -> prepare($sql);
		$prepare -> execute();
		$row = $prepare -> fetch();
		$kari=$row['sum(kingaku)'];

		//貸方集計
		$sql  = "SELECT sum(kingaku) FROM shiwake WHERE ";
		if($kkey1<500){
			// BS科目処理
      $sql .= "kashi >='$kkey1' and kashi <='$kkey2' and                date<'$m2' and bumon>='$bb1' and bumon<='$bb2'";
		} else {
			// PL科目、集計
			$sql .= "kashi >='$kkey1' and kashi <='$kkey2' and date>'$m1' and date<'$m2' and bumon>='$bb1' and bumon<='$bb2'";
		}
		$prepare =  $dbh -> prepare($sql);
		$prepare -> execute();
		$row = $prepare -> fetch();
		$kashi=$row['sum(kingaku)'];

		//表示金額の算出
		if($bunrui[$kkey1]>1 and $bunrui[$kkey1]<5){
			$gokei=$kashi-$kari;//貸方科目の場合
		}else{
			$gokei=$kari-$kashi;//借方科目の場合
		}

		//金額の書出し
		echo '<td align="right"';
    if($kkey1<>$kkey2){echo' style="background-color:lightgray;font-weight:bold;"';}//単独科目でなければ集計行なので太字にする
    echo '>';
		if($gokei and $b<>1){
			echo'<a href="motocho.htm?';
			echo "m1=$m1&m2=$m2&k=$kkey1&b1=$bb1&b2=$bb2";
			echo '" target="_blank">';
		}
		//if($kkey1<>$kkey2){echo'<b>';}//単独科目でなければ集計行なので太字にする
		echo number_format($gokei);
		if($gokei){echo'</a>';}
		$rkei[$bb1]=$gokei;
		$sogokei+=$gokei;
	}
  echo '<td align="right"';
  if($kkey1<>$kkey2){echo' style="background-color:lightgray;font-weight:bold;"';}//単独科目でなければ集計行なので太字にする
  echo '>';
	echo number_format($sogokei);
	return $rkei;
}
?>

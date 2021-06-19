<?
/////////////////////////////
// 選択用期間書出サブルーチン

function write_period($name,$sct,$day){
  global $mindate,$maxdate;
  $y  = floor($mindate/100);	      			  		// 開始年
  $m  = $mindate-(floor($mindate/100)*100); 		// 開始月
  $y2 = floor($maxdate/100);               			// 終了年
  $m2 = $maxdate-(floor($maxdate/100)*100);			// 終了月
  $w  = 1; 																			// 繰返しスイッチ
  $ty = 0;                                      // for optgroup 'title year'

  echo '<select name="'.$name.'" style="width: 60px" onchange="scng()">';

  while($w){
    if($y>$ty){echo'<optgroup label="20'.$y.'年">';$ty=$y;} //年タイトル表示
    $i=sprintf('%02d',$y).sprintf('%02d',$m).$day;
    echo'<option value="'.$i.'"';
    if($sct==$i){echo' selected';}
    echo'>'.substr_replace(floor($i/100),'-',2,0).'</option>';
    if($y==$y2 and $m==$m2){$w=0;}
    $m++; // 次の月にする
    if($m>12){$y++;$m=1;} // 12ヶ月書き終わったら次の年にする
  }

  echo '</select>';

}
////////////////////////////
?>

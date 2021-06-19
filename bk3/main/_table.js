//読み込み時の表示
window_load();

//ウィンドウサイズ変更時に更新
window.onresize = window_load;

//サイズの表示
function window_load() {
  sw = window.innerWidth;
  sh = window.innerHeight;
  $(function(){
    $('#header_h').css('width' ,sw-170);
    $('#data'    ).css('width' ,sw-170);
    $('#header_v').css('height',sh-60);
    $('#data'    ).css('height',sh-60);
  });
}

$("#fruits").change(function(){
	var fruit = $("#fruits option:selected").val();
       alert(fruit);
	});
